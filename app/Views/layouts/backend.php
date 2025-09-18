<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="description" content="Vali is a responsive and free admin theme built with Bootstrap 5.">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Admin') ?></title>

  <!-- Main CSS -->
  <link rel="stylesheet" href="<?= base_url('themes/admin/Vali/css/main.css') ?>">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <?= $this->renderSection('styles') ?>
</head>
<body class="app sidebar-mini">

  <!-- Navbar -->
  <?= view('admin/partials/navbar') ?>

  <!-- Sidebar -->
  <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
  <?= view('admin/partials/sidebar') ?>

  <!-- Content -->
  <main class="app-content">
    <?php if (! empty($appTitle) || ! empty($breadcrumbs)): ?>
      <div class="app-title">
        <div>
          <h1><i class="bi bi-speedometer"></i> <?= esc($appTitle ?? ($title ?? '')) ?></h1>
          <?php if (! empty($subtitle)): ?>
            <p><?= esc($subtitle) ?></p>
          <?php endif; ?>
        </div>
        <?php if (! empty($breadcrumbs)): ?>
          <ul class="app-breadcrumb breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
              <li class="breadcrumb-item">
                <?php if (! empty($crumb['url'])): ?>
                  <a href="<?= esc($crumb['url']) ?>"><?= esc($crumb['label']) ?></a>
                <?php else: ?>
                  <?= esc($crumb['label']) ?>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?= view('admin/partials/toasts') ?>

    <?= $this->renderSection('content') ?>
  </main>

  <!-- Core JS (Vali expects jQuery + Bootstrap) -->
  <script src="<?= base_url('themes/admin/Vali/js/jquery-3.7.0.min.js') ?>"></script>
  <script src="<?= base_url('themes/admin/Vali/js/bootstrap.min.js') ?>"></script>
  <script src="<?= base_url('themes/admin/Vali/js/main.js') ?>"></script>

  <?= $this->renderSection('scripts') ?>
</body>
</html>
