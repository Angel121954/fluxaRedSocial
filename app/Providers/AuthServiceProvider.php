<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\DiaryResponseComment;
use App\Models\Education;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Profile;
use App\Models\WorkExperience;
use App\Policies\CommentPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\DiaryResponseCommentPolicy;
use App\Policies\EducationPolicy;
use App\Policies\MessagePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\WorkExperiencePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Comment::class => CommentPolicy::class,
        DiaryResponseComment::class => DiaryResponseCommentPolicy::class,
        Project::class => ProjectPolicy::class,
        Profile::class => ProfilePolicy::class,
        Message::class => MessagePolicy::class,
        Conversation::class => ConversationPolicy::class,
        Education::class => EducationPolicy::class,
        Notification::class => NotificationPolicy::class,
        WorkExperience::class => WorkExperiencePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
