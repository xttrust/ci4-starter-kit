<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActivityIdToNotifications extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Add the activity_id column if it doesn't exist
        $fields = [
            'activity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
        ];

        // Use forge to add column
        $this->forge->addColumn('notifications', $fields);

        // Add an index for performance
        $this->forge->addKey('activity_id');

        // Add foreign key constraint for referential integrity (ON DELETE CASCADE)
        // Some DB drivers/versions may require the referenced table to have a primary key and matching engine
        try {
            $db->query("ALTER TABLE `notifications` ADD CONSTRAINT `fk_notifications_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `user_activity`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Throwable $e) {
            // Log and continue; the migration should not fatally fail if the DB doesn't support FK in this environment.
            log_message('error', 'Could not add foreign key fk_notifications_activity_id: ' . $e->getMessage());
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Drop foreign key if exists
        try {
            $db->query("ALTER TABLE `notifications` DROP FOREIGN KEY `fk_notifications_activity_id`");
        } catch (\Throwable $e) {
            // ignore
        }

        // Drop the column
        $this->forge->dropColumn('notifications', 'activity_id');
    }
}
