<?php
namespace App\Services;
use thiagoalessio\TesseractOCR\TesseractOCR;

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
            $text   = $pdf->getText();

            // Normalize form-feeds to newlines (same as old pdftotext behaviour)
            $text = str_replace("\f", "\n", $text);

            log_message('debug', '[OcrService] smalot/pdfparser extracted '
                . strlen(trim($text)) . ' chars');

            return trim($text) ?: '';

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



    // Extracts plain text from a .docx file by reading its XML content directly
    private function extractDocxText(string $docxPath): string
    {
        // .docx files are ZIP archives — word/document.xml contains the text
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            log_message('warning', '[OcrService] Could not open docx: ' . $docxPath);
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) return '';

        // Strip XML tags and decode entities to get plain text
        $text = strip_tags(str_replace('</w:p>', "\n", $xml));
        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
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
    // e.g. "Student Name: Juan dela Cruz"  /  "Full Name: ..."
    // NOTE: "pupil" and "learner" are intentionally NOT here — they appear as
    // body words ("a bonafide Grade Five pupil of...") and caused false matches.
    if (preg_match(
        // ✅ \s already covers newlines, but add \n explicitly to be safe
       '/(?:student\s*name|full\s*name|fullname|name\s*of\s*student)\s*[:\-]\s*\n?\s*([A-Z][a-zA-Z ,\.]{2,50})/i',
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

    // ── Strategy 1c: "Fullname : AQUINO, Wency" university report format ──────
    // Handles Last, First Middle names separated by a comma after the label.
    elseif (preg_match(
        '/full\s*name\s*[:\-]\s*([A-Z][A-Za-z\s,\.]{2,50})/i',
        $text, $m
    )) {
        $studentName = trim($m[1]);
        log_message('debug', '[OcrService] Strategy 1c (fullname colon) matched: ' . $studentName);
    }

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
    if (preg_match('/(?:student\s*(?:id|no\.?|number)|id\s*no?\.?|lrn)\s*[:\-]?\s*([A-Z0-9\-]{3,20})/i', $text, $m)) {
        $studentId = trim($m[1]);
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

    // Folder = formatted student name with underscores, stripping punctuation
    $folder = $formattedName
        ? preg_replace('/\s+/', '_', trim(preg_replace('/[^\w\s]/', '', $formattedName)))
        : $docType;

    // Filename = StudentId_FormattedName_DocTypeLabel  (StudentId omitted if empty)
    $namePart  = preg_replace('/\s+/', '_', preg_replace('/[^\w\s]/', '', $formattedName));
    $parts     = array_filter([$studentId, $namePart, $docTypeLabel]);
    $filename  = implode('_', $parts);

    log_message('debug', '[OcrService] final name=' . $formattedName . ' folder=' . $folder . ' filename=' . $filename);

    return [
    'folder'    => $folder,
    'filename'  => $filename,
    'doc_type'  => $docType,
    'name'      => $formattedName,   // ← add this so FileMetadataModel can use it
    ];

}

    private function detectDocumentType(string $text): string
{
    $typeModel  = new \App\Models\RecordTypeModel();
    $keywordMap = $typeModel->getKeywordMap();
    $t          = strtolower($text);

    foreach ($keywordMap as $keyName => $phrases) {
        foreach ($phrases as $phrase) {
            if (str_contains($t, strtolower($phrase))) {
                return $keyName;
            }
        }
    }
    return '';
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