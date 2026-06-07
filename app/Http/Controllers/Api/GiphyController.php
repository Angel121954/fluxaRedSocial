<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GiphyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GiphyController extends Controller
{
    public function __construct(
        protected GiphyService $giphy,
    ) {}

    public function search(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString();

        if (empty($query)) {
            return response()->json(['gifs' => [], 'total' => 0]);
        }

        $offset = max(0, (int) $request->integer('offset', 0));

        $result = $this->giphy->search($query, $offset);

        return response()->json($result);
    }

    public function trending(Request $request): JsonResponse
    {
        $offset = max(0, (int) $request->integer('offset', 0));

        $result = $this->giphy->trending($offset);

        return response()->json($result);
    }
}
