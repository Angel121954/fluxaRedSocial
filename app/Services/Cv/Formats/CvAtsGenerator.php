<?php

declare(strict_types=1);

namespace App\Services\Cv\Formats;

use App\Services\Cv\CvPdfGenerator;
use Illuminate\View\View;

class CvAtsGenerator
{
    public function __construct(
        protected CvPdfGenerator $pdfGenerator,
    ) {}

    public function render(array $data): View
    {
        return view('components.cv-templates.ats', $data);
    }

    public function generatePdf(array $data): string
    {
        $html = $this->render($data)->render();
        $fullHtml = $this->pdfGenerator->wrapHtml($html);

        return $this->pdfGenerator->generate($fullHtml);
    }
}
