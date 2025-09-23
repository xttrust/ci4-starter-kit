<?= $this->extend('layouts/backend') ?>

<?= $this->section('content') ?>
<div class="tile">
  <div class="tile-body">
    <h3><?= isset($notification) ? 'Edit' : 'Create' ?> Notification</h3>

    <?php $errors = session('errors') ?? []; ?>

    <form method="post" action="<?= site_url('admin/notifications/save') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= isset($notification) ? esc($notification['id']) : '' ?>">

      <div class="mb-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control <?= isset($errors['type']) ? 'is-invalid' : '' ?>" value="<?= esc(old('type', $notification['type'] ?? 'info')) ?>">
        <?php if (isset($errors['type'])): ?><div class="invalid-feedback"><?= esc($errors['type']) ?></div><?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= esc(old('title', $notification['title'] ?? '')) ?>">
        <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= esc($errors['title']) ?></div><?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Body</label>
        <textarea name="body" class="form-control <?= isset($errors['body']) ? 'is-invalid' : '' ?>" rows="4"><?= esc(old('body', $notification['body'] ?? '')) ?></textarea>
        <?php if (isset($errors['body'])): ?><div class="invalid-feedback"><?= esc($errors['body']) ?></div><?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">User ID (optional)</label>
        <input name="user_id" class="form-control <?= isset($errors['user_id']) ? 'is-invalid' : '' ?>" value="<?= esc(old('user_id', $notification['user_id'] ?? '')) ?>">
        <?php if (isset($errors['user_id'])): ?><div class="invalid-feedback"><?= esc($errors['user_id']) ?></div><?php endif; ?>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary"><?= isset($notification) ? 'Update' : 'Create' ?></button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/notifications') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
