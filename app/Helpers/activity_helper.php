<?php

use App\Models\UserActivityModel;
use CodeIgniter\I18n\Time;

if (! function_exists('log_activity')) {
    /**
     * Quick helper to log user activity.
     *
     * @param string $action  Description of what the user did
     * @param int|null $userId Optional user ID (defaults to current auth user)
     */
    function log_activity(string $action, ?int $userId = null): void
    {
        $user = auth()->user();
        $userId ??= $user?->id;

        $entry = [
            'user_id'    => $userId,
            'action'     => ($user?->username ?? 'guest') . ' - ' . $action,
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => (string) service('request')->getUserAgent(),
            'created_at' => Time::now(), // in case timestamps are not auto-handled
        ];

        $model = new UserActivityModel();
        $model->insert($entry);
    }
}
