<?php
namespace App\Services;
use thiagoalessio\TesseractOCR\TesseractOCR;
use PhpOffice\PhpWord\IOFactory       as PhpWordIO;
use PhpOffice\PhpWord\Element\Text    as WText;
use PhpOffice\PhpWord\Element\Table   as WTable;


class OcrService
{
    public function extractFromFile(string $absolutePath, string $originalName = ''): array
    {
        // Always use the physical file's extension — never guess from originalName.
        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        // Guard: if ext is still empty fall back to originalName
        if ($ext === '') {
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        }

        try {
            $text = match (true) {
                // ── Images: direct Tesseract OCR, NO conversion ──────────────
                in_array($ext, ['jpg', 'jpeg', 'png']) => $this->ocrImage($absolutePath),

                // ── PDF: detect text vs scanned inside extractPdfText() ──────
                $ext === 'pdf'                         => $this->extractPdfText($absolutePath),

                // ── Word documents: XML extraction ───────────────────────────
                in_array($ext, ['doc', 'docx'])        => $this->extractDocxText($absolutePath),

                // ── Anything else: skip silently ─────────────────────────────
                default                                => '',
            };

            log_message('debug', '[OcrService] RAW TEXT DUMP: ' . $text);
            $text        = $this->preprocessText($text);
            log_message('debug', '[OcrService] PREPROCESSED TEXT: ' . $text);
            $suggestions = $this->buildSuggestions($text, $originalName);

// Compute SHA-256 hash for duplicate detection
$fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

// Strip any invalid UTF-8 bytes — prevents CI4 setJSON() from
        // crashing on font-encoded PDFs that slip past garble detection.
        $safeText = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        return [
            'text'        => $safeText,
            'success'     => true,
            'suggestions' => $suggestions,
            'file_hash'   => $fileHash,
            // confidence: rough measure based on text length vs file size
            'ocr_confidence' => $this->estimateConfidence($safeText, $absolutePath),
        ];

        } catch (\Throwable $e) {
            log_message('error', '[OcrService] ' . $e->getMessage());
            return ['text' => '', 'success' => false, 'error' => $e->getMessage(),
                    'suggestions' => ['folder' => '', 'filename' => '']];
        }
    }

    private function ocrImage(string $path): string
{
    $ocr = new TesseractOCR($path);
    $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
    $ocr->lang('eng');
    return $ocr->run();
}

   // Extracts embedded text from a PDF — tries smalot/pdfparser first,
    // falls back to Tesseract OCR if the PDF is scanned or garbled.
    private function extractPdfText(string $pdfPath): string
    {
        // Step 1: Try pdftotext (fast path for text-based / born-digital PDFs)
        $text = $this->pdfToText($pdfPath);

        // Step 2: Determine whether the PDF is image-only.
        //
        // Strategy: count printable non-whitespace characters.
        //   • pdftotext on a scanned PDF returns only \f (form-feed) chars.
        //   • pdftotext on a text PDF returns actual words.
        //   • Threshold = 5 printable chars is safe even for very short docs
        //     (e.g. a single-page certificate with "Grade 12 Diploma" in the header).
        //
        // We do NOT strip all whitespace before counting — that inflates the
        // count for documents that are mostly blank/whitespace.
        // Count printable non-whitespace characters.
        // Threshold lowered to 3 so that very short single-field documents
        // (e.g. a stamp or one-line cert) are still treated as text PDFs.
        $printableCount = preg_match_all('/[^\s\f\r\n]/', $text);

        // Garble detection: measure ratio of real ASCII letters/digits
        // vs total printable chars. Legitimate pdftotext output is mostly
        // alphanumeric + punctuation. Garbled font-encoded text contains
        // a high ratio of symbols like * + , - . # @ < > { } etc.
         $realChars    = preg_match_all('/[a-zA-Z0-9]/', $text);
        $totalPrint   = max(1, $printableCount);
        $realRatio    = $realChars / $totalPrint;

        // Count how many printable chars are symbols (not letters/digits/space)
        // Font-encoded PDFs produce a LOT of symbols like !"#$%&'()*+,-./:;<=>?@
        $symbolChars  = preg_match_all('/[^a-zA-Z0-9\s\r\n\f]/', $text);
        $symbolRatio  = $symbolChars / $totalPrint;

        // Garbled if: fewer than 55% real alphanumeric chars OR more than 35%
        // are symbols. The old 40% threshold was too low — garbled font-encoded
        // PDFs contain mostly ASCII symbols that still pass as "printable".
        $isGarbled = $realRatio < 0.55 || $symbolRatio > 0.35;

        if ($printableCount < 3 || $isGarbled) {
            // Very few real characters → treat as scanned/image PDF, run OCR
            log_message('debug', '[OcrService] PDF has only ' . $printableCount
                . ' printable chars, real ratio=' . round($realRatio * 100) . '%'
                . ($isGarbled ? ' (GARBLED font encoding)' : '')
                . ' → running Tesseract OCR');
            $ocrText = $this->pdfToImageOcr($pdfPath);
            log_message('debug', '[OcrService] pdfToImageOcr returned ' . strlen($ocrText) . ' chars');
            log_message('debug', '[OcrService] Final text going to buildSuggestions: ' . substr($text, 0, 500));
            return $ocrText;
        }

        // We have real text — use it directly, no OCR needed
        log_message('debug', '[OcrService] PDF has ' . $printableCount
            . ' printable chars → using pdftotext result directly');
        log_message('debug', '[OcrService] Final text going to buildSuggestions: ' . substr($text, 0, 500));
        return $text;
    }


// Extracts embedded text from a text-based PDF using pdftotext (Poppler)
     /**
     * Extracts embedded text from a text-based PDF using smalot/pdfparser.
     * No shell_exec, no Poppler binary — pure PHP, Windows-safe.
     * Returns empty string on failure so the Tesseract fallback still triggers.
     */
    private function pdfToText(string $pdfPath): string
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($pdfPath);

            // ── Collect text page-by-page so page boundaries become newlines ──
            // pdfparser's getText() collapses all pages into one blob;
            // iterating pages preserves section breaks that the regex needs.
            $pages = $pdf->getPages();
            $parts = [];
            foreach ($pages as $page) {
                try {
                    $parts[] = $page->getText();
                } catch (\Throwable $pageErr) {
                    // Skip unreadable pages silently; let other pages succeed
                    log_message('warning', '[OcrService] pdfparser: skipped page — ' . $pageErr->getMessage());
                }
            }

            // Fall back to full-document getText() if no pages were collected
            $raw = $parts ? implode("\n\n", $parts) : $pdf->getText();

            // ── Normalise whitespace to match Tesseract-like output ───────────

            // 1. Convert form-feeds (PDF page breaks) to newlines
            $text = str_replace("\f", "\n", $raw);

            // 2. Convert Windows-style CRLF to LF
            $text = str_replace("\r\n", "\n", $text);
            $text = str_replace("\r",   "\n", $text);

            // 3. Insert a newline before known label words when they are run
            //    together with the previous content (e.g. "2023Student Name:Juan")
            //    Labels are sourced dynamically from the record_keywords DB table
            //    so no hardcoded list is needed here.
            $labelWords = $this->getDynamicLabels();
            foreach ($labelWords as $label) {
                // Insert \n before the label if preceded by a non-newline character
                $pattern = '/(?<!\n)(' . preg_quote($label, '/') . ')/iu';
                $text    = preg_replace($pattern, "\n$1", $text);
            }

            // 4. Replace multiple consecutive spaces/tabs with a single space
            //    (but preserve newlines)
            $text = preg_replace('/[ \t]+/', ' ', $text);

            // 5. Collapse 3+ consecutive blank lines to 2 (matching Tesseract
            //    paragraph spacing that buildSuggestions() expects)
            $text = preg_replace('/\n{3,}/', "\n\n", $text);

            // 6. Trim each individual line (removes leading/trailing spaces
            //    left by the PDF font encoding)
            $lines = array_map('trim', explode("\n", $text));
            $text  = implode("\n", $lines);

            // 7. Final overall trim
            $text = trim($text);

            log_message('debug', '[OcrService] smalot/pdfparser extracted '
                . strlen($text) . ' chars (normalised, ' . count($pages) . ' pages)');

            return $text ?: '';

        } catch (\Throwable $e) {
            log_message('error', '[OcrService] pdfparser failed: ' . $e->getMessage());
            return '';
        }
    }

// Converts each PDF page to a PNG image, then runs Tesseract OCR on each page
private function pdfToImageOcr(string $pdfPath): string
{
    $pdftoppm = 'C:\poppler-25.12.0\Library\bin\pdftoppm.exe';

    if (!file_exists($pdftoppm)) {
        log_message('error', '[OcrService] pdftoppm not found at: ' . $pdftoppm);
        return '';
    }

    // Use sys_get_temp_dir() but replace backslashes — pdftoppm needs forward slashes on Windows
    $tmpPrefix = str_replace('\\', '/', sys_get_temp_dir()) . '/ocr_pdf_' . uniqid();

    // -png = output as PNG, -r 300 = 300 DPI for good OCR accuracy, -l 1 = first page only (fast test)
     // Build command with correct quoting for Windows cmd.exe.
        // Executable and output prefix are wrapped in double-quotes.
        // pdfPath uses escapeshellarg() which on Windows also uses double-quotes.
        $cmd = '"' . $pdftoppm . '" -png -r 300 '
             . escapeshellarg($pdfPath) . ' '
             . '"' . $tmpPrefix . '"'
             . ' 2>&1';

    log_message('debug', '[OcrService] pdftoppm cmd: ' . $cmd);
    $output = @shell_exec($cmd);
    log_message('debug', '[OcrService] pdftoppm output: ' . ($output ?? '(null)'));

    // pdftoppm creates: tmpPrefix-1.png, tmpPrefix-01.png, or tmpPrefix-001.png
    // glob for all variations
    // Normalize to backslashes on Windows to avoid duplicate matches
    $globPrefix = str_replace('/', DIRECTORY_SEPARATOR, $tmpPrefix);
    $pageImages = glob($globPrefix . '-*.png') ?: [];

    log_message('debug', '[OcrService] pdftoppm images found: ' . count($pageImages));

    if (empty($pageImages)) {
        log_message('warning', '[OcrService] pdftoppm produced no images. cmd was: ' . $cmd);
        return '';
    }

    $fullText = '';
    sort($pageImages);
    foreach ($pageImages as $imagePath) {
        log_message('debug', '[OcrService] Running Tesseract on: ' . $imagePath);
        try {
            $pageText  = $this->ocrImage($imagePath);
            $fullText .= $pageText . "\n";
            log_message('debug', '[OcrService] Page OCR result length: ' . strlen($pageText));
        } catch (\Throwable $e) {
            log_message('warning', '[OcrService] OCR failed on page: ' . $e->getMessage());
        } finally {
            @unlink($imagePath);
        }
    }

    return trim($fullText);
}



    /**
     * Extracts plain text from a .docx file using PHPWord.
     *
     * PHPWord reads each paragraph and each table cell as a clean unit,
     * so the output is one field per line — the same format that
     * pdfparser and Tesseract produce. This means preprocessText() and
     * buildSuggestions() (which use getDynamicLabels(), RecordTypeModel,
     * detectDocumentType(), and getDocTypeLabel()) all work identically
     * for DOCX as they do for PDF and images. No special DOCX logic needed
     * anywhere else in the file.
     *
     * Reads in this order so the most useful text comes first:
     *   1. Headers  — document title / school name live here
     *   2. Body     — form fields: name, student ID, course, etc.
     *   3. Tables   — DepEd/TSU forms often put fields inside tables
     *   4. Footers  — signatories, dates
     *
     * Falls back to ZipArchive (the old method) if PHPWord fails.
     */
    private function extractDocxText(string $docxPath): string
    {
        try {
            $phpWord = PhpWordIO::load($docxPath);
            $lines   = [];

            foreach ($phpWord->getSections() as $section) {

                // 1. Headers
                foreach ($section->getHeaders() as $header) {
                    foreach ($header->getElements() as $el) {
                        $this->collectDocxLines($el, $lines);
                    }
                }

                // 2. Body (paragraphs + tables)
                foreach ($section->getElements() as $el) {
                    $this->collectDocxLines($el, $lines);
                }

                // 3. Footers
                foreach ($section->getFooters() as $footer) {
                    foreach ($footer->getElements() as $el) {
                        $this->collectDocxLines($el, $lines);
                    }
                }
            }

            $text = implode("\n", $lines);
            $text = preg_replace('/\n{3,}/', "\n\n", $text);
            $text = trim($text);

            log_message('debug', '[OcrService] PHPWord extracted ' . strlen($text) . ' chars from DOCX');

            if ($text === '') {
                log_message('warning', '[OcrService] PHPWord returned empty — trying ZipArchive fallback');
                return $this->extractDocxFallback($docxPath);
            }

            return $text;

        } catch (\Throwable $e) {
            log_message('error', '[OcrService] PHPWord failed: ' . $e->getMessage() . ' — trying ZipArchive fallback');
            return $this->extractDocxFallback($docxPath);
        }
    }

    /**
     * Walks a single PHPWord element and appends plain text to $lines.
     *
     * Rule: each paragraph = one line, each table cell = one line.
     * Text runs (bold, italic, etc.) within the same paragraph are
     * joined with a space so "Student Name:" and "AQUINO Wency" on
     * the same paragraph do not get merged without a separator.
     *
     * Uses getDynamicLabels() indirectly — the lines this method produces
     * feed into preprocessText(), which calls getDynamicLabels() to split
     * any labels that are still joined on the same line.
     */
    private function collectDocxLines($element, array &$lines): void
    {
        // Plain text run — append to the current line (same paragraph)
        if ($element instanceof WText) {
            $txt = $element->getText();
            if ($txt !== '' && $txt !== null) {
                if (empty($lines)) {
                    $lines[] = $txt;
                } else {
                    $lines[count($lines) - 1] .= ' ' . $txt;
                }
            }
            return;
        }

        // Table — each cell becomes its own line
        if ($element instanceof WTable) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    $lines[] = '';                          // new line per cell
                    foreach ($cell->getElements() as $child) {
                        $this->collectDocxLines($child, $lines);
                    }
                }
            }
            return;
        }

        // Everything else (Paragraph, TextRun, Title, ListItem, etc.)
        // Start a new line then recurse into child elements.
        if (method_exists($element, 'getElements')) {
            $lines[] = '';                                  // new line per paragraph
            foreach ($element->getElements() as $child) {
                $this->collectDocxLines($child, $lines);
            }
        }
    }

    /**
     * ZipArchive fallback — used when PHPWord cannot open the file.
     * This is the original extractDocxText() logic, kept as a safety net.
     */
    private function extractDocxFallback(string $docxPath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            log_message('warning', '[OcrService] ZipArchive fallback: could not open ' . $docxPath);
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) return '';

        $text = strip_tags(str_replace('</w:p>', "\n", $xml));
        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }


    /**
     * Pre-processes raw text from ANY source (pdfparser, Tesseract, DOCX)
     * into a consistent line-per-field format before regex extraction runs.
     *
     * Key transformations:
     *  1. Normalize line endings
     *  2. Break two-column Tesseract rows into separate lines
     *     e.g. "Fullname : AQUINO, Wency  Student No : 2019202031"
     *      →   "Fullname : AQUINO, Wency\nStudent No : 2019202031"
     *  3. Ensure known labels always start on their own line
     *  4. Collapse excess whitespace
     */
    private function preprocessText(string $text): string
    {
        // 1. Normalize line endings to \n
        $text = str_replace(["\r\n", "\r", "\f"], "\n", $text);

        // 2. Insert newline before known label words that appear mid-line
        //    This fixes Tesseract two-column output AND pdfparser join artifacts.
        //    Labels are sourced dynamically from the record_keywords DB table
        //    so no hardcoded list is needed here.
        $labels = $this->getDynamicLabels();
        foreach ($labels as $label) {
            // Break on 1+ spaces (not just 2+) so that Tesseract single-space
            // column joins are also split.  The (?<=\S) anchor ensures we never
            // insert a spurious newline at the very start of a line.
            $pattern = '/(?<=\S)(\s+)(' . preg_quote($label, '/') . '\s*[:\-])/i';
            $text    = preg_replace($pattern, "\n$2", $text);
        }

        // Extra pass: split the specific two-column pattern used by TSU/DepEd
        // university report forms, where Tesseract outputs:
        //   "Fullname : AQUINO, Wency Sampang  Student No : 2019202031"
        // Split at ANY known label keyword that immediately follows content
        // (even with just one space), as long as it is preceded by word chars.
        $text = preg_replace(
            '/(\w)\s+((?:Student\s*(?:No|ID|Number)|Gender|College|Program|Major|'
            . 'Year\s*Level|Academic\s*Year|LRN|ID\s*No|Retention\s*Status|'
            . 'Fullname|Full\s*Name|Student\s*Name)\s*[:\-])/i',
            "$1\n$2",
            $text
        );
        // 3. Collapse tabs and multiple spaces (but not newlines)
        $text = preg_replace('/[ \t]+/', ' ', $text);

        // 4. Trim each line
        $lines = array_map('trim', explode("\n", $text));
        $text  = implode("\n", $lines);

        // 5. Collapse 3+ blank lines to 2
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }


   private function buildSuggestions(string $text, string $originalName): array
{
    // Collapse repeated whitespace before working with the text so that
    // PDFs that use multiple spaces / tabs still yield usable content.
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/(\r\n|\r|\n){3,}/', "\n\n", $text);

    if (trim($text) === '') {
        log_message('debug', '[OcrService] buildSuggestions: empty text, no suggestions');
        return ['folder' => '', 'filename' => ''];
    }

    // Log full text in chunks to avoid CI4 log truncation
    $chunks = str_split($text, 300);
    foreach ($chunks as $i => $chunk) {
        log_message('debug', '[OcrService] buildSuggestions TEXT[' . $i . ']: ' . $chunk);
    }

    $studentName = '';
    $studentId   = '';
    $docType     = '';

    // ── Strategy 1: Labeled field with colon/dash ─────────────────────────────
    // Covers: "Fullname : AQUINO, Wency Sampang", "Full Name: Maria Santos",
    //         "Student Name: ...", "Name of Student: ..."
    // The lookahead uses \n OR a next known label so the capture terminates
    // cleanly whether preprocessText() split the lines or not.
    if (preg_match(
        '/(?:student\s*name|full\s*name|fullname|name\s*of\s*student)\s*[:\-]\s*'
        . '([A-Za-z][A-Za-z,\.\s]{2,60}?)'
        . '(?=\s*(?:\n|student\s*(?:no|id|number)|gender|college|program|major|'
        . 'year\s*level|academic\s*year|lrn|id\s*no|retention|$))/i',
        $text, $m
    )) {
        $studentName = trim($m[1]);
        log_message('debug', '[OcrService] Strategy 1 (label) matched: ' . $studentName);
    }

    // ── Strategy 1b: "Pupil:" or "Learner:" ONLY when used as a form label ────
    // Requires a colon — prevents matching "Grade Five pupil of Tupi..."
    elseif (preg_match(
        '/(?:pupil|learner)\s*:\s*([A-Z][a-zA-Z ,.]{2,50})/i',
        $text, $m
    )) {
        $studentName = trim($m[1]);
        log_message('debug', '[OcrService] Strategy 1b (pupil label) matched: ' . $studentName);
    }
    // NOTE: Strategy 1c (duplicate "full\s*name" pattern) has been REMOVED.
    // It was redundant with Strategy 1 and caused OCR text to fall through to it
    // after the lookahead in Strategy 1 failed on un-split Tesseract lines.
    // Strategy 1 now handles both "Fullname" and "Full Name" variants.


    // ── Strategy 2: Philippine cert — "certify that NAME is/was/a bonafide" ───
    // Handles: "This is to certify that JOSHUA DAVE B. AMINOLA is a bonafide..."
    elseif (preg_match(
        '/certif(?:y|ied|ication)?\s+that\s+([A-Z][A-Z\s,\.]{4,50}?)\s+(?:is\b|was\b|has\b|be\b|born\b|officially\b|a\s+bona)/i',
        $text, $m
    )) {
        $studentName = trim($m[1]);
        log_message('debug', '[OcrService] Strategy 2 (certify that) matched: ' . $studentName);
    }

    // ── Strategy 3: "awarded/issued/presented/conferred to NAME" ─────────────
    // Uses a lookahead to stop at non-name continuation words (upon, for, as, at…)
    elseif (preg_match(
        '/(?:awarded\s+to|presented\s+to|given\s+to|conferred\s+upon|issued\s+to)\s*[:\-]?\s*([A-Z][a-zA-Z,\.\s]{4,40}?)(?=\s+(?:upon\b|for\b|as\b|of\b|in\b|at\b|by\b|the\b|and\b|or\b|to\b|a\s+bona|this\b|that\b|who\b))/i',
        $text, $m
    )) {
        $studentName = trim($m[1]);
        log_message('debug', '[OcrService] Strategy 3 (awarded/issued to) matched: ' . $studentName);
    }

    // ── Strategy 4: ALL-CAPS name on its own line — skip known headers ─────────
    elseif (preg_match_all('/\n([A-Z][A-Z]+(?:\s+[A-Z][A-Z]*\.?){1,4})\n/m', $text, $allMatches)) {
        $skipWords = [
            'REPUBLIC', 'DEPARTMENT', 'UNIVERSITY', 'COLLEGE', 'SCHOOL',
            'REPORT', 'GRADES', 'PHILIPPINES', 'EDUCATION', 'CERTIFICATE',
            'DIVISION', 'DISTRICT', 'OFFICE', 'DEPED', 'STATE', 'CITY',
        ];
        foreach ($allMatches[1] as $candidate) {
            $isHeader = false;
            foreach ($skipWords as $skip) {
                if (stripos($candidate, $skip) !== false) {
                    $isHeader = true;
                    break;
                }
            }
            if (!$isHeader) {
                $studentName = trim($candidate);
                log_message('debug', '[OcrService] Strategy 4 (all-caps line) matched: ' . $studentName);
                break;
            }
        }
    }

    // ── Clean trailing noise words (articles, prepositions) ──────────────────
    $studentName = trim(preg_replace('/\s+(is|was|has|be|a|an|the|of|in|at|to)$/i', '', $studentName));

     // ── Student ID / LRN ──────────────────────────────────────────────────────
    // Covers: "Student No : 2019202031", "Student ID: ...", "LRN: ...", "ID No: ..."
    // Value is digits-only (Philippine IDs: 7–12 digits).
    // The \s*[:\-]\s* allows for spaces around the colon/dash.
    if (preg_match(
        '/(?:student\s*(?:no\.?|id|number)|id\s*no?\.?|lrn)\s*[:\-]\s*(\d{5,20})/i',
        $text, $m
    )) {
        $studentId = trim($m[1]);
        log_message('debug', '[OcrService] Student ID matched: ' . $studentId);
    }


    $docType      = $this->detectDocumentType($text);
    $docTypeLabel = $this->getDocTypeLabel($docType);

    // ── Format name for folder and filename ───────────────────────────────────
    // Convert ALL-CAPS names to Title Case: "JOSHUA DAVE B. AMINOLA" → "Joshua_Dave_B_Aminola"
    // Mixed-case names (Maria Santos) are kept as-is.
    $formattedName = $studentName;
    if ($studentName && strtoupper($studentName) === $studentName) {
        // All-caps: convert to title case, preserve single-letter initials (B.)
        $formattedName = implode(' ', array_map(function ($word) {
            return strlen($word) <= 2 ? rtrim($word, '.') : ucfirst(strtolower($word));
        }, preg_split('/\s+/', $studentName)));
    }

    // Sanitize name and ID for use in paths
    $safeName = $formattedName
        ? preg_replace('/\s+/', '_', trim(preg_replace('/[^\w\s]/', '', $formattedName)))
        : '';
    $safeId = preg_replace('/\D/', '', $studentId); // digits only

    // Folder = "StudentID_FormattedName" when both present
    //          "FormattedName" when only name
    //          "StudentID" when only ID
    //          docType key as last resort
    if ($safeName && $safeId) {
        $folder = $safeId . '_' . $safeName;
    } elseif ($safeName) {
        $folder = $safeName;
    } elseif ($safeId) {
        $folder = $safeId;
    } else {
        $folder = $docType ?: 'Unknown';
    }

    // Filename = StudentId_FormattedName_DocTypeLabel (each part omitted if empty)
    $parts    = array_filter([$safeId, $safeName, $docTypeLabel]);
    $filename = implode('_', $parts);

    log_message('debug', '[OcrService] final name=' . $formattedName . ' folder=' . $folder . ' filename=' . $filename);

    return [
    'folder'    => $folder,
    'filename'  => $filename,
    'doc_type'  => $docType,
    'name'      => $formattedName,   // ← add this so FileMetadataModel can use it
    ];

}

    /**
     * Returns all keywords from the DB as a flat array of strings,
     * used as the dynamic label list for line-break insertion.
     * Replaces all hardcoded label arrays — labels now come exclusively
     * from the record_keywords table managed via RecordTypes admin UI.
     *
     * Results are sorted longest-first so more specific phrases
     * (e.g. "Student Name") are matched before shorter ones ("Name").
     */
    private function getDynamicLabels(): array
    {
        $typeModel  = new \App\Models\RecordTypeModel();
        $keywordMap = $typeModel->getKeywordMap();

        // Flatten all keywords from all doc types into one unique list
        $allKeywords = [];
        foreach ($keywordMap as $phrases) {
            foreach ($phrases as $phrase) {
                $allKeywords[] = $phrase;
            }
        }

        // Deduplicate and sort longest-first (avoids partial-match shadowing)
        $allKeywords = array_unique($allKeywords);
        usort($allKeywords, fn($a, $b) => strlen($b) - strlen($a));

        return $allKeywords;
    }

    private function detectDocumentType(string $text): string
    {
        $typeModel  = new \App\Models\RecordTypeModel();
        $keywordMap = $typeModel->getKeywordMap();
        $t          = strtolower($text);

        $scores = [];

        foreach ($keywordMap as $keyName => $phrases) {
            $hits = 0;
            foreach ($phrases as $phrase) {
                if (str_contains($t, strtolower($phrase))) {
                    $hits++;
                }
            }
            if ($hits > 0) {
                $scores[$keyName] = $hits;
            }
        }

        if (empty($scores)) {
            return '';
        }

        // Return the doc type with the most keyword hits.
        // Ties are broken by the original sort_order (array insertion order).
        arsort($scores);
        return array_key_first($scores);
    }

    // Maps a detected doc type key to its standardized filename label suffix
    private function getDocTypeLabel(string $docType): string
{
    if ($docType === '') return '';
    $typeModel = new \App\Models\RecordTypeModel();
    $suffixes  = $typeModel->getSuffixMap();
    return $suffixes[$docType] ?? '';
}

/**
 * Rough OCR confidence 0-100 based on printable character ratio.
 * Not ML-based — just a practical sanity check.
 */
private function estimateConfidence(string $text, string $filePath): int
{
    $trimmed = trim($text);
    if (strlen($trimmed) < 10) return 0;

    $printable = preg_match_all('/[a-zA-Z0-9\s\.,\-:\/]/', $trimmed);
    $total     = strlen($trimmed);
    if ($total === 0) return 0;

    $ratio = ($printable / $total) * 100;
    return (int) min(100, max(0, $ratio));
}

    // Standard document type labels used in filename construction
}