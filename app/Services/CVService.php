<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Services\Cv\CvDataService;
use App\Services\Cv\CvPdfGenerator;
use App\Services\Cv\Formats\CvAtsGenerator;
use App\Services\Cv\Formats\CvJsonGenerator;
use Illuminate\Support\Collection;

class CVService
{
    public function __construct(
        protected CvDataService $cvData,
        protected CvPdfGenerator $pdfGenerator,
        protected CvAtsGenerator $atsGenerator,
        protected CvJsonGenerator $jsonGenerator,
    ) {}

    public function prepareCvData(User $user, ?array $cvSettings = null): array
    {
        return $this->cvData->prepare($user, $cvSettings);
    }

    public function generatePdf(string $html): string
    {
        return $this->pdfGenerator->generate($html);
    }

    public function wrapHtml(string $content): string
    {
        return $this->pdfGenerator->wrapHtml($content);
    }

    public function generateAtsPdf(User $user, ?array $cvSettings = null): string
    {
        $data = $this->prepareCvData($user, $cvSettings);

        return $this->atsGenerator->generatePdf($data);
    }

    public function generateJson(User $user, ?array $cvSettings = null): string
    {
        $data = $this->prepareCvData($user, $cvSettings);

        return $this->jsonGenerator->generateJsonString($user, $data);
    }

    public function generateJsonData(User $user, ?array $cvSettings = null): array
    {
        $data = $this->prepareCvData($user, $cvSettings);

        return $this->jsonGenerator->generate($user, $data);
    }

    public function urlToBase64(?string $url): ?string
    {
        return $this->cvData->urlToBase64($url);
    }

    public function generateQrCode(string $data): ?string
    {
        return $this->cvData->generateQrCode($data);
    }

    public function loadTechnologyIcons(User $user): Collection
    {
        return $this->cvData->loadTechnologyIcons($user);
    }
}
