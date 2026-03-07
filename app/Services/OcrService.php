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
                default                           => '',
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

    //private function ocrPdf(string $pdfPath): string
    //{
        //if (!extension_loaded('imagick')) {
         //   throw new \RuntimeException('php-imagick required for PDF OCR.');
        //}
        //$imagick = new \Imagick();
        //$imagick->setResolution(300, 300);
        //$imagick->readImage($pdfPath . '[0]');
        //$imagick->setImageFormat('png');
        //$tmpPng = sys_get_temp_dir() . '/ocr_' . uniqid() . '.png';
        //$imagick->writeImage($tmpPng);
        //$imagick->clear(); $imagick->destroy();
        //try { $text = $this->ocrImage($tmpPng); }
        //finally { if (file_exists($tmpPng)) unlink($tmpPng); }
       // return $text;
   // }

    private function buildSuggestions(string $text, string $originalName): array
    {
        if (trim($text) === '') return ['folder' => '', 'filename' => ''];
        $studentName = ''; $studentId = ''; $schoolYear = '';
        $gradeLevel  = ''; $docType    = '';
        if (preg_match('/(?:student\s*name|full\s*name)\s*[:\-]?\s*([A-Z][a-zA-Z ,.]{2,50})/i',$text,$m))
            $studentName = trim($m[1]);
        if (preg_match('/(?:student\s*id|id\s*no?\.?)\s*[:\-]?\s*([A-Z0-9\-]{3,20})/i',$text,$m))
            $studentId = trim($m[1]);
        if (preg_match('/(?:school\s*year|s\.?y\.?)\s*[:\-]?\s*(\d{4}[-–]\d{2,4})/i',$text,$m))
            $schoolYear = preg_replace('/\s+/','',$m[1]);
        if (preg_match('/(?:grade|year\s*level)\s*[:\-]?\s*(\d{1,2})/i',$text,$m))
            $gradeLevel = 'Grade'.$m[1];
        $docType = $this->detectDocumentType($text);
        $folder   = implode('/', array_filter([$docType, $schoolYear, $gradeLevel]));
        $parts    = array_filter([$studentId, $studentName, $docType]);
        $filename = $parts ? preg_replace('/\s+/','_',implode('_',array_map(
                       fn($p)=>preg_replace('/[^\w\s\-]/','', $p), $parts))) : '';
        return ['folder' => $folder, 'filename' => $filename];
    }

    private function detectDocumentType(string $text): string
    {
        $keywords = [
            'transcript'          => ['transcript of records','tor','official transcript'],
            'enrollment'          => ['certificate of enrollment','enrollment form','enrolled'],
            'clearance'           => ['clearance','cleared'],
            'good_moral'          => ['good moral','certificate of good moral'],
            'form_137'            => ['form 137','permanent record'],
            'form_138'            => ['form 138','report card'],
            'diploma'             => ['diploma','graduate'],
            'birth_certificate'   => ['birth certificate'],
            'honorable_dismissal' => ['honorable dismissal','transfer credential'],
        ];
        $t = strtolower($text);
        foreach ($keywords as $type => $phrases)
            foreach ($phrases as $phrase)
                if (str_contains($t, $phrase)) return $type;
        return '';
    }
}
