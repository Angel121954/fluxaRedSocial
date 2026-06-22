<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateCVSettingsRequest;
use App\Services\CVService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CVSettingsController extends Controller
{
    public function __construct(
        protected CVService $cvService,
    ) {}

    public function edit()
    {
        $user = auth()->user()->loadCount([
            'workExperiences',
            'educations',
            'projects',
            'technologies',
        ]);

        $hasData = [
            'experience' => $user->work_experiences_count > 0,
            'projects' => $user->projects_count > 0,
            'education' => $user->educations_count > 0,
            'skills' => $user->technologies_count > 0,
        ];

        $availableSections = array_keys(array_filter($hasData));

        $cvSettings = $user->profile->cv_settings ?? [
            'format' => 'pdf',
            'show_photo' => true,
            'show_location' => true,
            'show_email' => true,
            'show_projects' => true,
            'show_experience' => true,
            'show_education' => true,
            'section_order' => $availableSections,
        ];

        $savedOrder = $cvSettings['section_order'] ?? $availableSections;
        $existing = array_intersect($savedOrder, $availableSections);
        $missing = array_diff($availableSections, $existing);
        $cvSettings['section_order'] = array_values(array_merge($existing, $missing));

        return view('cv.cv', compact('cvSettings', 'hasData'));
    }

    public function update(UpdateCVSettingsRequest $request)
    {
        $profile = $request->user()->profile;
        $validated = $request->validated();

        $sectionOrder = $request->input('section_order', ['experience', 'projects', 'education', 'skills']);

        $cvSettings = [
            'format' => $validated['format'] ?? 'pdf',
            'show_photo' => $request->boolean('show_photo'),
            'show_location' => $request->boolean('show_location'),
            'show_email' => $request->boolean('show_email'),
            'show_projects' => $request->boolean('show_projects'),
            'show_experience' => $request->boolean('show_experience'),
            'show_education' => $request->boolean('show_education'),
            'section_order' => $sectionOrder,
        ];

        $profile->cv_settings = $cvSettings;
        $profile->save();

        return redirect()
            ->back()
            ->with('success', 'Configuración del CV actualizada correctamente.');
    }

    public function restore()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $profile->cv_settings = null;
        $profile->save();

        return redirect()
            ->route('cv.edit')
            ->with('success', 'Configuración del CV restaurada correctamente.');
    }

    public function download()
    {
        $user = Auth::user();
        $settings = $user->profile->cv_settings;
        $format = $settings['format'] ?? 'pdf';

        try {
            return match ($format) {
                'ats' => $this->downloadAts($user),
                'json' => $this->downloadJson($user),
                default => $this->downloadPdf($user),
            };
        } catch (Throwable $e) {
            Log::error('Error al descargar CV', [
                'user_id' => $user->id,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('cv.edit')
                ->with('error', 'Ocurrió un error al generar tu CV. Intenta de nuevo.');
        }
    }

    public function downloadFormat(string $format)
    {
        $user = Auth::user();

        try {
            return match ($format) {
                'ats' => $this->downloadAts($user),
                'json' => $this->downloadJson($user),
                default => $this->downloadPdf($user),
            };
        } catch (Throwable $e) {
            Log::error('Error al descargar CV', [
                'user_id' => $user->id,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('cv.edit')
                ->with('error', 'Ocurrió un error al generar tu CV. Intenta de nuevo.');
        }
    }

    protected function downloadPdf(mixed $user)
    {
        $datos = $this->cvService->prepareCvData($user);
        $html = $this->cvService->wrapHtml(
            view('components.cv-template', $datos)->render()
        );

        $filename = 'cv-'.str($user->username)->slug().'-'.now()->format('Ymd').'.pdf';
        $pdf = $this->cvService->generatePdf($html);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function downloadAts(mixed $user)
    {
        $pdf = $this->cvService->generateAtsPdf($user);
        $filename = 'cv-ats-'.str($user->username)->slug().'-'.now()->format('Ymd').'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function downloadJson(mixed $user)
    {
        $json = $this->cvService->generateJson($user);
        $filename = 'cv-'.str($user->username)->slug().'-'.now()->format('Ymd').'.json';

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
