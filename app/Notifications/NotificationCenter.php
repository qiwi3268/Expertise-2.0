<?php

declare(strict_types=1);

namespace App\Notifications;

use InvalidArgumentException;


/**
 * Центр уведомлений приложения
 *
 */
abstract class NotificationCenter extends AppNotification
{

    /**
     * Конструктор класса
     *
     * @param string $notificationName название уведомления
     * @param string $recipient получатель
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $notificationName,
        private string $recipient
    ) {
        if (empty($notificationName)) {
            throw new InvalidArgumentException('Название уведомления не может быть пустым');
        }
        if (empty($recipient)) {
            throw new InvalidArgumentException('Получатель не может быть пустым');
        }

        parent::__construct();
    }


    /**
     * Возвращает название уведомления
     *
     * @return string
     */
    public function getNotificationName(): string
    {
        return $this->notificationName;
    }


    /**
     * Возвращает получателя
     *
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }


    /**
     * Реализация абстрактного метода
     *
     * @param mixed $notifiable
     * @return array
     */
    final public function via(mixed $notifiable): array
    {
        return [$this->getChannelName()];
    }


    /**
     * Возвращает название канала уведомления дочернего класса
     *
     * @return string
     */
    abstract public function getChannelName(): string;
}
