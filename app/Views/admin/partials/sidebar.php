<?php
helper('ui'); // your helper with ui_active_link()

$user = function_exists('auth') && auth()->user() ? auth()->user() : null;

/**
 * Check if user has a permission
 */
function canUser(?\CodeIgniter\Shield\Entities\User $user, string $perm): bool
{
    return $user && $user->can($perm);
}

/**
 * Check if user has any of a list of permissions
 */
function canAnyUser(?\CodeIgniter\Shield\Entities\User $user, array $perms): bool
{
    if (! $user) return false;
    foreach ($perms as $p) {
        if ($user->can($p)) return true;
    }
    return false;
}

/**
 * Return true if any of the given paths is active
 */
function treeExpanded(array $paths): bool
{
    foreach ($paths as $p) {
        if (ui_active_link(null, $p) === 'active') {
            return true;
        }
    }
    return false;
}
?>

<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="https://randomuser.me/api/portraits/men/1.jpg" alt="User Image">
    <div>
      <p class="app-sidebar__user-name">
        <?= esc($user?->username ?? 'User') ?>
      </p>
      <p class="app-sidebar__user-designation">Administrator</p>
    </div>
  </div>

  <ul class="app-menu">

    <!-- Dashboard -->
    <?php if (canUser($user,'admin.access')): ?>
      <li>
        <a class="app-menu__item <?= ui_active_link(null, 'admin') ?>" href="<?= site_url('admin') ?>">
          <i class="app-menu__icon bi bi-speedometer"></i>
          <span class="app-menu__label">Dashboard</span>
        </a>
      </li>
    <?php endif; ?>


    <!-- Users & Access -->
    <?php
      $usersPerms = ['users.view','users.create','users.edit','users.delete','users.manage-admins'];
      if (canAnyUser($user,$usersPerms)):
        $expanded = treeExpanded(['admin/users','admin/access/groups']);
    ?>
      <li class="treeview <?= $expanded?'is-expanded':'' ?>">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon bi bi-people"></i>
          <span class="app-menu__label">Users & Access</span>
          <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
        <ul class="treeview-menu" <?= $expanded?'style="display:block"':'' ?>>
          <?php if (canUser($user,'users.view')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/users') ?>" href="<?= site_url('admin/users') ?>"><i class="icon bi bi-circle-fill"></i> Users</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'users.manage-admins')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/access/groups') ?>" href="<?= site_url('admin/access/groups') ?>"><i class="icon bi bi-circle-fill"></i> Groups & Permissions</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>


    <!-- Content (Media, Pages, Menus) -->
    <?php
      $contentPerms = ['media.view','pages.view','menus.view'];
      if (canAnyUser($user,$contentPerms)):
        $expanded = treeExpanded(['admin/media','admin/pages','admin/menus']);
    ?>
      <li class="treeview <?= $expanded?'is-expanded':'' ?>">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon bi bi-layout-text-window-reverse"></i>
          <span class="app-menu__label">Content</span>
          <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
        <ul class="treeview-menu" <?= $expanded?'style="display:block"':'' ?>>
          <?php if (canUser($user,'media.view')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/media') ?>" href="<?= site_url('admin/media') ?>"><i class="icon bi bi-circle-fill"></i> Media</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'pages.view')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/pages') ?>" href="<?= site_url('admin/pages') ?>"><i class="icon bi bi-circle-fill"></i> Pages</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'menus.view')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/menus') ?>" href="<?= site_url('admin/menus') ?>"><i class="icon bi bi-circle-fill"></i> Menus</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>


    <!-- System (Audit, Jobs, Logs, Backups, Settings) -->
    <?php
      $systemPerms = ['audit.view','jobs.manage','logs.view','system.maintain','backups.manage','admin.settings'];
      if (canAnyUser($user,$systemPerms)):
        $expanded = treeExpanded(['admin/audit','admin/jobs','admin/logs','admin/system','admin/backups','admin/settings']);
    ?>
      <li class="treeview <?= $expanded?'is-expanded':'' ?>">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon bi bi-gear"></i>
          <span class="app-menu__label">System</span>
          <i class="treeview-indicator bi bi-chevron-right"></i>
        </a>
        <ul class="treeview-menu" <?= $expanded?'style="display:block"':'' ?>>
          <?php if (canUser($user,'audit.view')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/audit') ?>" href="<?= site_url('admin/audit') ?>"><i class="icon bi bi-circle-fill"></i> Activity Log</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'jobs.manage')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/jobs') ?>" href="<?= site_url('admin/jobs') ?>"><i class="icon bi bi-circle-fill"></i> Jobs / Queue</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'logs.view')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/logs') ?>" href="<?= site_url('admin/logs') ?>"><i class="icon bi bi-circle-fill"></i> Logs</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'system.maintain')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/system') ?>" href="<?= site_url('admin/system') ?>"><i class="icon bi bi-circle-fill"></i> Cache & Maintenance</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'backups.manage')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/backups') ?>" href="<?= site_url('admin/backups') ?>"><i class="icon bi bi-circle-fill"></i> Backups</a></li>
          <?php endif; ?>
          <?php if (canUser($user,'admin.settings')): ?>
            <li><a class="treeview-item <?= ui_active_link(null,'admin/settings') ?>" href="<?= site_url('admin/settings') ?>"><i class="icon bi bi-circle-fill"></i> Settings</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>


    <!-- Optional modules (show only if user has permissions) -->
    <?php if (canUser($user,'analytics.view')): ?>
      <li>
        <a class="app-menu__item <?= ui_active_link(null,'admin/analytics') ?>" href="<?= site_url('admin/analytics') ?>">
          <i class="app-menu__icon bi bi-activity"></i>
          <span class="app-menu__label">Analytics</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if (canUser($user,'billing.view') || canUser($user,'billing.manage')): ?>
      <li>
        <a class="app-menu__item <?= ui_active_link(null,'admin/billing') ?>" href="<?= site_url('admin/billing') ?>">
          <i class="app-menu__icon bi bi-credit-card"></i>
          <span class="app-menu__label">Billing</span>
        </a>
      </li>
    <?php endif; ?>


  </ul>
</aside>
