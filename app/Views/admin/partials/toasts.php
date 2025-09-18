<?php foreach ((array) session('toasts') as $t): ?>
  <div class="alert alert-<?= esc($t['type'] ?? 'info') ?>"><?= esc($t['msg'] ?? '') ?></div>
<?php endforeach; ?>
