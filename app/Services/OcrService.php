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

            $suggestions = $this->buildSuggestions($text, $originalName);

// Compute SHA-256 hash for duplicate detection
$fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

return [
    'text'        => $text,
    'success'     => true,
    'suggestions' => $suggestions,
    'file_hash'   => $fileHash,
    // confidence: rough measure based on text length vs file size
    'ocr_confidence' => $this->estimateConfidence($text, $absolutePath),
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

   // Extracts embedded text from a PDF using pdftotext (Poppler utility)
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
        $printableCount = preg_match_all('/[^\s\f\r\n]/', $text);

        if ($printableCount < 5) {
            // Very few real characters → treat as scanned/image PDF, run OCR
            log_message('debug', '[OcrService] PDF has only ' . $printableCount
                . ' printable chars → treating as image-only, running Tesseract OCR');
            $ocrText = $this->pdfToImageOcr($pdfPath);
            log_message('debug', '[OcrService] pdfToImageOcr returned ' . strlen($ocrText) . ' chars');
            return $ocrText;
        }

        // We have real text — use it directly, no OCR needed
        log_message('debug', '[OcrService] PDF has ' . $printableCount
            . ' printable chars → using pdftotext result directly');
        return $text;
    }


// Extracts embedded text from a text-based PDF using pdftotext (Poppler)
    private function pdfToText(string $pdfPath): string
    {
        $pdftotext = 'C:\poppler-25.12.0\Library\bin\pdftotext.exe';

        if (!file_exists($pdftotext)) {
            log_message('error', '[OcrService] pdftotext not found at: ' . $pdftotext);
            return '';
        }

        // FIX: Use WRITEPATH (no spaces, forward-slash safe) instead of sys_get_temp_dir().
        // sys_get_temp_dir() on Windows often returns a path with spaces
        // (e.g. C:\Users\John Doe\AppData\Local\Temp) which breaks cmd.exe even
        // when double-quoted, causing pdftotext to silently produce no output.
        $tmpDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'temp_uploads';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $tmpTxt = $tmpDir . DIRECTORY_SEPARATOR . 'ocr_' . uniqid() . '.txt';

        // Build the shell command. All three paths are wrapped in double-quotes
        // individually so spaces in any segment are handled correctly on cmd.exe.
        $cmd = '"' . $pdftotext . '" -layout '
             . '"' . str_replace('/', DIRECTORY_SEPARATOR, $pdfPath)  . '" '
             . '"' . $tmpTxt . '"'
             . ' 2>&1';

        log_message('debug', '[OcrService] pdfToText cmd: ' . $cmd);

        // Try shell_exec first; fall back to exec() if shell_exec is disabled.
        $output = null;
        if (function_exists('shell_exec')) {
            $output = @shell_exec($cmd);
        } else {
            @exec($cmd, $lines, $rc);
            $output = implode("\n", $lines);
        }
        log_message('debug', '[OcrService] pdfToText output: ' . ($output ?? '(null)'));

        if (!file_exists($tmpTxt)) {
            log_message('warning', '[OcrService] pdfToText: output file not created. cmd: ' . $cmd);
            return '';
        }

        $text = file_get_contents($tmpTxt);
        @unlink($tmpTxt);

        $charCount = strlen(trim($text));
        log_message('debug', '[OcrService] pdfToText extracted ' . $charCount . ' chars');

        return trim($text) ?: '';
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
        '/(?:student\s*name|full\s*name|name\s*of\s*student)\s*[:\-]\s*([A-Z][a-zA-Z ,.]{2,50})/i',
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

    // ── Strategy 4: ALL-CAPS name on its own line (diplomas, DepEd certs) ─────
    elseif (preg_match('/\n([A-Z][A-Z]+(?:\s+[A-Z][A-Z]*\.?){1,4})\n/m', $text, $m)) {
        $studentName = trim($m[1]);
        log_message('debug', '[OcrService] Strategy 4 (all-caps line) matched: ' . $studentName);
    }

    // ── Clean trailing noise words (articles, prepositions) ──────────────────
    $studentName = trim(preg_replace('/\s+(is|was|has|be|a|an|the|of|in|at|to)$/i', '', $studentName));

    // ── Student ID / LRN ──────────────────────────────────────────────────────
    if (preg_match('/(?:student\s*id|id\s*no?\.?|lrn)\s*[:\-]?\s*([A-Z0-9\-]{3,20})/i', $text, $m)) {
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