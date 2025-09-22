<?= $this->extend('layouts/backend') ?>

<?= $this->section('content') ?>
<div class="tile">
  <div class="tile-body">
    <?php $errors = session('errors') ?? []; ?>
      <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/users/'.$user->id.'/edit') ?>">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" type="text"
               class="form-control <?= isset($errors['username'])?'is-invalid':'' ?>"
               value="<?= esc(old('username',$user->username)) ?>">
        <?php if(isset($errors['username'])): ?>
          <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email"
               class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>"
               value="<?= esc(old('email',$userEmail ?? '')) ?>">
        <?php if(isset($errors['email'])): ?>
          <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
        <?php endif; ?>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">New Password (leave blank to keep)</label>
          <input name="password" type="password"
                 class="form-control <?= isset($errors['password'])?'is-invalid':'' ?>">
          <?php if(isset($errors['password'])): ?>
            <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
          <?php endif; ?>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Confirm Password</label>
          <input name="password_confirm" type="password"
                 class="form-control <?= isset($errors['password_confirm'])?'is-invalid':'' ?>">
          <?php if(isset($errors['password_confirm'])): ?>
            <div class="invalid-feedback"><?= esc($errors['password_confirm']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Group</label>
        <select name="group" class="form-select">
          <option value="">— leave as is —</option>
          <?php $currentGroup = old('group') ?: ($user->getGroups()[0] ?? ''); ?>
          <?php foreach (array_keys(config('AuthGroups')->groups ?? []) as $group): ?>
            <option value="<?= esc($group) ?>" <?= $currentGroup === $group ? 'selected' : '' ?>>
              <?= esc($group) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small class="text-muted">
          If selected, user will be removed from other groups and added to this one.
        </small>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check-lg me-1"></i> Save
        </button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">Back</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
