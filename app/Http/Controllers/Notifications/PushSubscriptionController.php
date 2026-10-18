<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StorePushSubscriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Persiste (crea o actualiza) la suscripción push del usuario autenticado.
     */
    public function store(StorePushSubscriptionRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->updatePushSubscription(
            endpoint: $request->string('endpoint')->toString(),
            key: $request->string('public_key')->toString(),
            token: $request->string('auth_token')->toString(),
            contentEncoding: $request->string('content_encoding')->toString()
        );

        return response()->json(['success' => true]);
    }

    /**
     * Elimina la suscripción push del usuario (por endpoint).
     */
    public function destroy(Request $request): JsonResponse
    {
        $expectedEndpoint = $request->string('endpoint')->toString();

        if ($expectedEndpoint !== '') {
            $request->user()->deletePushSubscription($expectedEndpoint);

            return response()->json(['success' => true]);
        }

        $request->user()->pushSubscriptions()->delete();

        return response()->json(['success' => true]);
    }
}
