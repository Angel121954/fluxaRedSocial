<?php

namespace App\View\Composers;

use App\Models\Conversation;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopbarComposer
{
    public function compose(View $view): void
    {
        $currentConvId = request()->query('conv') ? (int) request()->query('conv') : null;

        if (Auth::check() && Auth::user()->role !== 'guest') {
            $unreadMessages = Conversation::getUnreadGlobalCount(Auth::id(), $currentConvId);
            $unreadNotifications = Notification::unreadCount(Auth::id());
        } else {
            $unreadMessages = 0;
            $unreadNotifications = 0;
        }

        $view->with('unreadMessages', $unreadMessages);
        $view->with('unreadNotifications', $unreadNotifications);
    }
}
