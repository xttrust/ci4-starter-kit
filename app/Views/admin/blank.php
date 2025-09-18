<?= $this->extend('layouts/backend') ?>

<?= $this->section('styles') ?>
<!-- Page-specific CSS (optional) -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">Create a beautiful dashboard</div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page-specific JS (optional) -->
<script>
  // Example: console.log('Blank page loaded');
</script>
<?= $this->endSection() ?>
