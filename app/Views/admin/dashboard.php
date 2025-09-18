<?= $this->extend('layouts/backend') ?>

<?= $this->section('styles') ?>
<!-- Page-specific CSS -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        Welcome to your dashboard content.
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page-specific JS -->
<script>
  console.log('Dashboard loaded');
</script>
<?= $this->endSection() ?>
