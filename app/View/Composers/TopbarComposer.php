<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Conversation;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopbarComposer
{
    private static ?array $cache = null;

    public function compose(View $view): void
    {
        if (self::$cache !== null) {
            $view->with('unreadMessages', self::$cache['unreadMessages']);
            $view->with('unreadNotifications', self::$cache['unreadNotifications']);
            return;
        }

        $currentConvId = request()->query('conv') ? (int) request()->query('conv') : null;

        if (Auth::check() && Auth::user()->role !== 'guest') {
            self::$cache = [
                'unreadMessages' => Conversation::getUnreadGlobalCount(Auth::id(), $currentConvId),
                'unreadNotifications' => Notification::unreadCount(Auth::id()),
            ];
        } else {
            self::$cache = [
                'unreadMessages' => 0,
                'unreadNotifications' => 0,
            ];
        }

        $view->with('unreadMessages', self::$cache['unreadMessages']);
        $view->with('unreadNotifications', self::$cache['unreadNotifications']);
    }
}
