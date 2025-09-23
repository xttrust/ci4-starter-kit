<header class="app-header">
  <a class="app-header__logo" href="<?= site_url('admin') ?>">Admin</a>

  <!-- Sidebar toggle -->
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>

  <!-- Right menu -->
  <ul class="app-nav">
    <li class="app-search">
      <form method="get" action="<?= site_url('admin/search') ?>">
        <?= csrf_field() ?> 
        
        <input class="app-search__input" type="search" name="q" placeholder="Search">
        <button class="app-search__button" type="submit"><i class="bi bi-search"></i></button>
      </form>
    </li>

    <!-- Notifications (styled) -->
    <li class="dropdown">
      <a class="app-nav__item position-relative" href="#" data-bs-toggle="dropdown" aria-label="Show notifications" id="notifToggle">
        <i class="bi bi-bell fs-5"></i>
        <!-- Badge positioned bottom-end (bottom-right) so it doesn't overflow the top of the page -->
        <span class="position-absolute bottom-0 end-0 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge">0</span>
      </a>

      <ul class="app-notification dropdown-menu dropdown-menu-end p-0" style="min-width: 360px" aria-labelledby="notifToggle">
        <li class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
          <strong class="mb-0" id="notifTitle">Loading…</strong>
          <div>
            <button class="btn btn-sm btn-link" id="notifRefresh" title="Refresh notifications" aria-label="Refresh notifications"><i class="bi bi-arrow-clockwise"></i></button>
          </div>
        </li>

        <li>
          <div id="notifList" class="list-group list-group-flush" style="max-height: 320px; overflow:auto"></div>
        </li>

        <li class="px-3 py-2 border-top">
          <div class="d-flex align-items-center gap-2">
            <button id="notifShowMore" class="btn btn-sm btn-link">Show more</button>
            <div class="ms-auto">
              <form method="post" action="<?= site_url('admin/notifications/mark-read') ?>" class="d-flex align-items-center">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all as read</button>
                <a class="ms-2" href="<?= site_url('admin/activity') ?>">See all</a>
              </form>
            </div>
          </div>
        </li>
      </ul>
    </li>

    <script>
    (function(){
      const endpoint = "<?= site_url('admin/notifications/recent') ?>";
  let lastCheck  = localStorage.getItem('notif_last_check') || null;
  const perPage  = 10; // number of items per page in dropdown (new requirement)
  let offset     = 0; // current offset for "show more"

  const listEl        = document.getElementById('notifList');
  const badgeEl       = document.getElementById('notifBadge');
  const titleEl       = document.getElementById('notifTitle');
  const refreshButton = document.getElementById('notifRefresh');
  const showMoreBtn   = document.getElementById('notifShowMore');

      function createSkeleton() {
        listEl.innerHTML = Array.from({length:3}).map(()=> `
          <div class="list-group-item py-2">
            <div class="placeholder-glow">
              <span class="placeholder col-7"></span>
              <span class="placeholder col-4 mt-2"></span>
            </div>
          </div>
        `).join('');
      }

      function renderItems(items){
        if (!items || items.length === 0) {
          titleEl.textContent = 'No new notifications';
          listEl.innerHTML = '<div class="list-group-item text-muted small">You have no recent notifications.</div>';
          badgeEl.classList.add('d-none');
          return;
        }

        titleEl.textContent = 'Recent activity';
        // Count unread items for badge
        const unreadCount = items.filter(it => !it.is_read).length;
        if (unreadCount > 0) {
          badgeEl.textContent = unreadCount;
          badgeEl.classList.remove('d-none');
        } else {
          badgeEl.classList.add('d-none');
        }

        listEl.innerHTML = '';
          items.forEach(item => {
          const when = new Date(item.created_at.replace(' ', 'T'));
          const ago  = timeAgo(when);

          const el = document.createElement('div');
          el.className = 'list-group-item ' + (item.is_read ? '' : 'fw-semibold');

          const meta = document.createElement('div');
          meta.className = 'small text-muted mb-1';
          meta.textContent = ago;

          const action = document.createElement('div');
          action.innerHTML = escapeHtml(item.action);

          const ip = document.createElement('div');
          ip.className = 'text-muted small';
          ip.textContent = item.ip || '';

          el.appendChild(meta);
          el.appendChild(action);
          el.appendChild(ip);
          listEl.appendChild(el);
        });
      }

      async function loadNotifs({ append = false, loadNewOnly = false } = {}) {
        try {
          createSkeleton();

          // If loading new items since lastCheck, ignore offset and request only latest
          if (loadNewOnly && lastCheck) {
            const url = `${endpoint}?since=${encodeURIComponent(lastCheck)}`;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            if (!res.ok) throw new Error('Failed to fetch');
            const data = await res.json();
            lastCheck = data.now || lastCheck;
            localStorage.setItem('notif_last_check', lastCheck);

            // Prepend new items so newest always appears at the top.
            if (data.items && data.items.length) {
              // Update badge count
              const existing = parseInt(badgeEl.textContent || '0', 10) || 0;
              badgeEl.textContent = existing + data.items.length;
              badgeEl.classList.remove('d-none');

              // Create elements in reverse so the newest (first in array) appears at the top
              data.items.reverse().forEach(item => {
                const when = new Date(item.created_at.replace(' ', 'T'));
                const ago  = timeAgo(when);

                const el = document.createElement('div');
                el.className = 'list-group-item';

                const meta = document.createElement('div');
                meta.className = 'small text-muted mb-1';
                meta.textContent = ago;

                const action = document.createElement('div');
                action.innerHTML = escapeHtml(item.action);

                const ip = document.createElement('div');
                ip.className = 'text-muted small';
                ip.textContent = item.ip || '';

                el.appendChild(meta);
                el.appendChild(action);
                el.appendChild(ip);

                  // Insert at the top of the list
                  listEl.insertBefore(el, listEl.firstChild);
              });
            }

            return;
          }

          const url = `${endpoint}?limit=${perPage}&offset=${offset}`;
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
          if (!res.ok) {
            titleEl.textContent = 'Notifications';
            listEl.innerHTML = '<div class="list-group-item text-danger small">Failed to load notifications.</div>';
            return;
          }

          const data = await res.json();
          lastCheck = data.now || lastCheck;
          localStorage.setItem('notif_last_check', lastCheck);

          if (!append) listEl.innerHTML = '';

          renderItems(data.items);

          // If less than perPage returned, hide show-more button
          if (!data.items || data.items.length < perPage) {
            showMoreBtn?.classList.add('d-none');
          } else {
            showMoreBtn?.classList.remove('d-none');
          }
        } catch (e) {
          console.error('notif error', e);
          titleEl.textContent = 'Notifications';
          listEl.innerHTML = '<div class="list-group-item text-danger small">Error loading notifications.</div>';
        }
      }

      function timeAgo(d){
        const s = Math.floor((Date.now() - d.getTime())/1000);
        if (s < 60) return `${s}s ago`;
        const m = Math.floor(s/60); if (m < 60) return `${m}m ago`;
        const h = Math.floor(m/60); if (h < 24) return `${h}h ago`;
        const dd= Math.floor(h/24); return `${dd}d ago`;
      }

      function escapeHtml(t){
        const div = document.createElement('div');
        div.textContent = t ?? '';
        return div.innerHTML;
      }

      // Wire refresh button (load latest)
      refreshButton?.addEventListener('click', (e)=>{ e.preventDefault(); loadNotifs({ loadNewOnly: true }); });

      // Wire show more button
      showMoreBtn?.addEventListener('click', (e)=>{
        e.preventDefault();
        offset += perPage;
        loadNotifs({ append: true });
      });

      // When opening the dropdown we want to reset pagination.
      // If there are no unread notifications, don't load older/read items —
      // only show a concise 'No unread notifications' message.
      document.getElementById('notifToggle')?.addEventListener('click', ()=>{
        offset = 0;

        const currentCount = parseInt(badgeEl.textContent || '0', 10) || 0;
        // If there are unread notifications, load them. Otherwise show friendly message.
        if (currentCount > 0) {
          loadNotifs();
        } else {
          // Render concise message and hide controls that would load older/read items
          titleEl.textContent = 'No unread notifications';
          listEl.innerHTML = '<div class="list-group-item text-muted small">You have no unread notifications.</div>';
          showMoreBtn?.classList.add('d-none');
          // Keep mark-all button disabled when there's nothing to mark
          const markAllBtn = document.querySelector('#notifList')?.parentElement?.nextElementSibling?.querySelector('button[type="submit"]');
          if (markAllBtn) markAllBtn.disabled = true;
        }
      });

  // Initial fast badge population: fetch unread count first (cheap), then
  // only fetch full items if we have unread items or when the dropdown opens.
  (async function initBadge(){
    try {
      const countRes = await fetch("<?= site_url('admin/notifications/unread-count') ?>", { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (countRes.ok) {
        const j = await countRes.json();
        const c = parseInt(j.count || 0, 10) || 0;
        if (c > 0) {
          badgeEl.textContent = c;
          badgeEl.classList.remove('d-none');
          // Also load the recent items to populate the dropdown cache
          loadNotifs();
        } else {
          // No unread: set lastCheck to now so subsequent 'loadNewOnly' checks work
          const now = new Date().toISOString().slice(0,19).replace('T',' ');
          lastCheck = now;
          localStorage.setItem('notif_last_check', lastCheck);
        }
      } else {
        // Fallback to full load if count endpoint fails
        loadNotifs();
      }
    } catch (e) {
      console.error('notif init error', e);
      loadNotifs();
    }
  })();

  setInterval(()=> loadNotifs({ loadNewOnly: true }), 30000);
    })();
    </script>


    <!-- User menu -->
    <li class="dropdown">
      <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
        <i class="bi bi-person fs-4"></i>
      </a>
      <ul class="dropdown-menu settings-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="<?= site_url('admin/profile') ?>"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
        <li><a class="dropdown-item" href="<?= site_url('admin/settings') ?>"><i class="bi bi-gear me-2 fs-5"></i> Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <?php if (function_exists('auth') && auth()->loggedIn()) : ?>
            <a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a>
          <?php else: ?>
            <a class="dropdown-item" href="<?= site_url('login') ?>"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i> Login</a>
          <?php endif; ?>
        </li>
      </ul>
    </li>
  </ul>
</header>
