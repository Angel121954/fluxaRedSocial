<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreCredentialRequest;
use App\Http\Requests\Profile\UpdateCredentialRequest;
use App\Models\Credential;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CredentialController extends Controller
{
    private const MAX_CREDENTIALS = 10;

    public function index(): View
    {
        $credentials = Credential::where('user_id', Auth::id())
            ->orderByDesc('issued_at')
            ->get();

        return view('profile.credentials', [
            'credentials' => $credentials,
            'limits' => ['max_credentials' => self::MAX_CREDENTIALS],
        ]);
    }

    public function store(StoreCredentialRequest $request): RedirectResponse
    {
        if (Credential::where('user_id', Auth::id())->count() >= self::MAX_CREDENTIALS) {
            return back()
                ->withInput()
                ->withErrors(['title' => 'Has alcanzado el límite de '.self::MAX_CREDENTIALS.' certificaciones.']);
        }

        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        Credential::create($validated);

        return redirect()->route('credentials.index')
            ->with('success', 'Certificación agregada correctamente.');
    }

    public function update(UpdateCredentialRequest $request, Credential $credential): RedirectResponse
    {
        $this->authorize('update', $credential);

        $credential->update($request->validated());

        return redirect()->route('credentials.index')
            ->with('success', 'Certificación actualizada correctamente.');
    }

    public function destroy(Credential $credential): RedirectResponse
    {
        $this->authorize('delete', $credential);

        $credential->delete();

        return redirect()->route('credentials.index')
            ->with('success', 'Certificación eliminada correctamente.');
    }
}
