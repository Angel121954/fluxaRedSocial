<?php

declare(strict_types=1);

namespace App\Http\Controllers\Diary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diary\StoreDiaryCommentRequest;
use App\Http\Requests\Diary\StoreDiaryReportRequest;
use App\Http\Requests\Diary\StoreDiaryResponseRequest;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseComment;
use App\Services\DiaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function __construct(
        protected DiaryService $diaryService,
    ) {}

    public function index(): View
    {
        $diary = $this->diaryService->getActiveDiary();

        if (! $diary) {
            return view('diary.index')->with('noDiary', true);
        }

        $sort = request('sort', 'top');
        $responses = $this->diaryService->getResponses($diary->id, Auth::id(), $sort);
        $recentResponders = $this->diaryService->getRecentResponders($diary->id);
        $userHasResponded = $this->diaryService->hasUserResponded($diary->id, Auth::id());

        return view('diary.index', compact('diary', 'responses', 'recentResponders', 'userHasResponded'));
    }

    public function store(StoreDiaryResponseRequest $request): JsonResponse
    {
        $diary = $this->diaryService->getActiveDiary();
        abort_if($diary === null, 404);

        $response = $this->diaryService->createOrUpdateResponse(
            $diary,
            $request->user()->id,
            $request->content,
        );

        $response->load([
            'user.profile',
            'likes' => fn ($q) => $q->where('user_id', Auth::id()),
            'bookmarks' => fn ($q) => $q->where('user_id', Auth::id()),
        ]);
        $response->loadCount(['likes', 'comments']);

        $html = view('diary.partials._response', ['response' => $response])->render();

        return response()->json([
            'html' => $html,
            'responses_count' => $this->diaryService->responsesCount($diary->id),
            'response' => $response,
        ]);
    }

    public function like(DiaryResponse $response): JsonResponse
    {
        return response()->json(
            $this->diaryService->toggleLike($response, Auth::id())
        );
    }

    public function bookmark(DiaryResponse $response): JsonResponse
    {
        return response()->json(
            $this->diaryService->toggleBookmark($response, Auth::id())
        );
    }

    public function comment(StoreDiaryCommentRequest $request, DiaryResponse $response): JsonResponse
    {
        return response()->json(
            $this->diaryService->createComment(
                $response,
                $request->user()->id,
                $request->content,
                $request->parent_id,
            )
        );
    }

    public function comments(DiaryResponse $response): JsonResponse
    {
        return response()->json([
            'comments' => $this->diaryService->getComments($response, Auth::id()),
        ]);
    }

    public function commentLike(DiaryResponseComment $comment): JsonResponse
    {
        return response()->json([
            'liked' => $this->diaryService->toggleCommentLike($comment, Auth::id()),
        ]);
    }

    public function commentDestroy(DiaryResponseComment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        return response()->json([
            'success' => true,
            'comments_count' => $this->diaryService->deleteComment($comment),
        ]);
    }

    public function report(StoreDiaryReportRequest $request, DiaryResponse $response): JsonResponse
    {
        $created = $this->diaryService->report($response, Auth::id(), $request->reason);

        if (! $created) {
            return response()->json([
                'message' => 'Ya reportaste esta respuesta. Lo estaremos revisando.',
            ]);
        }

        return response()->json([
            'message' => 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.',
        ]);
    }

    public function destroy(DiaryResponse $response): JsonResponse
    {
        Gate::authorize('delete', $response);

        $this->diaryService->deleteResponse($response);

        return response()->json(['success' => true]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $diary = $this->diaryService->getActiveDiary();
        abort_if($diary === null, 404);

        $sort = $request->sort ?? 'top';
        $responses = $this->diaryService->getResponses($diary->id, Auth::id(), $sort);

        $html = '';
        foreach ($responses as $response) {
            $html .= view('diary.partials._response', ['response' => $response])->render();
        }

        return response()->json([
            'html' => $html,
            'next_page_url' => $responses->nextPageUrl(),
        ]);
    }
}
