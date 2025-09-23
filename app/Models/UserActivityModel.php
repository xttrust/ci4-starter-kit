<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActivityModel extends Model
{
    protected $table            = 'user_activity';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = ['createNotificationFromActivity'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * After insert callback: create a notification for this activity so admin navbar
     * can surface it quickly. The notifications table has an optional activity_id
     * we use to make this idempotent in case of retries.
     *
     * @param array $data
     * @return array
     */
    protected function createNotificationFromActivity(array $data)
    {
        try {
            if (empty($data['id'])) return $data;

            $insertId = is_array($data['id']) ? ($data['id'][0] ?? null) : $data['id'];
            if (! $insertId) return $data;

            $activityId = (int) $insertId;

            $notifModel = new \App\Models\NotificationsModel();

            // If a notification for this activity already exists, skip (idempotent)
            $existing = $notifModel->where('activity_id', $activityId)->first();
            if ($existing) return $data;

            // Load the inserted activity row if available
            $activityRow = $this->find($activityId);
            if (! $activityRow) return $data;
            $activity = is_array($activityRow) ? $activityRow : (array) $activityRow;

            $title = isset($activity['action']) ? (mb_strimwidth($activity['action'], 0, 120, '...')) : 'User activity';
            $body  = $activity['action'] ?? '';

            $notifModel->insert([
                'type' => 'activity',
                'title' => $title,
                'body'  => $body,
                'user_id' => $activity['user_id'] ?? null,
                'ip_address' => $activity['ip_address'] ?? null,
                'is_read' => 0,
                'activity_id' => $activityId,
                'created_at' => $activity['created_at'] ?? date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Never throw from a callback — just log and continue
            log_message('error', 'createNotificationFromActivity failed: ' . $e->getMessage());
        }

        return $data;
    }
}
