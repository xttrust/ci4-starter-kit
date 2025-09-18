<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>
  <h1 class="display-5 mb-3"><?= esc($headline ?? 'Welcome') ?></h1>
  <p class="lead"><?= esc($tagline ?? 'Your tagline here.') ?></p>
<?= $this->endSection() ?>
