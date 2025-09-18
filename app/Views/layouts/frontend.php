<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= esc($title ?? 'Site') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?= csrf_meta() ?>
  <link rel="stylesheet" href="<?= base_url('assets/site.css') ?>">
  <?= $this->renderSection('styles') ?>
</head>
<body>
  <?= view('frontend/partials/headerbar') ?>
  <main class="container py-4">
    <?= $this->renderSection('content') ?>
  </main>
  <?= view('frontend/partials/footerbar') ?>

  <script src="<?= base_url('assets/site.js') ?>"></script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
