<?php

namespace App\Support;

class AdminUnlock
{
    public static function isUnlocked(): bool
    {
        $sessionKey = (string) config('admin.session_key');
        $unlockedAt = session($sessionKey);

        if (!$unlockedAt) {
            return false;
        }

        $timeout = (int) config('admin.unlock_timeout_seconds');
        $expiresAt = ((int) $unlockedAt) + $timeout;

        if (now()->timestamp > $expiresAt) {
            self::lock();
            return false;
        }

        return true;
    }

    public static function unlock(): void
    {
        $sessionKey = (string) config('admin.session_key');
        session()->put($sessionKey, now()->timestamp);
    }

    public static function lock(): void
    {
        $sessionKey = (string) config('admin.session_key');
        session()->forget($sessionKey);
    }
}
