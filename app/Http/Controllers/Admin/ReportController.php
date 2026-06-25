<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\DiaryReport;
use App\Models\ProblemReport;
use App\Models\ProjectReport;
use App\Models\UserReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $userReports = UserReport::with([
            'reporter' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
            'reported' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
        ])
            ->select('id', 'reporter_id', 'reported_id', 'reason', 'created_at')
            ->latest()
            ->get();

        $projectReports = ProjectReport::with([
            'user' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
            'project:id,title,user_id',
        ])
            ->select('id', 'user_id', 'project_id', 'reason', 'created_at')
            ->latest()
            ->get();

        $diaryReports = DiaryReport::with([
            'user' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
            'diaryResponse:id,diary_id,content,user_id',
        ])
            ->select('id', 'user_id', 'diary_response_id', 'reason', 'created_at')
            ->latest()
            ->get();

        $problemReports = ProblemReport::with([
            'user' => fn ($q) => $q->select('id', 'name', 'username')->with('profile'),
        ])
            ->select('id', 'user_id', 'type', 'message', 'created_at')
            ->latest()
            ->get();

        $contacts = Contact::select('id', 'name', 'email', 'message', 'readed', 'created_at')
            ->latest()
            ->get();

        $counts = [
            'user' => $userReports->count(),
            'project' => $projectReports->count(),
            'diary' => $diaryReports->count(),
            'problem' => $problemReports->count(),
            'contact' => $contacts->count(),
            'unread_contacts' => $contacts->where('readed', false)->count(),
        ];

        return view('admin.reports.index', compact(
            'userReports',
            'projectReports',
            'diaryReports',
            'problemReports',
            'contacts',
            'counts',
        ));
    }

    public function dismissUserReport(UserReport $userReport): RedirectResponse
    {
        $userReport->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Reporte de usuario descartado correctamente.');
    }

    public function dismissProjectReport(ProjectReport $projectReport): RedirectResponse
    {
        $projectReport->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Reporte de proyecto descartado correctamente.');
    }

    public function dismissDiaryReport(DiaryReport $diaryReport): RedirectResponse
    {
        $diaryReport->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Reporte de diario descartado correctamente.');
    }

    public function dismissProblemReport(ProblemReport $problemReport): RedirectResponse
    {
        $problemReport->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Reporte de problema descartado correctamente.');
    }

    public function markContactRead(Contact $contact): RedirectResponse
    {
        $contact->update(['readed' => true]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Mensaje de contacto marcado como leído.');
    }

    public function markContactUnread(Contact $contact): RedirectResponse
    {
        $contact->update(['readed' => false]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Mensaje de contacto marcado como no leído.');
    }
}
