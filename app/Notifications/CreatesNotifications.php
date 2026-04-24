<?php

namespace App\Notifications;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\User;
use App\Models\SkillEndorsement;
use Illuminate\Support\Facades\Event;

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

        return $notification;
    }

    public static function notifyNewMessage(int $recipientId, int $senderId, int $conversationId, string $senderName): Notification
    {
        return self::createNotification(
            userId: $recipientId,
            type: Notification::TYPE_MESSAGE,
            title: 'Nuevo mensaje',
            body: "{$senderName} te ha enviado un mensaje",
            link: "/messages/{$conversationId}",
            fromUserId: $senderId,
            referenceId: $conversationId,
            referenceType: 'conversation'
        );
    }

    public static function notifyNewFollower(int $recipientId, int $followerId, string $followerName): Notification
    {
        return self::createNotification(
            userId: $recipientId,
            type: Notification::TYPE_FOLLOW,
            title: 'Nuevo seguidor',
            body: "{$followerName} comenzó a seguirte",
            link: "/profile/{$followerId}",
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
            body: "{$likerName} dio like a tu proyecto \"{$projectTitle}\"",
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
            body: "{$commenterName} comentó en tu proyecto \"{$projectTitle}\"",
            link: "/projects/{$projectId}",
            fromUserId: $commenterId,
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
            body: "{$endorserName} te recomendó en {$skillLabel} en \"{$projectTitle}\"",
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
            body: "{$mentionerName} te mencionó",
            link: "/{$referenceType}/{$referenceId}",
            fromUserId: $mentionerId,
            referenceId: $referenceId,
            referenceType: $referenceType
        );
    }
}