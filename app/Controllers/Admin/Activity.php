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
        foreach ($ids as $id) {
            $model->delete((int) $id);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'message' => 'Deleted selected activity entries']);
        }

        toast_success('Deleted selected activity entries');
        return redirect()->back();
    }
}
