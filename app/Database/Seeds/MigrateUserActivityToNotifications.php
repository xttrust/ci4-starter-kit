<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\NotificationsModel;

class MigrateUserActivityToNotifications extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $activity = $db->table('user_activity');
        $notifModel = new NotificationsModel();

        // Fetch all legacy rows
        $rows = $activity->orderBy('id', 'ASC')->get()->getResultArray();

        $inserted = 0;
        foreach ($rows as $r) {
            // Basic de-dup: check same created_at and action
            $exists = $notifModel->where('created_at', $r['created_at'])->where('title', $r['action'])->first();
            if ($exists) continue;

            $payload = [
                'type' => 'activity',
                'title' => $r['action'],
                'body' => null,
                'user_id' => $r['user_id'] ?? null,
                'ip_address' => $r['ip_address'] ?? null,
                'is_read' => 0,
                'created_at' => $r['created_at'],
                'updated_at' => $r['created_at'],
            ];

            $notifModel->insert($payload);
            $inserted++;
        }

        echo "Migrated {$inserted} rows from user_activity to notifications\n";
    }
}
