<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="description" content="<?= esc($title ?? 'Admin') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Admin') ?></title>

  <!-- Main CSS -->
  <link rel="stylesheet" href="<?= base_url('themes/admin/Vali/css/main.css') ?>">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <?= $this->renderSection('styles') ?>
</head>
<body class="app sidebar-mini">

  <!-- Navbar -->
  <?= view('admin/partials/navbar') ?>

  <!-- Sidebar -->
  <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
  <?= view('admin/partials/sidebar') ?>

  <!-- Content -->
  <main class="app-content">
    <?php if (! empty($appTitle) || ! empty($breadcrumbs)): ?>
      <div class="app-title">
        <div>
          <h1><i class="bi bi-speedometer"></i> <?= esc($appTitle ?? ($title ?? '')) ?></h1>
          <?php if (! empty($subtitle)): ?>
            <p><?= esc($subtitle) ?></p>
          <?php endif; ?>
        </div>
        <?php if (! empty($breadcrumbs)): ?>
          <ul class="app-breadcrumb breadcrumb">
            <?php foreach ($breadcrumbs as $crumb): ?>
              <li class="breadcrumb-item">
                <?php if (! empty($crumb['url'])): ?>
                  <a href="<?= esc($crumb['url']) ?>"><?= esc($crumb['label']) ?></a>
                <?php else: ?>
                  <?= esc($crumb['label']) ?>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?= view('admin/partials/toasts') ?>

    <?= $this->renderSection('content') ?>
  </main>

  <!-- Core JS (Vali expects jQuery + Bootstrap) -->
  <script src="<?= base_url('themes/admin/Vali/js/jquery-3.7.0.min.js') ?>"></script>
  <script src="<?= base_url('themes/admin/Vali/js/bootstrap.min.js') ?>"></script>
  <script src="<?= base_url('themes/admin/Vali/js/main.js') ?>"></script>

  <?= $this->renderSection('scripts') ?>
  <script>
  // Expose CSRF token details to client-side JS for robust AJAX requests
  window.CSRF = {
    tokenName: '<?= csrf_token() ?>',
    hash: '<?= csrf_hash() ?>',
    header: '<?= esc(config('Security')->csrfHeader ?? 'X-CSRF-Token') ?>'
  };

  /**
   * confirmModal(options) -> Promise
   * options: { title, body, confirmText }
   * Resolves true if confirmed, false otherwise.
   */
  function confirmModal(opts){
    return new Promise((resolve)=>{
      const title = opts.title || 'Confirm';
      const body  = opts.body || 'Are you sure?';
      const confirmText = opts.confirmText || 'Confirm';

      let modalEl = document.getElementById('appConfirmModal');
      if (!modalEl) {
        modalEl = document.createElement('div');
        modalEl.innerHTML = `
          <div class="modal fade" id="appConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">${title}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">${body}</div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-danger" id="appConfirmOk">${confirmText}</button>
                </div>
              </div>
            </div>
          </div>`;
        document.body.appendChild(modalEl);
        modalEl = document.getElementById('appConfirmModal');
      }

      // Update contents
      modalEl.querySelector('.modal-title').textContent = title;
      modalEl.querySelector('.modal-body').textContent = body;
      modalEl.querySelector('#appConfirmOk').textContent = confirmText;

      const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });
      const okBtn = modalEl.querySelector('#appConfirmOk');

      const onOk = () => { cleanup(); resolve(true); };
      const onHide = () => { cleanup(); resolve(false); };
      function cleanup(){
        okBtn.removeEventListener('click', onOk);
        modalEl.removeEventListener('hidden.bs.modal', onHide);
        try{ bsModal.hide(); } catch(e){}
      }

      okBtn.addEventListener('click', onOk);
      modalEl.addEventListener('hidden.bs.modal', onHide);
      bsModal.show();
    });
  }

  // Global handler: any form with data-confirm attribute will show confirmModal before submitting
  document.addEventListener('submit', async function(e){
    const form = e.target;
    if (form && form.tagName === 'FORM' && form.dataset.confirm) {
      e.preventDefault();
      const ok = await confirmModal({ title: 'Confirm', body: form.dataset.confirm, confirmText: 'OK' });
      if (ok) form.submit();
    }
  }, true);

  setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el => {
      if (el.classList.contains('show')) el.classList.remove('show');
    });
  }, 4000);
</script>
</body>
</html>
