<?php

declare(strict_types=1);

namespace App\Models;


/**
 * Центр уведомлений
 *
 */
final class NotificationCenter extends AppModel
{

    protected $fillable = [
        'notification_name',
        'recipient',
        'user_id',
        'channel_name'
    ];
}