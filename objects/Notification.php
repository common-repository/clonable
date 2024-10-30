<?php

namespace Clonable\Objects;

class Notification {
    const ERROR = 'error';
    const WARNING = 'warning';
    const SUCCESS = 'success';
    const INFO = 'info';

    public static function add_notification(string $message, string $type) {
        if (!in_array($type, [self::ERROR, self::WARNING, self::SUCCESS, self::INFO])) {
            $type = self::INFO;
        }

        $existing = self::retrieve();
        $existing[] = ['type' => $type, 'content' => $message];
        set_transient('clonable_notifications', $existing, 1);
    }

    /**
     * Gets all the notifications
     * @return array
     */
    public static function retrieve(): array {
        $notifications = get_transient('clonable_notifications');
        if (gettype($notifications) !== 'array') {
            return [];
        }
        return $notifications;
    }
}