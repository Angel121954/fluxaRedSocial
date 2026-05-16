<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\ProblemReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProblemReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        ProblemReport::create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        return response()->json(['success' => true]);
    }
}
