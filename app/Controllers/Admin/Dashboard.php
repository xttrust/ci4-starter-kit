<?php 

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title'       => 'Dashboard',
            'appTitle'    => 'Dashboard',              // optional: used in layout header
            'subtitle'    => 'Start a beautiful journey here', // optional
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => site_url('admin')],
                ['label' => 'Dashboard'],
            ],
        ];

        // Because the view itself extends layouts/backend (Vali),
        // you just return it:
        return view('admin/dashboard', $data);
        // or, if you want to keep your Theme library:
        // return $this->theme->backend('admin/dashboard', $data);
    }
}
