<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationsModel;
use App\Models\NotificationReadModel;

class Notifications extends BaseController
{
    /**
     * Admin index listing (paginated)
     */
    public function index()
    {
        $model = new NotificationsModel();
        $data['notifications'] = $model->orderBy('created_at', 'DESC')->paginate(10);
        $data['pager'] = $model->pager;

        // Per-user last_read_at (if available) to help determine unread rows
        $data['last_read_at'] = null;
        if (auth()->user()) {
            $nr = new \App\Models\NotificationReadModel();
            $row = $nr->where('user_id', auth()->id())->first();
            if ($row && ! empty($row['last_read_at'])) {
                $data['last_read_at'] = $row['last_read_at'];
            }
        }

        return view('admin/notifications/index', $data);
    }

    /**
     * Show create/edit form
     */
    public function form($id = null)
    {
        $model = new NotificationsModel();
        $data = [];
        if ($id) {
            $data['notification'] = $model->find($id);
        }
        return view('admin/notifications/form', $data);
    }

    /**
     * Save (create or update)
     */
    public function save()
    {
        $model = new NotificationsModel();
        $id = $this->request->getPost('id');

        $payload = [
            'type' => $this->request->getPost('type'),
            'title' => $this->request->getPost('title'),
            'body' => $this->request->getPost('body'),
            'user_id' => $this->request->getPost('user_id') ?: null,
            'ip_address' => $this->request->getIPAddress(),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $model->update($id, $payload);
            toast_success('Notification updated');
            return redirect()->to(site_url('admin/notifications'));
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        $model->insert($payload);
        toast_success('Notification created');
        return redirect()->to(site_url('admin/notifications'));
    }

    /**
     * Delete single
     */
    public function delete($id)
    {
        // permission check: only admin or superadmin can delete notifications
        if (! auth()->user() || (! auth()->user()->inGroup('admin') && ! auth()->user()->inGroup('superadmin'))) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
            }
            toast_error('You do not have permission to delete notifications.');
            return redirect()->back();
        }

        $model = new NotificationsModel();
        $model->delete($id);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Notification deleted']);
        }
        toast_success('Notification deleted');
        return redirect()->back();
    }

    /**
     * Purge selected
     */
    public function purge()
    {
        $ids = $this->request->getPost('ids') ?? [];
        // permission check: only admin or superadmin
        if (! auth()->user() || (! auth()->user()->inGroup('admin') && ! auth()->user()->inGroup('superadmin'))) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
            }
            toast_error('You do not have permission to delete notifications.');
            return redirect()->back();
        }

        $model = new NotificationsModel();
        foreach ($ids as $id) {
            $model->delete((int) $id);
        }
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Deleted selected notifications']);
        }
        toast_success('Deleted selected notifications');
        return redirect()->back();
    }

    /**
     * Mark a single notification as read (sets is_read = 1)
     */
    public function markAsRead($id)
    {
        $model = new NotificationsModel();
        $model->update($id, ['is_read' => 1]);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Marked as read']);
        }
        toast_success('Marked as read');
        return redirect()->back();
    }

    /**
     * Bulk mark selected notifications as read
     */
    public function markReadSelected()
    {
        $ids = $this->request->getPost('ids') ?? [];
        $model = new NotificationsModel();
        foreach ($ids as $id) {
            $model->update((int) $id, ['is_read' => 1]);
        }
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Marked selected as read']);
        }
        toast_success('Marked selected as read');
        return redirect()->back();
    }

    /**
     * API for top navbar: recent notifications
     */
    public function recent()
    {
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $offset = (int) ($this->request->getGet('offset') ?? 0);
        $since = $this->request->getGet('since');

        $model = new NotificationsModel();

        if ($since) {
            $rows = $model->where('created_at >', $since)->orderBy('id', 'DESC')->limit($limit)->find();
        } else {
            $rows = $model->orderBy('id', 'DESC')->limit($limit, $offset)->find();
        }

        $items = array_map(static function ($r) {
            return [
                'id' => $r['id'],
                'user_id' => $r['user_id'],
                'is_read' => isset($r['is_read']) ? (int)$r['is_read'] : 0,
                'action' => $r['title'] ?: substr($r['body'] ?? '', 0, 120),
                'ip' => $r['ip_address'],
                'ua' => null,
                'created_at' => $r['created_at'],
            ];
        }, $rows);

        return $this->response->setJSON(['items' => $items, 'now' => date('Y-m-d H:i:s')]);
    }
    /**
     * Return unread notification count (fast endpoint for navbar badge)
     */
    public function unreadCount()
    {
        $model = new NotificationsModel();
        // If user has per-user last_read_at, count only items after that
        if (auth()->user()) {
            $nr = new \App\Models\NotificationReadModel();
            $row = $nr->where('user_id', auth()->id())->first();
            if ($row && ! empty($row['last_read_at'])) {
                $count = $model->where('created_at >', $row['last_read_at'])->countAllResults();
                return $this->response->setJSON(['count' => (int) $count]);
            }
        }
        $count = $model->where('is_read', 0)->countAllResults();
        return $this->response->setJSON(['count' => (int) $count]);
    }
    /**
     * Persist a per-user last_read_at timestamp (mark all read for current user)
     */
    public function markAllReadPerUser()
    {
        if (! auth()->user()) {
            return $this->response->setStatusCode(401)->setJSON(['ok' => false, 'message' => 'Not authenticated']);
        }

        $nr = new \App\Models\NotificationReadModel();
        $now = date('Y-m-d H:i:s');
        $existing = $nr->where('user_id', auth()->id())->first();
        if ($existing) {
            $nr->update($existing['id'], ['last_read_at' => $now]);
        } else {
            $nr->insert(['user_id' => auth()->id(), 'last_read_at' => $now]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'All notifications marked read', 'now' => $now]);
        }

        toast_success('All notifications marked as read');
        return redirect()->back();
    }
    /**
     * Mark all notifications as read (global)
     */
    public function markRead()
    {
        // Update all notifications as read. Using a direct query because
        // Model::update requires a WHERE clause for safety.
        $db = \Config\Database::connect();
        $db->query("UPDATE notifications SET is_read = 1");
        return $this->response->setJSON(['ok' => true, 'now' => date('Y-m-d H:i:s')]);
    }

    // NOTE: markAllReadPerUser implemented above; duplicate removed.
}
