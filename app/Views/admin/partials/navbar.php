<header class="app-header">
  <a class="app-header__logo" href="<?= site_url('admin') ?>">Admin</a>

  <!-- Sidebar toggle -->
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>

  <!-- Right menu -->
  <ul class="app-nav">
    <li class="app-search">
      <form method="get" action="<?= site_url('admin/search') ?>">
        <input class="app-search__input" type="search" name="q" placeholder="Search">
        <button class="app-search__button" type="submit"><i class="bi bi-search"></i></button>
      </form>
    </li>

    <!-- Notifications (stub) -->
    <li class="dropdown">
      <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Show notifications">
        <i class="bi bi-bell fs-5"></i>
      </a>
      <ul class="app-notification dropdown-menu dropdown-menu-end">
        <li class="app-notification__title">No new notifications</li>
        <li class="app-notification__footer"><a href="#">See all notifications.</a></li>
      </ul>
    </li>

    <!-- User menu -->
    <li class="dropdown">
      <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
        <i class="bi bi-person fs-4"></i>
      </a>
      <ul class="dropdown-menu settings-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="<?= site_url('admin/profile') ?>"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
        <li><a class="dropdown-item" href="<?= site_url('admin/settings') ?>"><i class="bi bi-gear me-2 fs-5"></i> Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <?php if (function_exists('auth') && auth()->loggedIn()) : ?>
            <a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a>
          <?php else: ?>
            <a class="dropdown-item" href="<?= site_url('login') ?>"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i> Login</a>
          <?php endif; ?>
        </li>
      </ul>
    </li>
  </ul>
</header>
