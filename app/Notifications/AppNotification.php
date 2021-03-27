<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;



/**
 * Расширенный класс корневого уведомления
 *
 * Все уведомления приложения отправляются из очереди
 */
abstract class AppNotification extends Notification// implements ShouldQueue
{
    use Queueable;

    public const MAIL_CHANNEL = 'mail';


    /**
     * Конструктор класса
     *
     * @return void
     */
    public function __construct()
    {
        $this->locale = 'ru'; //todo проверить

        // Явное указание имени подключения к очереди
        $this->connection = 'redis';
        // Уведомление из очереди будет отправлено после фиксации транзакций
        $this->afterCommit = true;
    }


    /**
     * Возвращает каналы уведомления
     *
     * @param mixed $notifiable
     * @return array
     */
    abstract public function via(mixed $notifiable): array;
}
