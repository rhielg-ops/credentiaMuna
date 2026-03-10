<?php
namespace App\Services;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    public function extractFromFile(string $absolutePath, string $originalName = ''): array
    {
        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        try {
            $text = match (true) {
            in_array($ext, ['jpg','jpeg','png']) => $this->ocrImage($absolutePath),
            $ext === 'pdf'                       => $this->extractPdfText($absolutePath),
             in_array($ext, ['doc','docx'])       => $this->extractDocxText($absolutePath),
    default                              => '',
};
            return ['text' => $text, 'success' => true,
                    'suggestions' => $this->buildSuggestions($text, $originalName)];
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
    // Step 1: Try pdftotext first (fast, works on text-based PDFs)
    $text = $this->pdfToText($pdfPath);

    // Step 2: If no meaningful text found, fall through to image OCR
    // Use strlen < 10 instead of empty check — pdftotext returns \f (form feed)
    // character for image-only PDFs which passes trim() but has no real content
    if (strlen(trim($text)) < 10) {
        log_message('debug', '[OcrService] pdfToText returned no useful text, trying image OCR');
        $text = $this->pdfToImageOcr($pdfPath);
        log_message('debug', '[OcrService] pdfToImageOcr returned ' . strlen($text) . ' chars');
    }

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

    $tmpTxt = sys_get_temp_dir() . '/ocr_' . uniqid() . '.txt';
    $cmd    = sprintf('"%s" %s %s 2>&1', $pdftotext, escapeshellarg($pdfPath), escapeshellarg($tmpTxt));

    log_message('debug', '[OcrService] pdfToText cmd: ' . $cmd);
    $output = @shell_exec($cmd);
    log_message('debug', '[OcrService] pdfToText output: ' . ($output ?? '(null)'));

    if (!file_exists($tmpTxt)) {
        log_message('warning', '[OcrService] pdfToText: no output file created');
        return '';
    }

    $text = file_get_contents($tmpTxt);
    @unlink($tmpTxt);

   log_message('debug', '[OcrService] pdfToText extracted ' . strlen(trim($text)) . ' chars');

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
    $cmd = sprintf('"%s" -png -r 300 %s "%s" 2>&1',
        $pdftoppm,
        escapeshellarg($pdfPath),
        $tmpPrefix
    );

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

    return ['folder' => $folder, 'filename' => $filename, 'doc_type' => $docType];
}

    private function detectDocumentType(string $text): string
    {
        $keywords = [
            'transcript'          => ['transcript of records', 'tor', 'official transcript'],
            'enrollment'          => ['certificate of enrollment', 'enrollment form', 'certificate of registration', 'enrolled'],
            'clearance'           => ['clearance', 'cleared'],
            'good_moral'          => [
                'good moral',
                'certificate of good moral',
                'good moral character',
                'has not committed any misbehavior',   // ← your PDF has this exact phrase
                'has not violated any school rules',   // ← your PDF has this too
                'rules and regulations',               // ← common in Philippine good moral certs
                'conduct and behavior',
                'moral character', 'certify'
            ],
            'form_137'            => ['form 137', 'permanent record'],
            'form_138'            => ['form 138', 'report card'],
            'diploma'             => ['diploma', 'graduate', 'graduation'],
            'birth_certificate'   => ['birth certificate', 'certificate of live birth'],
            'honorable_dismissal' => ['honorable dismissal', 'transfer credential'],
        ];
        $t = strtolower($text);
        foreach ($keywords as $type => $phrases)
            foreach ($phrases as $phrase)
                if (str_contains($t, $phrase)) return $type;
        return '';
    }

    // Maps a detected doc type key to its standardized filename label suffix
    private function getDocTypeLabel(string $docType): string
    {
        return self::DOC_TYPE_LABELS[$docType] ?? '';
    }

    // Standard document type labels used in filename construction
    public const DOC_TYPE_LABELS = [
        'transcript'          => 'Transcript_Record',
        'enrollment'          => 'Enrollment_Certificate',
        'clearance'           => 'Clearance_Record',
        'good_moral'          => 'Good_Moral_Certificate',
        'form_137'            => 'Form137_Permanent_Record',
        'form_138'            => 'Form138_Report_Card',
        'diploma'             => 'Diploma_Record',
        'birth_certificate'   => 'Birth_Certificate',
        'honorable_dismissal' => 'Honorable_Dismissal',
    ];
}