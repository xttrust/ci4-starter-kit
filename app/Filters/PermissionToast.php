<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionToast implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['auth']); // Shield helpers
        // If not logged in just let your session/login filter handle it.
        if (! function_exists('auth') || ! auth()->user()) {
            return;
        }

        $user = auth()->user();

        // arguments: ['perm.one','perm.two', ...] → allow if user has ANY
        $perms = (array) ($arguments ?? []);
        if (! $perms) {  // nothing to check
            return;
        }

        foreach ($perms as $perm) {
            if ($user->can($perm)) {
                return; // ok
            }
        }

        // No permission → flash + redirect
        session()->setFlashdata('error', 'You do not have permission to access that area.');
        // Send them somewhere safe (admin home by default)
        return redirect()->to(site_url('admin'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
