<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\NotificationCenter as NotificationCenterModel;
use App\Notifications\NotificationCenter;



final class NotificationCenterListener// implements ShouldQueue
{
    use InteractsWithQueue;

    //todo настройки Queueblae

    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $a = 1;
    }


    /**
     * Обработка события
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event): void
    {
        $notification = $event->notification;

        if (!($notification instanceof NotificationCenter)) {
            // todo разобраться с $this->delete() и трейтом;
            return;
        }

        NotificationCenterModel::create([
            'notification_name' => $notification->getNotificationName(),
            'recipient'         => 'tmp',
            'user_id'           => null, //todo
            'channel_name'      => $notification->getChannelName(),
        ]);


        $a = 1;
    }
}