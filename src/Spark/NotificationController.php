<?php

namespace Makeable\DatabaseNotifications\Spark;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Spark\Contracts\Repositories\AnnouncementRepository;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;
use Laravel\Spark\Http\Controllers\Controller;
use Makeable\DatabaseNotifications\Notification;
use Makeable\DatabaseNotifications\NotificationResource;

class NotificationController extends Controller
{
    /**
     * The announcements repository.
     *
     * @var AnnouncementRepository
     */
    protected $announcements;

    /**
     * The notifications repository.
     *
     * @var NotificationRepository
     */
    protected $notifications;

    /**
     * Create a new controller instance.
     *
     * @param  AnnouncementRepository  $announcements
     * @param  NotificationRepository  $notifications
     * @return void
     */
    public function __construct(AnnouncementRepository $announcements,
                                NotificationRepository $notifications)
    {
        $this->announcements = $announcements;
        $this->notifications = $notifications;

        $this->middleware('auth');
    }

    /**
     * Get the recent notifications and announcements for the user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function recent(Request $request)
    {
        return response()->json([
            'announcements' => $this->announcements->recent()->toArray(),
            'notifications' => $this->transformNotifications($this->notifications->recent($request->user()))
        ]);
    }

    /**
     * Mark the given notifications as read.
     *
     * @param  Request  $request
     * @return Response
     */
    public function markAsRead(Request $request)
    {
        Notification::whereIn('id', $request->notifications)->whereMorph('notifiable', $request->user())->update(['read_at' => now()]);
    }

    /**
     * @param Collection $notifications
     * @return array|mixed
     */
    protected function transformNotifications(Collection $notifications)
    {
        if (app()->isAlias(NotificationResource::class)) {
            return call_user_func([
                app()->getAlias(NotificationResource::class, 'collection')
            ], $notifications);
        }
        return $notifications->toArray();
    }
}
