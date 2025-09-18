<?php if (!empty($breadcrumbs)) : ?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <?php foreach ($breadcrumbs as $i => $b): ?>
      <?php if (!empty($b['url']) && $i < count($breadcrumbs)-1): ?>
        <li class="breadcrumb-item"><a href="<?= esc($b['url']) ?>"><?= esc($b['label']) ?></a></li>
      <?php else: ?>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($b['label']) ?></li>
      <?php endif ?>
    <?php endforeach ?>
  </ol>
</nav>
<?php endif ?>
