<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Events\NotificationCreated;
use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\SkillEndorsement;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

trait CreatesNotifications
{
    public static function createNotification(
        int $userId,
        string $type,
        string $title,
        string $body,
        ?string $link = null,
        ?int $fromUserId = null,
        ?int $referenceId = null,
        ?string $referenceType = null,
        bool $broadcast = true
    ): Notification {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'link' => $link,
            'from_user_id' => $fromUserId,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
        ]);

        if ($broadcast) {
            Event::dispatch(new NotificationCreated($notification));
        }

        self::sendEmailForNotification($notification);

        return $notification;
    }

    private static function sendEmailForNotification(Notification $notification): void
    {
        $prefs = NotificationPreference::where('user_id', $notification->user_id)->first();

        if (! $prefs || ! $prefs->email_enabled) {
            return;
        }

        $flag = self::getPreferenceFlag($notification->type);

        if ($flag && ! $prefs->$flag) {
            return;
        }

        $user = User::find($notification->user_id);

        if (! $user?->email) {
            return;
        }

        $fromUserName = null;

        if ($notification->from_user_id) {
            $fromUser = User::find($notification->from_user_id);
            $fromUserName = $fromUser?->name;
        }

        Mail::to($user)->queue(new NotificationMail(
            user: $user,
            subject: $notification->title,
            body: $notification->body,
            fromUserName: $fromUserName,
            actionUrl: $notification->link,
            actionText: 'Ver en Fluxa',
        ));
    }

    private static function getPreferenceFlag(string $type): ?string
    {
        return match ($type) {
            Notification::TYPE_FOLLOW => 'notify_followers',
            Notification::TYPE_COMMENT => 'notify_comments',
            Notification::TYPE_LIKE => 'notify_comments',
            Notification::TYPE_MENTION => 'notify_mentions',
            default => null,
        };
    }

    public static function notifyNewMessage(int $recipientId, int $senderId, int $conversationId, string $senderName): Notification
    {
        return self::createNotification(
            userId: $recipientId,
            type: Notification::TYPE_MESSAGE,
            title: 'Nuevo mensaje',
            body: 'te ha enviado un mensaje',
            link: "/messages/{$conversationId}",
            fromUserId: $senderId,
            referenceId: $conversationId,
            referenceType: 'conversation'
        );
    }

    public static function notifyNewFollower(int $recipientId, int $followerId, string $followerName): Notification
    {
        $followerUsername = User::where('id', $followerId)->value('username');

        return self::createNotification(
            userId: $recipientId,
            type: Notification::TYPE_FOLLOW,
            title: 'Nuevo seguidor',
            body: 'comenzó a seguirte',
            link: $followerUsername ? "/profile/{$followerUsername}" : '#',
            fromUserId: $followerId,
            referenceId: $followerId,
            referenceType: 'user'
        );
    }

    public static function notifyProjectLike(int $ownerId, int $likerId, string $likerName, int $projectId, string $projectTitle): Notification
    {
        return self::createNotification(
            userId: $ownerId,
            type: Notification::TYPE_LIKE,
            title: 'Nuevo like',
            body: "dio like a tu proyecto \"{$projectTitle}\"",
            link: "/projects/{$projectId}",
            fromUserId: $likerId,
            referenceId: $projectId,
            referenceType: 'project'
        );
    }

    public static function notifyProjectComment(int $ownerId, int $commenterId, string $commenterName, int $projectId, string $projectTitle): Notification
    {
        return self::createNotification(
            userId: $ownerId,
            type: Notification::TYPE_COMMENT,
            title: 'Nuevo comentario',
            body: "comentó en tu proyecto \"{$projectTitle}\"",
            link: "/projects/{$projectId}",
            fromUserId: $commenterId,
            referenceId: $projectId,
            referenceType: 'project'
        );
    }

    public static function notifyCommentReply(int $commentOwnerId, int $replierId, string $replierName, int $projectId, string $projectTitle): Notification
    {
        return self::createNotification(
            userId: $commentOwnerId,
            type: Notification::TYPE_COMMENT,
            title: 'Nueva respuesta',
            body: "respondió a tu comentario en \"{$projectTitle}\"",
            link: "/projects/{$projectId}",
            fromUserId: $replierId,
            referenceId: $projectId,
            referenceType: 'project'
        );
    }

    public static function notifyEndorsement(int $recipientId, int $endorserId, string $endorserName, int $projectId, string $projectTitle, string $skillType): Notification
    {
        $skillLabel = SkillEndorsement::SKILLS[$skillType]['label'] ?? $skillType;

        return self::createNotification(
            userId: $recipientId,
            type: 'endorsement',
            title: 'Nueva recomendación',
            body: "te recomendó en {$skillLabel} en \"{$projectTitle}\"",
            link: "/projects/{$projectId}",
            fromUserId: $endorserId,
            referenceId: $projectId,
            referenceType: 'project'
        );
    }

    public static function notifyMention(int $mentionedId, int $mentionerId, string $mentionerName, int $referenceId, string $referenceType): Notification
    {
        return self::createNotification(
            userId: $mentionedId,
            type: Notification::TYPE_MENTION,
            title: 'Te mencionaron',
            body: 'te mencionó',
            link: "/{$referenceType}/{$referenceId}",
            fromUserId: $mentionerId,
            referenceId: $referenceId,
            referenceType: $referenceType
        );
    }

    public static function notifyCommentLike(int $commentOwnerId, int $likerId, string $likerName, int $commentId, int $projectId, string $projectTitle): Notification
    {
        return self::createNotification(
            userId: $commentOwnerId,
            type: Notification::TYPE_LIKE,
            title: 'Like en tu comentario',
            body: "dio like a tu comentario en \"{$projectTitle}\"",
            link: "/projects/{$projectId}",
            fromUserId: $likerId,
            referenceId: $commentId,
            referenceType: 'comment'
        );
    }

    public static function notifyDiaryResponseComment(int $responseOwnerId, int $commenterId, string $commenterName): Notification
    {
        return self::createNotification(
            userId: $responseOwnerId,
            type: Notification::TYPE_COMMENT,
            title: 'Nuevo comentario',
            body: 'comentó en tu respuesta del diario',
            link: '/diary',
            fromUserId: $commenterId,
            referenceId: null,
            referenceType: 'diary'
        );
    }

    public static function notifyDiaryResponseLike(int $responseOwnerId, int $likerId, string $likerName): Notification
    {
        return self::createNotification(
            userId: $responseOwnerId,
            type: Notification::TYPE_LIKE,
            title: 'Like en tu respuesta',
            body: 'dio like a tu respuesta del diario',
            link: '/diary',
            fromUserId: $likerId,
            referenceId: null,
            referenceType: 'diary'
        );
    }

    public static function notifyDiaryResponseBookmark(int $responseOwnerId, int $bookmarkerId, string $bookmarkerName): Notification
    {
        return self::createNotification(
            userId: $responseOwnerId,
            type: Notification::TYPE_BOOKMARK,
            title: 'Guardaron tu respuesta',
            body: 'guardó tu respuesta del diario en favoritos',
            link: '/diary',
            fromUserId: $bookmarkerId,
            referenceId: null,
            referenceType: 'diary'
        );
    }

    public static function notifySuggestionApproved(int $recipientId, int $adminId, int $suggestionId): Notification
    {
        return self::createNotification(
            userId: $recipientId,
            type: Notification::TYPE_SUGGESTION,
            title: 'Sugerencia aprobada',
            body: 'Fluxa aprobó tu sugerencia. ¡Pronto la implementaremos!',
            link: route('suggestions.create'),
            fromUserId: null,
            referenceId: $suggestionId,
            referenceType: 'suggestion'
        );
    }
}
