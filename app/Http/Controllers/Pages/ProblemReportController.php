<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProblemReportRequest;
use App\Models\ProblemReport;
use Illuminate\Http\JsonResponse;

class ProblemReportController extends Controller
{
    public function store(StoreProblemReportRequest $request): JsonResponse
    {
        ProblemReport::create([
            'user_id' => $request->user()->id,
            'type' => $request->validated('type'),
            'message' => $request->validated('message'),
        ]);

        return response()->json(['success' => true]);
    }
}
