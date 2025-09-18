<?php helper('ui'); ?>
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
    <!-- Dashboard -->
    <li>
      <a class="app-menu__item <?= ui_active_link(null, 'admin') ?>" href="<?= site_url('admin') ?>">
        <i class="app-menu__icon bi bi-speedometer"></i>
        <span class="app-menu__label">Dashboard</span>
      </a>
    </li>

    <!-- Artists -->
    <?php
      // tree expanded if any Artists route is active
      $artistsExpanded = ui_active_link(null, 'admin/artists') 
                      || ui_active_link(null, 'admin/artists/create') 
                      || ui_active_link('artists'); // if your routes are like /admin/artists/...
    ?>
    <li class="treeview <?= $artistsExpanded ? 'is-expanded' : '' ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon bi bi-table"></i>
        <span class="app-menu__label">Artists</span>
        <i class="treeview-indicator bi bi-chevron-right"></i>
      </a>
      <ul class="treeview-menu" <?= $artistsExpanded ? 'style="display:block"' : '' ?>>
        <li>
          <a class="treeview-item <?= ui_active_link(null, 'admin/artists') ?>" href="<?= site_url('admin/artists') ?>">
            <i class="icon bi bi-circle-fill"></i> List
          </a>
        </li>
        <li>
          <a class="treeview-item <?= ui_active_link(null, 'admin/artists/create') ?>" href="<?= site_url('admin/artists/create') ?>">
            <i class="icon bi bi-circle-fill"></i> Create
          </a>
        </li>
      </ul>
    </li>

    <!-- More sections... -->
    <?php
      // Example pattern for another module
      $showsExpanded = ui_active_link(null, 'admin/shows')
                    || ui_active_link(null, 'admin/shows/create')
                    || ui_active_link('shows');
    ?>
    <li class="treeview <?= $showsExpanded ? 'is-expanded' : '' ?>">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon bi bi-music-note"></i>
        <span class="app-menu__label">Shows</span>
        <i class="treeview-indicator bi bi-chevron-right"></i>
      </a>
      <ul class="treeview-menu" <?= $showsExpanded ? 'style="display:block"' : '' ?>>
        <li>
          <a class="treeview-item <?= ui_active_link(null, 'admin/shows') ?>" href="<?= site_url('admin/shows') ?>">
            <i class="icon bi bi-circle-fill"></i> List
          </a>
        </li>
        <li>
          <a class="treeview-item <?= ui_active_link(null, 'admin/shows/create') ?>" href="<?= site_url('admin/shows/create') ?>">
            <i class="icon bi bi-circle-fill"></i> Create
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>
