<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocketHelper
{
    protected static string $serverUrl = '';

    public static function setServerUrl(string $url): void
    {
        self::$serverUrl = $url;
    }

    public static function push(string $channel, array $data): void
    {
        $url = self::$serverUrl ?: (env('SOCKET_SERVER_URL', 'http://127.0.0.1:3001'));
        try {
            Http::timeout(1)->post($url . '/push', [
                'channel' => $channel,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            Log::debug('Socket push failed: ' . $e->getMessage());
        }
    }

    public static function notification(array $data): void
    {
        self::push('notification', $data);
    }

    public static function cartUpdate(): void
    {
        self::push('cart-update', []);
    }
}
