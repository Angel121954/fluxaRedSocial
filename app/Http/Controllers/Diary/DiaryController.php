<?php

declare(strict_types=1);

namespace App\Http\Controllers\Diary;

use App\Http\Requests\StoreDiaryComment;
use App\Http\Requests\StoreDiaryResponse;
use App\Models\Diary;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseBookmark;
use App\Models\DiaryResponseComment;
use App\Models\DiaryResponseLike;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function index(): View
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        $diary = Diary::active()->withCount('responses')->firstOrFail();

        $sort = request('sort', 'top');

        $responses = DiaryResponse::where('diary_id', $diary->id)
            ->with('user')
            ->withCount(['likes', 'comments'])
            ->when($sort === 'top', fn($q) => $q->orderByDesc('likes_count'))
            ->when($sort !== 'top', fn($q) => $q->latest())
            ->paginate(10);

        $recentResponders = DiaryResponse::where('diary_id', $diary->id)
            ->with('user')
            ->latest()
            ->take(3)
            ->get()
            ->pluck('user');

        return view('diary.index', compact('diary', 'responses', 'recentResponders', 'profile'));
    }

    public function store(StoreDiaryResponse $request): JsonResponse
    {
        $diary = Diary::active()->firstOrFail();

        $response = DiaryResponse::updateOrCreate(
            [
                'diary_id' => $diary->id,
                'user_id' => $request->user()->id,
            ],
            [
                'content' => $request->content,
            ]
        );

        $response->load('user');
        $response->loadCount(['likes', 'comments']);

        $responsesCount = DiaryResponse::where('diary_id', $diary->id)->count();

        $html = view('diary.partials._response', ['response' => $response])->render();

        return response()->json([
            'html' => $html,
            'responses_count' => $responsesCount,
            'response' => $response,
        ]);
    }

    public function like(DiaryResponse $response): JsonResponse
    {
        $like = DiaryResponseLike::where('diary_response_id', $response->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
            $response->decrement('likes_count');
        } else {
            DiaryResponseLike::create([
                'diary_response_id' => $response->id,
                'user_id' => Auth::id(),
            ]);
            $response->increment('likes_count');
        }

        return response()->json([
            'liked' => $like === null,
            'likes_count' => $response->fresh()->likes_count,
        ]);
    }

    public function bookmark(DiaryResponse $response): JsonResponse
    {
        $bookmark = DiaryResponseBookmark::where('diary_response_id', $response->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($bookmark) {
            $bookmark->delete();
        } else {
            DiaryResponseBookmark::create([
                'diary_response_id' => $response->id,
                'user_id' => Auth::id(),
            ]);
        }

        return response()->json([
            'bookmarked' => $bookmark === null,
        ]);
    }

    public function comment(StoreDiaryComment $request, DiaryResponse $response): JsonResponse
    {
        $comment = DiaryResponseComment::create([
            'diary_response_id' => $response->id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        $response->increment('comments_count');

        $comment->load('user');

        $html = view('diary.partials._comment', ['comment' => $comment])->render();

        return response()->json([
            'html' => $html,
            'comments_count' => $response->fresh()->comments_count,
        ]);
    }

    public function destroy(DiaryResponse $response): JsonResponse
    {
        Gate::authorize('delete', $response);

        $response->delete();

        return response()->json(['success' => true]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $diary = Diary::active()->firstOrFail();
        $sort = $request->sort ?? 'top';

        $responses = DiaryResponse::where('diary_id', $diary->id)
            ->with('user')
            ->withCount(['likes', 'comments'])
            ->when($sort === 'top', fn($q) => $q->orderByDesc('likes_count'))
            ->when($sort !== 'top', fn($q) => $q->latest())
            ->paginate(10);

        $html = '';
        foreach ($responses as $response) {
            $html .= view('diary.partials._response', ['response' => $response])->render();
        }

        return response()->json([
            'html' => $html,
            'next_page_url' => $responses->nextPageUrl(),
        ]);
    }

    public function adminIndex(): View
    {
        $diaries = Diary::withCount('responses')->latest()->get();

        return view('admin.diary.index', compact('diaries'));
    }

    public function update(Request $request, Diary $diary): RedirectResponse
    {
        if ($diary->responses()->exists()) {
            return redirect()->route('admin.diary.index')
                ->with('error', 'No puedes editar la pregunta porque el diario ya tiene respuestas.');
        }

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'emoji' => 'nullable|string|max:10',
        ]);

        $diary->update($validated);

        return redirect()->route('admin.diary.index')
            ->with('success', 'Diario actualizado correctamente.');
    }

    public function close(Diary $diary): RedirectResponse
    {
        $diary->update(['status' => 'closed']);

        return redirect()->route('admin.diary.index')
            ->with('success', 'Diario cerrado correctamente.');
    }

    public function adminStore(Request $request): RedirectResponse
    {
        if (Diary::where('status', 'active')->exists()) {
            return redirect()->route('admin.diary.index')
                ->with('error', 'Ya hay un diario activo. Ciérralo antes de crear otro.');
        }

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'emoji' => 'nullable|string|max:10',
        ]);

        Diary::create($validated);

        return redirect()->route('admin.diary.index')
            ->with('success', 'Diario creado correctamente.');
    }
}
