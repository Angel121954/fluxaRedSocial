<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateCVSettingsRequest;
use App\Services\CVService;
use Illuminate\Support\Facades\Auth;

class CVSettingsController extends Controller
{
    public function __construct(
        protected CVService $cvService,
    ) {}

    public function edit()
    {
        $cvSettings = auth()->user()->profile->cv_settings ?? [
            'template' => 'classic',
            'show_photo' => true,
            'show_location' => true,
            'show_email' => true,
            'show_projects' => true,
            'show_experience' => true,
            'show_education' => true,
            'section_order' => ['experience', 'projects', 'education', 'skills'],
        ];

        return view('cv.cv', compact('cvSettings'));
    }

    public function update(UpdateCVSettingsRequest $request)
    {
        $profile = $request->user()->profile;
        $validated = $request->validated();

        $sectionOrder = $request->input('section_order', ['experience', 'projects', 'education', 'skills']);

        $cvSettings = [
            'template' => $validated['template'],
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
        $datos = $this->cvService->prepareCvData($user);

        $html = view('components.cv-template', $datos)->render();

        $fullHtml = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>* { margin:0; padding:0; box-sizing:border-box; } body { background:#f8fafc; }</style>
</head>
<body>' . $html . '</body>
</html>';

        $filename = 'cv-' . str($user->username)->slug() . '-' . now()->format('Ymd') . '.pdf';

        $pdf = $this->cvService->generatePdf($fullHtml);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
