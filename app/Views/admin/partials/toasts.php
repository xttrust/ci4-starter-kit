<?php
$success     = session()->getFlashdata('success');
$error       = session()->getFlashdata('error');
$info        = session()->getFlashdata('info');
$errorsArray = session('errors') ?? [];
?>

<?php if ($success || $error || $info || !empty($errorsArray)): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">

  <?php if ($success): ?>
    <div class="toast text-bg-success border-0" role="alert" data-bs-autohide="true" data-bs-delay="4000">
      <div class="d-flex">
        <div class="toast-body"><?= esc($success) ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="toast text-bg-danger border-0" role="alert" data-bs-autohide="true" data-bs-delay="6000">
      <div class="d-flex">
        <div class="toast-body"><?= esc($error) ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($info): ?>
    <div class="toast text-bg-info border-0" role="alert" data-bs-autohide="true" data-bs-delay="4000">
      <div class="d-flex">
        <div class="toast-body"><?= esc($info) ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($errorsArray): ?>
    <div class="toast text-bg-warning border-0" role="alert" data-bs-autohide="true" data-bs-delay="8000">
      <div class="d-flex">
        <div class="toast-body">
          <strong>Validation errors:</strong>
          <ul class="mb-0 ps-3">
            <?php foreach ($errorsArray as $msg): ?>
              <li><?= esc($msg) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php endif; ?>

</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.toast').forEach(el => new bootstrap.Toast(el).show());
});
</script>
