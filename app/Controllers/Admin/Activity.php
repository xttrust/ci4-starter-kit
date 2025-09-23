<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserActivityModel;
use App\Models\NotificationReadModel;

class Activity extends BaseController
{
    public function index()
    {
        $model = new UserActivityModel();

        $data['activities'] = $model->orderBy('created_at', 'DESC')->paginate(20);
        $data['pager'] = $model->pager;

        return view('admin/activity/index', $data);
    }

    /**
     * Delete a single activity entry
     */
    public function delete($id)
    {
        // permission check: only admin or superadmin can delete activity entries
        if (! auth()->user() || (! auth()->user()->inGroup('admin') && ! auth()->user()->inGroup('superadmin'))) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
            }
            toast_error('You do not have permission to delete activity entries.');
            return redirect()->back();
        }

        $model = new UserActivityModel();
        $model->delete((int) $id);

        // Also delete any notifications that were generated from this activity
        try {
            $notifModel = new \App\Models\NotificationsModel();
            $notifModel->where('activity_id', (int) $id)->delete();
        } catch (\Throwable $e) {
            log_message('error', 'Failed to delete linked notifications for activity ' . $id . ': ' . $e->getMessage());
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Activity entry deleted']);
        }

        // Use toast helper for consistent toasts across the app
        toast_success('Activity entry deleted');
        return redirect()->back();
    }

    /**
     * Bulk delete selected activity entries
     */
    public function purge()
    {
        $ids = $this->request->getPost('ids') ?? [];
        // permission check: only admin or superadmin can purge
        if (! auth()->user() || (! auth()->user()->inGroup('admin') && ! auth()->user()->inGroup('superadmin'))) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Forbidden']);
            }
            toast_error('You do not have permission to delete activity entries.');
            return redirect()->back();
        }

        $model = new UserActivityModel();
        $notifModel = new \App\Models\NotificationsModel();
        foreach ($ids as $id) {
            $iid = (int) $id;
            $model->delete($iid);
            try {
                $notifModel->where('activity_id', $iid)->delete();
            } catch (\Throwable $e) {
                log_message('error', 'Failed to delete linked notifications for activity ' . $iid . ': ' . $e->getMessage());
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Deleted selected activity entries']);
        }

        toast_success('Deleted selected activity entries');
        return redirect()->back();
    }
}
