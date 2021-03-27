<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;


/**
 * Центр уведомлений приложения
 *
 */
final class EmailNotificationCenter extends NotificationCenter
{

    /**
     * Реализация абстрактного метода
     *
     * @return string
     */
    public function getChannelName(): string
    {
        return self::MAIL_CHANNEL;
    }



    /**
     * Email уведомление
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
}
