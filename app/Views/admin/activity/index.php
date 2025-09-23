<?= $this->extend('layouts/backend') ?>

<?= $this->section('content') ?>
<div class="tile">
  <div class="tile-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Activity Log</h3>
      <div>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/activity') ?>">Refresh</a>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/activity/purge') ?>">
      <?= csrf_field() ?>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th style="width:1%"><input type="checkbox" id="selectAll"></th>
              <th>User</th>
              <th>Action</th>
              <th>IP</th>
              <th>Agent</th>
              <th>When</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($activities)): ?>
              <tr><td colspan="7" class="text-center text-muted">No activity recorded.</td></tr>
            <?php else: foreach ($activities as $a): ?>
              <?php $isRecent = (time() - strtotime($a['created_at'])) < 86400; ?>
              <tr class="<?= $isRecent ? '' : 'table-light text-muted' ?>">
                <td><input type="checkbox" name="ids[]" value="<?= esc($a['id']) ?>"></td>
                <td><?= esc($a['user_id'] ?? 'System') ?></td>
                <td><?= esc($a['action']) ?></td>
                <td><?= esc($a['ip_address']) ?></td>
                <td class="text-truncate" style="max-width:220px"><?= esc($a['user_agent']) ?></td>
                <td><?= esc($a['created_at']) ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <form method="post" action="<?= site_url('admin/activity/delete/'.$a['id']) ?>" data-confirm="Delete this entry?" data-ajax="1">
                      <?= csrf_field() ?>
                      <button class="btn btn-danger" type="submit"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex align-items-center">
        <div>
          <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete selected activity entries?">Delete selected</button>
        </div>
        <div class="ms-auto">
          <?= $pager->links() ?>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<script>
document.getElementById('selectAll')?.addEventListener('change', function(e){
  const checked = e.target.checked;
  document.querySelectorAll('input[name="ids[]"]').forEach(i => i.checked = checked);
});
// Small utility to show an inline toast for AJAX actions
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

// Intercept delete forms and send AJAX
document.querySelectorAll('form[action*="/admin/activity/delete"]').forEach(f => {
    f.addEventListener('submit', async function(e){
    if (!this.dataset.ajax) return true; // allow normal submit if not marked
    e.preventDefault();
    const msg = this.dataset.confirm || 'Confirm?';
    const ok = await confirmModal({ title: 'Confirm', body: msg, confirmText: 'Delete' });
    if (!ok) return;
    const headers = {'X-Requested-With':'XMLHttpRequest'};
    if (window.CSRF && window.CSRF.header) headers[window.CSRF.header] = window.CSRF.hash;
    fetch(this.action, { method: 'POST', headers, body: new FormData(this) })
      .then(r=>r.json()).then(j=>{
        if (j.ok) {
          showToast(j.message || 'Deleted');
          // Remove row
          this.closest('tr')?.remove();
        } else {
          showToast('Failed', 'danger');
        }
      }).catch(()=>showToast('Error', 'danger'));
  });
});
</script>
