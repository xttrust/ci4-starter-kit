<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Entities\User;
use App\Models\UserActivityModel;

/**
 * Admin\Users Controller
 *
 * CRUD and management actions for Shield users in the Admin area.
 * - Lists users with simple search (by username or email).
 * - Creates users (username + email/password + optional group).
 * - Edits users (username, email, password, group).
 * - Deletes users (soft delete by default).
 * - Toggles user status (active/inactive).
 *
 * Security:
 * - Routes are protected via route filters (e.g., permission:users.*).
 * - CSRF protection is expected globally (csrf_field() in forms).
 * - Prevents self-delete and (optionally) superadmin deletion—see notes in delete().
 *
 * Notes:
 * - Uses Shield models/entities; email/password identity stored in auth_identities:
 *   - type     = "email_password"
 *   - secret   = email
 *   - secret2  = password hash
 * - Username lives in `users` (Shield users table).
 */
class Users extends BaseController
{
    /** @var ShieldUserModel */
    protected ShieldUserModel $users;

    /** @var UserIdentityModel */
    protected UserIdentityModel $identities;

    /**
     * Controller initializer.
     * Instantiates Shield models once per request.
     *
     * @param mixed $request
     * @param mixed $response
     * @param mixed $logger
     */
    public function initController($request, $response, $logger): void
    {
        parent::initController($request, $response, $logger);

        // Shield models
        $this->users      = new ShieldUserModel();
        $this->identities = new UserIdentityModel();
    }

    /**
     * GET /admin/users
     * List users with optional search (q) over username or email.
     *
     * @return string
     */
    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));

        // Base query: select the most useful columns for the grid
        $builder = $this->users
            ->select('users.id, users.username, users.status, users.last_active, users.created_at')
            ->orderBy('users.id', 'DESC');

        // If searching, match username OR email (email is stored in identities.secret)
        if ($q !== '') {
            $builder->groupStart()
                    ->like('users.username', $q)
                    ->orWhereIn('users.id', function ($sub) use ($q) {
                        $sub->select('user_id')
                            ->from('auth_identities')
                            ->like('secret', $q); // email lives in `secret` for email_password
                    })
                    ->groupEnd();
        }

        // Paginate results
        $perPage = 20;
        $users   = $builder->paginate($perPage);
        $pager   = $this->users->pager;

        // Render index view
        return view('admin/users/index', [
            'title'       => 'Users',
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => site_url('admin')],
                ['label' => 'Users'],
            ],
            'q'     => $q,
            'users' => $users,
            'pager' => $pager,
        ]);
    }

    /**
     * GET|POST /admin/users/create
     * Render create form (GET) and handle user creation (POST).
     *
     * Flow:
     * - Validate username/email/password.
     * - Insert user (Shield users table).
     * - Insert email/password identity to auth_identities.
     * - Optionally assign group.
     *
     * @return RedirectResponse|string
     */
    public function create()
    {
        log_message('error', 'HIT Users::create() method: ' . $this->request->getMethod());

        // NOTE: In this project we compare with uppercase "POST" for consistency.
        if ($this->request->getMethod() === 'POST') {
            // Pull whitelisted input
            $data = $this->request->getPost([
                'username', 'email', 'password', 'password_confirm', 'group',
            ]);

            // Validation: ensure username is unique in users; email unique in identities
            $rules = [
                'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'email'            => 'required|valid_email|is_unique[auth_identities.secret]',
                'password'         => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
                'group'            => 'permit_empty|string',
            ];

            if (! $this->validate($rules)) {
                // Feedback + re-display with input and field errors
                toast_error('Please fix the errors below.');
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Create Shield user entity (status/active fields depend on your Shield config)
            $user = new User([
                'username' => $data['username'],
                'active'   => 'active', // matches your current schema usage
            ]);

            // Insert user row and get its ID (save() does not guarantee $user->id)
            $userId = $this->users->insert($user, true);

            // Create primary identity (email/password)
            // NOTE: You can switch to service('passwords')->hash(...) to follow Shield hashing config.
            $this->identities->insert([
                'user_id' => $userId,
                'type'    => 'email_password',
                'secret'  => $data['email'],                                // email
                'secret2' => password_hash($data['password'], PASSWORD_DEFAULT), // password hash
            ]);

            // Optional: assign to a group (e.g., admin, editor, user)
            if (! empty($data['group'])) {
                $user = $this->users->find($userId); // refresh entity bound to DB row
                $user->addGroup($data['group']);
            }

            toast_success('User created.');
            return redirect()->to(site_url('admin/users'));
        }

        // GET: render the create form
        return view('admin/users/create', [
            'title'       => 'Create User',
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => site_url('admin')],
                ['label' => 'Users', 'url' => site_url('admin/users')],
                ['label' => 'Create'],
            ],
        ]);
    }

    /**
     * GET|POST /admin/users/{id}/edit
     * Render edit form (GET) and handle updates (POST).
     *
     * Updates:
     * - Username (unique in users, ignoring current row).
     * - Email (unique in auth_identities, ignoring current identity).
     * - Password (only if provided).
     * - Group (optional re-assignment).
     *
     * @param int $id User ID (Shield users.id)
     * @return RedirectResponse|string
     */
    public function edit(int $id)
    {
        // Ensure the record exists
        $user = $this->users->find($id);
        if (! $user) {
            log_activity("Attempted to edit non-existent user #{$id}");
            toast_error('User not found.');
            return redirect()->to(site_url('admin/users'));
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getPost(['username', 'email', 'password', 'password_confirm', 'group']);

            // Current identity for uniqueness ignore rule (if it exists)
            $emailIdentity = $this->identities
                ->where('user_id', $id)
                ->where('type', 'email_password')
                ->first();

            // Validation:
            // - username unique except for this user
            // - email unique except for this user's email identity
            $rules = [
                'username'         => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
                'email'            => 'required|valid_email|is_unique[auth_identities.secret,id,' . ($emailIdentity->id ?? 0) . ']',
                'password'         => 'permit_empty|min_length[8]',
                'password_confirm' => 'permit_empty|matches[password]',
                'group'            => 'permit_empty|string',
            ];

            if (! $this->validate($rules)) {
                // Log details for quick diagnosis if needed
                log_message('error', 'Validation errors: ' . print_r($this->validator->getErrors(), true));
                log_activity("Validation failed when editing user #{$id} : " . json_encode($this->validator->getErrors()));
                
                toast_error('Please fix the errors below.');
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Update username (check for model errors to avoid silent failures)
            if (! $this->users->update($id, ['username' => $data['username']])) {
                log_message('error', 'User update failed: ' . json_encode($this->users->errors()));
                log_activity("Failed to update user #{$id} : " . json_encode($this->users->errors()));
                
                toast_error('Failed to update user.');
                return redirect()->back()->withInput();
            }

            // Update or insert email identity
            if ($emailIdentity) {
                $this->identities->update($emailIdentity->id, ['secret' => $data['email']]);
            } else {
                $this->identities->insert([
                    'user_id' => $id,
                    'type'    => 'email_password',
                    'secret'  => $data['email'],
                ]);
            }

            // Update password if provided
            if (! empty($data['password'])) {
                $hash = service('passwords')->hash($data['password']); // follow Shield hashing policy
                $this->identities
                    ->where('user_id', $id)
                    ->where('type', 'email_password')
                    ->set(['secret2' => $hash])
                    ->update();
                // Note: You might want to invalidate sessions or tokens here
            }

            // Group (optional): remove from all known groups and add selected
            if (! empty($data['group'])) {
                $user       = $this->users->find($id); // refresh entity
                $allGroups  = array_keys(config('AuthGroups')->groups ?? []);
                foreach ($allGroups as $g) {
                    $user->removeGroup($g);
                }
                $user->addGroup($data['group']);
            }

            log_activity("Updated user #{$id}");

            toast_success('User updated.');
            return redirect()->to(site_url('admin/users'));
        }

        // GET: render the edit form with current email (from identity)
        $emailIdentity = $this->identities
            ->where('user_id', $id)
            ->where('type', 'email_password')
            ->first();

        return view('admin/users/edit', [
            'title'       => 'Edit User',
            'user'        => $user,
            'userEmail'   => $emailIdentity?->secret,
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => site_url('admin')],
                ['label' => 'Users', 'url' => site_url('admin/users')],
                ['label' => 'Edit'],
            ],
        ]);
    }

    /**
     * POST /admin/users/{id}/delete
     * Soft-deletes a user (recommended). Protects self-delete and can be extended to protect superadmin.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $user = $this->users->find($id);
        if (! $user) {
            log_activity("Attempted to delete non-existent user #{$id}");
            toast_error('User not found.');
            return redirect()->to(site_url('admin/users'));
        }

        // Prevent self-delete (accidental lockout)
        if (auth()->id() === $id) {
            log_activity("Attempted to delete own user #{$id}");
            toast_error('You cannot delete your own account.');
            return redirect()->to(site_url('admin/users'));
        }

        // OPTIONAL: Protect superadmin user/role (uncomment to enforce)
        if ($user->inGroup('superadmin') || $id === 2) {
            log_activity("Attempted to delete superadmin user #{$id}");
            toast_error('Superadmin accounts cannot be deleted.');
            return redirect()->to(site_url('admin/users'));
        }

        // Soft delete (safer). Purge only from maintenance tooling.
        $this->users->delete($id);
        log_activity("Deleted user #{$id}");
        toast_success('User deleted.');
        return redirect()->to(site_url('admin/users'));
    }

    /**
     * POST /admin/users/{id}/toggle
     * Toggles a user's status between active/inactive.
     * Note: Ensure your schema uses the same column (`status`) as read here.
     *
     * @param  int $id
     * @return RedirectResponse
     */
    public function toggle(int $id): RedirectResponse
    {
        $user = $this->users->find($id);
        if (! $user) {
            log_activity("Attempted to toggle status of non-existent user #{$id}");
            toast_error('User not found.');
            return redirect()->to(site_url('admin/users'));
        }

        // Flip status (ensure your Shield users table uses "status")
        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
        $this->users->update($id, ['status' => $newStatus]);

        toast_success("User status set to {$newStatus}.");
        return redirect()->to(site_url('admin/users'));
    }
}
