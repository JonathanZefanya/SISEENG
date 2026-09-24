<?php
$menus = \App\Middleware\RoleMiddleware::getAccessibleMenus();
$currentUrl = trim($_GET['url'] ?? 'admin/dashboard', '/');

// Cocokkan per segmen agar 'admin/jadwal' tidak ikut aktif di 'admin/jadwal-pengkhotbah'
$matches = function (string $path) use ($currentUrl): bool {
    return $currentUrl === $path || strpos($currentUrl, $path . '/') === 0;
};

$unreadCount = (new \App\Models\ContactMessage())->countUnread();
$pageTitle = trim(explode(' - ', $title ?? 'Dashboard')[0]);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <title><?= e($title ?? 'Pengerja Panel') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    <?= themeStyleTag() ?>
</head>

<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo"><i class="bi bi-brightness-high-fill"></i></div>
            <div class="sidebar-brand">
                SISEENG
                <small>Pengerja Panel</small>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar"><?= strtoupper(substr(auth('name'), 0, 1)) ?></div>
            <div class="min-w-0">
                <div class="user-name text-truncate"><?= e(auth('name')) ?></div>
                <div class="user-role"><?= auth('role') === ROLE_SUPER_ADMIN ? 'Super Admin' : 'Administrator' ?></div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>

            <?php foreach ($menus as $menu): ?>
                <?php if (($menu['type'] ?? '') === 'dropdown'):
                    $dropdownActive = false;
                    foreach ($menu['submenu'] as $sub) {
                        if ($matches($sub['active'])) {
                            $dropdownActive = true;
                            break;
                        }
                    }
                    ?>
                    <div class="nav-item nav-dropdown <?= $dropdownActive ? 'open' : '' ?>">
                        <a href="#" class="nav-link nav-dropdown-toggle <?= $dropdownActive ? 'active' : '' ?>">
                            <i class="bi <?= $menu['icon'] ?>"></i>
                            <span><?= e($menu['title']) ?></span>
                            <i class="bi bi-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <?php foreach ($menu['submenu'] as $submenu): ?>
                                <a href="<?= url($submenu['url']) ?>"
                                    class="nav-link nav-submenu-link <?= $matches($submenu['active']) ? 'active' : '' ?>">
                                    <i class="bi <?= $submenu['icon'] ?>"></i>
                                    <span><?= e($submenu['title']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="nav-item">
                        <a href="<?= url($menu['url']) ?>" class="nav-link <?= $matches($menu['active']) ? 'active' : '' ?>">
                            <i class="bi <?= $menu['icon'] ?>"></i>
                            <span><?= e($menu['title']) ?></span>
                            <?php if ($menu['url'] === 'admin/pesan' && $unreadCount > 0): ?>
                                <span class="badge bg-danger"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="sidebar-divider"></div>

            <div class="nav-item">
                <a href="<?= url() ?>" target="_blank" class="nav-link">
                    <i class="bi bi-globe2"></i>
                    <span>Lihat Website</span>
                </a>
            </div>

            <div class="nav-item">
                <form action="<?= url('auth/logout') ?>" method="POST" id="logoutForm">
                    <?= csrfField() ?>
                    <a href="#" class="nav-link text-danger"
                        onclick="document.getElementById('logoutForm').submit(); return false;">
                        <i class="bi bi-box-arrow-left text-danger"></i>
                        <span>Logout</span>
                    </a>
                </form>
            </div>
        </nav>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-info">
                    <div class="page-title-sm"><?= e($pageTitle) ?></div>
                    <nav aria-label="breadcrumb" class="d-none d-md-block">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Admin</a></li>
                            <li class="breadcrumb-item active"><?= e($pageTitle) ?></li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="header-right">
                <span class="header-date">
                    <i class="bi bi-calendar3 me-2"></i><?= formatDate(date('Y-m-d'), 'l, d F Y') ?>
                </span>

                <a href="<?= url('admin/pesan') ?>" class="header-btn position-relative d-none d-lg-grid" aria-label="Pesan">
                    <i class="bi bi-bell"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>

                <div class="dropdown">
                    <button class="header-avatar dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-label="Akun">
                        <?= strtoupper(substr(auth('name'), 0, 1)) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2">
                            <div class="fw-bold"><?= e(auth('name')) ?></div>
                            <small class="text-muted"><?= e(auth('email')) ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= url() ?>" target="_blank">
                                <i class="bi bi-globe2 me-2"></i>Lihat Website
                            </a>
                        </li>
                        <li>
                            <form action="<?= url('auth/logout') ?>" method="POST">
                                <?= csrfField() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="page-content">
            <?php foreach (['success' => 'check-circle-fill', 'error' => 'exclamation-circle-fill', 'warning' => 'exclamation-triangle-fill'] as $type => $icon): ?>
                <?php if (hasFlash($type)): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-<?= $icon ?> fs-5"></i>
                        <div><?= getFlash($type) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?= $content ?>
        </div>
    </main>

    <!-- ===== BOTTOM NAVIGATION (mobile) ===== -->
    <nav class="admin-bottomnav" aria-label="Navigasi admin">
        <a href="<?= url('admin/dashboard') ?>" class="<?= $matches('admin/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-house-door<?= $matches('admin/dashboard') ? '-fill' : '' ?>"></i>Beranda
        </a>
        <a href="<?= url('admin/jadwal-pengkhotbah') ?>" class="<?= $matches('admin/jadwal-pengkhotbah') || $matches('admin/jadwal') ? 'active' : '' ?>">
            <i class="bi bi-calendar-week"></i>Jadwal
        </a>
        <a href="<?= url('admin/kegiatan') ?>" class="<?= $matches('admin/kegiatan') ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i>Kegiatan
        </a>
        <a href="<?= url('admin/pesan') ?>" class="<?= $matches('admin/pesan') ? 'active' : '' ?>">
            <i class="bi bi-envelope<?= $matches('admin/pesan') ? '-fill' : '' ?>"></i>Pesan
            <?php if ($unreadCount > 0): ?><span class="bn-badge"><?= $unreadCount ?></span><?php endif; ?>
        </a>
        <button type="button" id="bottomMenuBtn">
            <i class="bi bi-grid"></i>Menu
        </button>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const main = document.querySelector('.admin-main');
        const isDesktop = () => window.innerWidth >= 992;

        function openDrawer() { sidebar.classList.add('show'); backdrop.classList.add('show'); }
        function closeDrawer() { sidebar.classList.remove('show'); backdrop.classList.remove('show'); }

        // Pulihkan state collapse di desktop
        try {
            if (isDesktop() && localStorage.getItem('sidebarCollapsed') === '1') {
                sidebar.classList.add('collapsed');
                main.classList.add('expanded');
            }
        } catch (e) {}

        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            if (isDesktop()) {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('expanded');
                try {
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
                } catch (e) {}
            } else {
                sidebar.classList.contains('show') ? closeDrawer() : openDrawer();
            }
        });

        document.getElementById('bottomMenuBtn')?.addEventListener('click', openDrawer);
        backdrop?.addEventListener('click', closeDrawer);

        document.querySelectorAll('.admin-sidebar .nav-link:not(.nav-dropdown-toggle)').forEach(link => {
            link.addEventListener('click', () => { if (!isDesktop()) closeDrawer(); });
        });

        document.querySelectorAll('.nav-dropdown-toggle').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                btn.closest('.nav-dropdown').classList.toggle('open');
            });
        });

        // Tabel -> kartu di mobile: beri label tiap sel dari <thead>
        document.querySelectorAll('.page-content table.table:not(.no-stack)').forEach(table => {
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            if (!headers.length) return;
            table.classList.add('table-stack');

            table.querySelectorAll('tbody tr').forEach(row => {
                if (row.querySelector('td[colspan]')) {
                    row.classList.add('row-full');
                    return;
                }
                let col = 0;
                row.querySelectorAll('td').forEach(td => {
                    td.dataset.label = headers[col] || '';
                    col += td.colSpan || 1;
                    const hasMedia = td.querySelector('img, button, a, input, select, form, i');
                    if (!td.textContent.trim() && !hasMedia) td.classList.add('cell-empty');

                    // Bungkus isi sel agar ikon + teks tetap satu blok di sisi kanan
                    const wrap = document.createElement('div');
                    wrap.className = 'cell-value';
                    while (td.firstChild) wrap.appendChild(td.firstChild);
                    td.appendChild(wrap);
                });
            });
        });

        // Auto-dismiss flash setelah 5 detik
        setTimeout(() => {
            document.querySelectorAll('.page-content > .alert-dismissible').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert)?.close();
            });
        }, 5000);

        window.addEventListener('resize', () => { if (isDesktop()) closeDrawer(); });
    </script>
</body>

</html>
