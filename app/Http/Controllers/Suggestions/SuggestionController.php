<?php

namespace App\Http\Controllers\Suggestions;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuggestionRequest;
use App\Models\Suggestion;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Suggestion::with('user');

        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $order = $request->get('order', 'latest');
        match ($order) {
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $suggestions = $query->paginate(10);

        return view('admin.suggestions.index', compact('suggestions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suggestions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuggestionRequest $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $suggestionId = Suggestion::max('id') + 1;
            $result = $this->cloudinaryService->uploadSuggestionImage(
                $request->file('image'),
                "suggestion_{$suggestionId}"
            );
            $imagePath = $result['secure_url'];
        }

        Suggestion::create([
            'user_id' => $request->user()->id,
            'description' => $request->validated()['description'],
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()->route('explore.index')->with('success', 'Sugerencia enviada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Suggestion $suggestion)
    {
        return redirect()->route('admin.suggestions.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Suggestion $suggestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Suggestion $suggestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suggestion $suggestion)
    {
        $suggestion->delete();

        return redirect()->route('admin.suggestions.index')->with('success', 'Sugerencia eliminada');
    }

    public function approve(Suggestion $suggestion)
    {
        $suggestion->update(['status' => 'approved']);

        return redirect()->route('admin.suggestions.index')->with('success', 'Sugerencia aprobada');
    }

    public function markRead(Suggestion $suggestion)
    {
        $suggestion->update(['status' => 'reviewing']);

        return redirect()->route('admin.suggestions.index')->with('success', 'Sugerencia marcada como leída');
    }
}
