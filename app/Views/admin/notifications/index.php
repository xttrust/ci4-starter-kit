<?= $this->extend('layouts/backend') ?>

<?= $this->section('content') ?>
<div class="tile">
  <div class="tile-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Notifications</h3>
      <div>
        <?php if (auth()->user()?->can('notifications.manage')): ?>
          <a class="btn btn-primary" href="<?= site_url('admin/notifications/form') ?>"><i class="bi bi-plus-lg me-1"></i> New</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <form method="get" class="row g-2 mb-3">
      <div class="col-md-4">
        <input name="q" value="<?= esc($this->request->getGet('q')) ?>" class="form-control" placeholder="Search title or body">
      </div>
      <div class="col-auto">
        <select name="type" class="form-select">
          <option value="">All types</option>
          <option value="info">Info</option>
          <option value="warning">Warning</option>
          <option value="activity">Activity</option>
        </select>
      </div>
      <div class="col-auto">
        <select name="unread" class="form-select">
          <option value="">All</option>
          <option value="1">Unread</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary">Filter</button>
        <a class="btn btn-outline-secondary ms-1" href="<?= site_url('admin/notifications') ?>">Clear</a>
      </div>
    </form>

    <form method="post" action="<?= site_url('admin/notifications/purge') ?>">
      <?= csrf_field() ?>
      <div class="mb-2 d-flex gap-2">
        <button formaction="<?= site_url('admin/notifications/mark-read-selected') ?>" class="btn btn-secondary btn-sm">Mark selected as read</button>
        <button type="submit" class="btn btn-danger btn-sm">Delete selected</button>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th style="width:1%"><input id="selectAll" type="checkbox"></th>
              <th>ID</th>
              <th>Type</th>
              <th>Title</th>
              <th>Body</th>
              <th>User</th>
              <th>Created</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($notifications as $n): ?>
              <?php $isUnread = empty($n['is_read']) && (!empty($last_read_at) ? strtotime($n['created_at']) > strtotime($last_read_at) : true); ?>
              <tr class="<?= $isUnread ? 'table-warning' : 'table-light text-muted' ?>">
                <td><input name="ids[]" value="<?= esc($n['id']) ?>" type="checkbox"></td>
                <td><?= esc($n['id']) ?></td>
                <td><?= esc($n['type']) ?></td>
                <td><?= esc($n['title']) ?></td>
                <td><?= esc($n['body']) ?></td>
                <td><?= esc($n['user_id']) ?></td>
                <td><?= esc($n['created_at']) ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <form method="post" action="<?= site_url('admin/notifications/mark-as-read/'.$n['id']) ?>" data-ajax="1" data-confirm="Mark this notification as read?">
                      <?= csrf_field() ?>
                      <button class="btn btn-outline-success">Mark read</button>
                    </form>
                    <form method="post" action="<?= site_url('admin/notifications/delete/'.$n['id']) ?>" onsubmit="return false" data-ajax="1" data-confirm="Delete this notification?">
                      <?= csrf_field() ?>
                      <button class="btn btn-outline-danger">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?= isset($pager) ? $pager->links() : '' ?>
    </form>
  </div>
</div>

<script>
  document.getElementById('selectAll')?.addEventListener('change', function(e){
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
  });

// AJAX helper and handlers
function showToast(message, type = 'success'){
  const container = document.querySelector('.toast-container') || (function(){
    const c = document.createElement('div'); c.className='toast-container position-fixed bottom-0 end-0 p-3'; document.body.appendChild(c); return c;
  })();
  const toast = document.createElement('div');
  toast.className = `toast text-bg-${type} border-0`;
  toast.setAttribute('role','alert');
  toast.setAttribute('data-bs-autohide','true');
  toast.setAttribute('data-bs-delay','4000');
  toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
  container.appendChild(toast);
  new bootstrap.Toast(toast).show();
}

document.querySelectorAll('form[data-ajax="1"]').forEach(f => {
  f.addEventListener('submit', async function(e){
    e.preventDefault();
    const msg = this.dataset.confirm || 'Confirm?';
    const ok = await confirmModal({ title: 'Confirm', body: msg, confirmText: 'OK' });
    if (!ok) return;
    const headers = {'X-Requested-With':'XMLHttpRequest'};
    if (window.CSRF && window.CSRF.header) headers[window.CSRF.header] = window.CSRF.hash;
    fetch(this.action, { method: 'POST', headers, body: new FormData(this) })
      .then(r=>r.json()).then(j=>{
        if (j.ok) {
          showToast(j.message || 'Done');
          // remove row if delete
          if (this.action.includes('/delete')) this.closest('tr')?.remove();
        } else {
          showToast('Failed', 'danger');
        }
      }).catch(()=>showToast('Error', 'danger'));
  });
});
</script>

<?= $this->endSection() ?>
