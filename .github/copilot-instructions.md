# 🧑‍💻 Copilot Instructions for CI4 Starter Kit

This guide enables AI coding agents to be productive in the CodeIgniter 4 Starter Kit codebase. It summarizes architecture, workflows, and conventions unique to this project.

---

## 🏗️ Architecture Overview
- **Framework**: CodeIgniter 4.6+ with [Shield Auth](https://github.com/codeigniter4/shield) for authentication/authorization.
- **Admin Panel**: All admin features are under `/admin` routes, protected by session and permission filters.
- **User Management**: CRUD, group assignment, soft deletes, and activity logging via `Admin\Users` controller and `UserActivityModel`.
- **Database**: Core tables from Shield (`users`, `auth_identities`, etc.) plus custom `user_activity` for audit logs.
- **UI**: Bootstrap 5 layouts in `Views/layouts/backend.php` and partials (e.g., `partials/toasts.php`).
- **Helpers**: Custom helpers in `app/Helpers/` for UI toasts and activity logging.
- **Themes**: Admin/backend rendering via `app/Libraries/Theme.php`.

---

## ⚡ Developer Workflows
- **Install**: `composer install` (see `README.md`)
- **Configure**: Copy `env` to `.env`, set DB credentials, and environment.
- **Run Migrations**: `php spark migrate`
- **Start Server**: `php spark serve`
- **Run Tests**: `vendor\bin\phpunit` or `php spark test` (see `tests/README.md`)
- **Debug**: System logs in `writable/logs/`, user activity in `user_activity` table.

---

## 🧩 Project-Specific Patterns
- **Route Filters**: Permissions enforced via route filters, e.g.:
  ```php
  $routes->match(['GET','POST'], 'users/(:num)/edit', 'Admin\Users::edit/$1', ['filter' => 'permission:users.edit']);
  ```
- **Activity Logging**: Use `log_activity($action, $userId)` from controllers for admin/audit events.
- **Flash Toasts**: Set via helpers, displayed in `partials/toasts.php`.
- **Soft Deletes**: Users are soft-deleted to prevent accidental loss; superadmin cannot be deleted.
- **Group/Permission Management**: Defined in `app/Config/AuthGroups.php`.

---

## 🔗 Integration Points
- **Shield Auth**: Integrated via config files in `app/Config/`.
- **Bootstrap 5**: Used for all admin UI components.
- **Migrations/Seeders**: Located in `app/Database/Migrations` and `app/Database/Seeds`.

---

## 📁 Key Files & Directories
- `app/Controllers/Admin/` — Admin controllers
- `app/Models/UserActivityModel.php` — Audit log model
- `app/Helpers/` — UI and activity helpers
- `app/Config/` — App, routes, filters, auth config
- `app/Views/layouts/backend.php` — Admin layout
- `app/Views/partials/toasts.php` — Flash message UI
- `app/Libraries/Theme.php` — Theme rendering
- `writable/logs/` — System logs
- `tests/` — Unit and integration tests

---

## 📝 Examples
- **Log user action:**
  ```php
  log_activity("Attempted to edit non-existent user #{$id}");
  ```
- **Show success toast:**
  ```php
  toast_success('User created successfully');
  ```

---

## 🛑 Conventions & Gotchas
- Always use helpers for toasts and activity logging.
- Never hard-delete users; use soft delete.
- Superadmin is protected from deletion.
- All admin routes require session and permission filters.
- Update migrations when changing DB schema.

---

For more, see `README.md` and `tests/README.md`. Update this file as project conventions evolve.
