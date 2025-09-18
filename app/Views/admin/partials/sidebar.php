<?php
  // helper to mark active items
  $isActive = static function(string $path): string {
      return (uri_string() === trim($path, '/')) ? 'active' : '';
  };
?>
<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="https://randomuser.me/api/portraits/men/1.jpg" alt="User Image">
    <div>
      <p class="app-sidebar__user-name">
        <?= esc(function_exists('auth') && auth()->user() ? auth()->user()->username : 'User') ?>
      </p>
      <p class="app-sidebar__user-designation">Administrator</p>
    </div>
  </div>

  <ul class="app-menu">
    <li>
      <a class="app-menu__item <?= $isActive('admin') ?>" href="<?= site_url('admin') ?>">
        <i class="app-menu__icon bi bi-speedometer"></i><span class="app-menu__label">Dashboard</span>
      </a>
    </li>

    <li class="treeview">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon bi bi-table"></i><span class="app-menu__label">Artists</span>
        <i class="treeview-indicator bi bi-chevron-right"></i>
      </a>
      <ul class="treeview-menu">
        <li><a class="treeview-item <?= $isActive('admin/artists') ?>" href="<?= site_url('admin/artists') ?>"><i class="icon bi bi-circle-fill"></i> List</a></li>
        <li><a class="treeview-item <?= $isActive('admin/artists/create') ?>" href="<?= site_url('admin/artists/create') ?>"><i class="icon bi bi-circle-fill"></i> Create</a></li>
      </ul>
    </li>

    <!-- add more items as needed -->
  </ul>
</aside>
