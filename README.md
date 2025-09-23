# 🚀 CodeIgniter 4 Starter Kit

A modern **CodeIgniter 4 boilerplate** designed to kickstart web projects with a ready-to-use **admin dashboard**, **authentication**, **authorization**, **logging**, and **helpers**.  
Built with **Bootstrap 5**, **Shield Auth**, and best practices for scalability and security.

---

## 📑 Table of Contents

- [Features](#-features)
- [Installation](#-installation)
- [Database](#-database)
- [Authentication & Authorization](#-authentication--authorization)
- [Admin Panel](#-admin-panel)
- [Layouts & Themes](#-layouts--themes)
- [Helpers](#-helpers)
- [Logging](#-logging)
- [Configuration](#-configuration)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

- ⚡ **CodeIgniter 4.6+** base (latest version)
- 🔐 **Shield Auth** (user accounts, sessions, identities, groups, permissions)
- 🎨 **Bootstrap 5 UI** with reusable layouts (`backend.php`, toasts, breadcrumbs)
- 🛠 **Admin Dashboard** with role-based restrictions
- 📂 **Migrations & Seeders** for auth tables and activity logs
- 📝 **Activity Logging** (`user_activity` table + model)
- 🧩 **Helpers** for flash toasts and UI utilities
- 🔒 **CSRF protection** enabled globally
- ✅ **Validation-ready forms** (create/edit users with error feedback)
- 🔄 **Soft deletes** for users (protects accidental data loss)
- 🧑‍🤝‍🧑 **User management** (create, edit, delete, toggle status, group assignment)

---

## ⚙️ Installation

Clone the repo and install dependencies:

```bash
git clone https://github.com/your-username/ci4-starter-kit.git
cd ci4-starter-kit
composer install
```

Copy the environment file and configure:

```bash
cp env .env
```

Update `.env` with your DB and app config:

```dotenv
CI_ENVIRONMENT = development
database.default.hostname = localhost
database.default.database = ci4starter
database.default.username = root
database.default.password = root
database.default.DBDriver = MySQLi
```

Run migrations:

```bash
php spark migrate
```

Start local server:

```bash
php spark serve
```

---

## 🛢 Database

### Auth Tables (from Shield)

- `users` – core user records
- `auth_identities` – login identities (email/password)
- `auth_groups_users` – user-to-group relations
- `auth_permissions_users` – user-to-permission relations
- `auth_logins` – login history
- `auth_remember_tokens`, `auth_token_logins` – session/tokens

### Custom Tables

- `user_activity` – audit log of user actions (admin events, deletes, etc.)

Migration example (`user_activity`):

```sql
id          BIGINT AUTO_INCREMENT PRIMARY KEY
user_id     BIGINT NULL
action      VARCHAR(255)
ip_address  VARCHAR(45)
user_agent  TEXT
created_at  DATETIME NULL
```

---

## 🔐 Authentication & Authorization

- Built on **CodeIgniter Shield**  
- Supports **email + password** login  
- Roles & Permissions:
  - `superadmin` (full access, cannot be deleted)
  - `admin` (access to admin zone, restricted CRUD based on permissions)
  - `user` (default role, no admin access)
- Permissions enforced via route filters:

  ```php
  ['filter' => 'permission:users.edit']
  ```

---

## 🛠 Admin Panel

Accessible at:  

```
/admin
```

### Features

- Dashboard (`Admin\Dashboard`)
- User management (`Admin\Users`)
  - Create (with email/password + group)
  - Edit (update username, email, password, group)
  - Delete (soft delete, prevents self-delete and protects superadmin)
  - Toggle active/inactive
- Pagination + search (username/email)

### Restrictions

- Routes grouped under `/admin` with session + permission filters
- Example:

  ```php
  $routes->match(['GET','POST'], 'users/(:num)/edit', 'Admin\Users::edit/$1', ['filter' => 'permission:users.edit']);
  ```

---

## 🎨 Layouts & Themes

- **`layouts/backend.php`** – Admin layout wrapper  
- **`partials/toasts.php`** – Bootstrap 5 Toasts for flash messages  
- **Breadcrumbs & tiles** – Consistent UI patterns  

---

## 🧩 Helpers

- **UI Helper** (`ui_helper.php`)
  - `toast_success($msg)`
  - `toast_error($msg)`
  - `toast_info($msg)`

- **Activity Helper** (`activity_helper.php`)
  - `log_activity(string $action, ?int $userId = null): void`
  - `Will log a message in user_activity table`
  - `Example: username - Updated group for user #7 to admin`

- Helpers set flashdata consumed by `partials/toasts.php`

---

## 📝 Logging

Two levels of logging are supported:

1. **System Logs** (default CI4)  
   - Written to `writable/logs/`
   - Configurable in `Config/Logger.php`

2. **User Activity Logs**  
   - `user_activity` table + `UserActivityModel`
   - Inserted from controllers for admin/audit events
   - Example:

     ```php
     log_activity("Attempted to edit non-existent user #{$id}");
     ```

---

## ⚙️ Configuration

- **Routes**:  
  - `app/Config/Routes.php` groups admin routes under `/admin`
- **Filters**:  
  - `app/Config/Filters.php` applies `session` and `csrf` globally
- **Shield**:  
  - `app/Config/AuthGroups.php` defines groups and permissions
- **Theme**:  
  - `app/Libraries/Theme.php` handles admin/backend rendering

---

## 📸 Screenshots

### Admin Dashboard

![Dashboard](docs/screenshots/dashboard.png)

### User Management

![Users](docs/screenshots/users.png)

### Create/Edit Form

![Form](docs/screenshots/form.png)

### Toast Notifications

![Toasts](docs/screenshots/toasts.png)

---

## 🤝 Contributing

1. Fork this repo
2. Create a feature branch (`git checkout -b feature/new-thing`)
3. Commit your changes (`git commit -m "Add new thing"`)
4. Push to branch (`git push origin feature/new-thing`)
5. Create Pull Request

---

## 📜 License

This project is open-source under the [MIT License](LICENSE).
