<?= $this->extend('layouts/backend') ?>

<?= $this->section('content') ?>
<div class="tile">
  <div class="tile-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <form class="d-flex" method="get" action="<?= site_url('admin/users') ?>">
        <?= csrf_field() ?>
        <input class="form-control me-2" type="search" name="q" placeholder="Search username or email" value="<?= esc($q ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>
      <?php if (auth()->user()?->can('users.create')): ?>
        <a class="btn btn-primary" href="<?= site_url('admin/users/create') ?>"><i class="bi bi-plus-lg me-1"></i> New User</a>
      <?php endif; ?>
    </div>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Status</th>
            <th>Last Active</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (! empty($users)): foreach ($users as $u): ?>
          <tr>
            <td><?= esc($u->id) ?></td>
            <td><?= esc($u->username) ?></td>
            <td>
              <?= ($u->status === 'active') ? badge('Active','success') : badge(ucfirst($u->status ?? 'inactive'),'secondary') ?>
            </td>
            <td><?= esc($u->last_active ?? '-') ?></td>
            <td><?= esc($u->created_at ?? '-') ?></td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                <?php if (auth()->user()?->can('users.edit')): ?>
                  <a class="btn btn-warning" href="<?= site_url('admin/users/'.$u->id.'/edit') ?>"><i class="bi bi-pencil"></i></a>
                  <form method="post" action="<?= site_url('admin/users/'.$u->id.'/toggle') ?>" data-confirm="Toggle status?">
                    <?= csrf_field() ?>
                    <button class="btn btn-secondary" type="submit"><i class="bi bi-power"></i></button>
                  </form>
                <?php endif; ?>
                <?php if (auth()->user()?->can('users.delete')): ?>
                  <form method="post" action="<?= site_url('admin/users/'.$u->id.'/delete') ?>" data-confirm="Delete user #<?= esc($u->id) ?>?">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger" type="submit"><i class="bi bi-trash"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div><?= $pager->links() ?></div>
  </div>
</div>
<?= $this->endSection() ?>
