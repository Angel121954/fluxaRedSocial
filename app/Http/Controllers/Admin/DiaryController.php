<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diary\UpdateDiaryRequest;
use App\Models\Diary;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function adminIndex(): View
    {
        $diaries = Diary::withCount('responses')->latest()->get();

        return view('admin.diary.index', compact('diaries'));
    }

    public function adminStore(UpdateDiaryRequest $request): RedirectResponse
    {
        if (Diary::where('status', 'active')->exists()) {
            return redirect()->route('admin.diary.index')
                ->with('error', 'Ya hay un diario activo. Ciérralo antes de crear otro.');
        }

        Diary::create($request->validated());

        return redirect()->route('admin.diary.index')
            ->with('success', 'Diario creado correctamente.');
    }

    public function update(UpdateDiaryRequest $request, Diary $diary): RedirectResponse
    {
        if ($diary->responses()->exists()) {
            return redirect()->route('admin.diary.index')
                ->with('error', 'No puedes editar la pregunta porque el diario ya tiene respuestas.');
        }

        $diary->update($request->validated());

        return redirect()->route('admin.diary.index')
            ->with('success', 'Diario actualizado correctamente.');
    }

    public function close(Diary $diary): RedirectResponse
    {
        $diary->update(['status' => 'closed']);

        return redirect()->route('admin.diary.index')
            ->with('success', 'Diario cerrado correctamente.');
    }
}
