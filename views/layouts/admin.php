<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Panel') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-color: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --sidebar-bg: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            --body-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
            --card-shadow-hover: 0 10px 40px -15px rgba(0,0,0,0.15);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--body-bg);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* ===== SIDEBAR ===== */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        
        .admin-sidebar::-webkit-scrollbar { width: 5px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
        
        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .sidebar-brand small {
            display: block;
            font-size: 0.7rem;
            font-weight: 400;
            opacity: 0.7;
        }
        
        .sidebar-user {
            padding: 1.25rem;
            margin: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            font-weight: 600;
        }
        
        .user-name { color: white; font-weight: 600; font-size: 0.95rem; }
        .user-role { font-size: 0.75rem; color: rgba(255,255,255,0.6); }
        
        .sidebar-nav { padding: 0.5rem 1rem; }
        
        .nav-section {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.4);
            padding: 1rem 0.75rem 0.5rem;
        }
        
        .nav-item { margin-bottom: 4px; }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover { color: white; background: rgba(255,255,255,0.1); }
        .nav-link.active { color: white; background: var(--primary-color); box-shadow: 0 4px 15px -3px rgba(79, 70, 229, 0.5); }
        .nav-link i { font-size: 1.15rem; width: 24px; text-align: center; }
        .nav-link .badge { margin-left: auto; font-size: 0.65rem; padding: 0.35em 0.65em; }
        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.1); margin: 1rem 0; }
        
        /* ===== MAIN CONTENT ===== */
        .admin-main { margin-left: var(--sidebar-width); min-height: 100vh; transition: margin-left 0.3s ease; }
        
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header-left { display: flex; align-items: center; gap: 1rem; }
        
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
        }
        
        .sidebar-toggle:hover { background: #f1f5f9; color: var(--primary-color); }
        .page-info h1 { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; }
        .page-info .breadcrumb { margin: 0; font-size: 0.8rem; }
        .page-info .breadcrumb-item a { color: #64748b; text-decoration: none; }
        .header-right { display: flex; align-items: center; gap: 0.75rem; }
        .header-date { color: #64748b; font-size: 0.875rem; display: none; }
        
        .header-btn {
            width: 42px;
            height: 42px;
            border: none;
            background: #f1f5f9;
            border-radius: 10px;
            color: #64748b;
            font-size: 1.15rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-btn:hover { background: var(--primary-color); color: white; }
        
        .page-content { padding: 1.5rem; }
        
        /* ===== CARDS ===== */
        .card { border: none; border-radius: 16px; box-shadow: var(--card-shadow); transition: all 0.3s ease; }
        .card:hover { box-shadow: var(--card-shadow-hover); }
        .card-header { background: white; border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem; border-radius: 16px 16px 0 0 !important; }
        .card-body { padding: 1.5rem; }
        .card-title { font-weight: 700; color: #1e293b; margin: 0; }
        
        /* ===== STAT CARDS ===== */
        .stat-card { border-radius: 16px; padding: 1.5rem; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); }
        .stat-card.bg-primary { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important; }
        .stat-card.bg-success { background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important; }
        .stat-card.bg-warning { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%) !important; }
        .stat-card.bg-info { background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important; }
        .stat-card.bg-danger { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%) !important; }
        .stat-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white; }
        .stat-value { font-size: 2rem; font-weight: 700; color: white; line-height: 1.2; }
        .stat-label { color: rgba(255,255,255,0.85); font-size: 0.9rem; font-weight: 500; }
        
        /* ===== TABLES ===== */
        .table { margin: 0; }
        .table th { background: #f8fafc; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; color: #64748b; padding: 1rem 1.25rem; border: none; }
        .table td { padding: 1rem 1.25rem; vertical-align: middle; border-color: #f1f5f9; color: #334155; }
        .table tbody tr { transition: background 0.2s; }
        .table tbody tr:hover { background: #f8fafc; }
        
        /* ===== BUTTONS ===== */
        .btn { font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 10px; transition: all 0.2s; }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); }
        .btn-sm { padding: 0.5rem 0.875rem; font-size: 0.8rem; }
        .btn-icon { width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
        
        /* ===== FORMS ===== */
        .form-label { font-weight: 600; color: #334155; font-size: 0.875rem; margin-bottom: 0.5rem; }
        .form-control, .form-select { border-radius: 10px; border-color: #e2e8f0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.2s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); }
        
        /* ===== BADGES ===== */
        .badge { font-weight: 600; padding: 0.5em 0.85em; border-radius: 6px; }
        
        /* ===== ALERTS ===== */
        .alert { border: none; border-radius: 12px; padding: 1rem 1.25rem; }
        .alert-success { background: #ecfdf5; color: #065f46; }
        .alert-danger { background: #fef2f2; color: #991b1b; }
        .alert-warning { background: #fffbeb; color: #92400e; }
        
        /* ===== PAGINATION ===== */
        .pagination { gap: 0.25rem; }
        .page-link { border-radius: 8px !important; border: none; padding: 0.5rem 0.875rem; color: #64748b; font-weight: 500; }
        .page-item.active .page-link { background: var(--primary-color); }
        
        /* ===== RESPONSIVE ===== */
        @media (min-width: 1200px) { .header-date { display: block; } }
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .sidebar-toggle { display: flex; }
            .sidebar-backdrop { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; opacity: 0; visibility: hidden; transition: all 0.3s; }
            .sidebar-backdrop.show { opacity: 1; visibility: visible; }
        }
        @media (max-width: 575.98px) { .page-content { padding: 1rem; } .stat-card { padding: 1.25rem; } .stat-value { font-size: 1.5rem; } }
    </style>
</head>
<body>
    <!-- Sidebar Backdrop (Mobile) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    
    <!-- ===== SIDEBAR ===== -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-building"></i>
            </div>
            <div class="sidebar-brand">
                GBI Ciseeng
                <small>Admin Panel</small>
            </div>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">
                <?= strtoupper(substr(auth('name'), 0, 1)) ?>
            </div>
            <div>
                <div class="user-name"><?= e(auth('name')) ?></div>
                <div class="user-role"><?= auth('role') === ROLE_SUPER_ADMIN ? 'Super Admin' : 'Administrator' ?></div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>
            
            <?php 
            $menus = \App\Middleware\RoleMiddleware::getAccessibleMenus();
            $currentUrl = $_GET['url'] ?? 'admin/dashboard';
            foreach ($menus as $menu): 
                $isActive = strpos($currentUrl, $menu['active']) === 0;
            ?>
            <div class="nav-item">
                <a href="<?= url($menu['url']) ?>" class="nav-link <?= $isActive ? 'active' : '' ?>">
                    <i class="bi <?= $menu['icon'] ?>"></i>
                    <span><?= e($menu['title']) ?></span>
                    <?php if ($menu['title'] === 'Pesan Masuk'): 
                        $messageModel = new \App\Models\ContactMessage();
                        $unreadCount = $messageModel->countUnread();
                        if ($unreadCount > 0): ?>
                        <span class="badge bg-danger"><?= $unreadCount ?></span>
                    <?php endif; endif; ?>
                </a>
            </div>
            <?php endforeach; ?>
            
            <div class="sidebar-divider"></div>
            
            <div class="nav-item">
                <a href="<?= url() ?>" target="_blank" class="nav-link">
                    <i class="bi bi-globe"></i>
                    <span>Lihat Website</span>
                </a>
            </div>
            
            <div class="nav-item">
                <form action="<?= url('auth/logout') ?>" method="POST" id="logoutForm">
                    <?= csrfField() ?>
                    <a href="#" class="nav-link" onclick="document.getElementById('logoutForm').submit(); return false;">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </a>
                </form>
            </div>
        </nav>
    </aside>
    
    <!-- ===== MAIN CONTENT ===== -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-info d-none d-md-block">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Admin</a></li>
                            <li class="breadcrumb-item active"><?= e($title ?? 'Dashboard') ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="header-right">
                <span class="header-date">
                    <i class="bi bi-calendar3 me-1"></i>
                    <?= formatDate(date('Y-m-d'), 'l, d F Y') ?>
                </span>
                
                <div class="dropdown">
                    <button class="header-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2">
                            <div class="fw-semibold"><?= e(auth('name')) ?></div>
                            <small class="text-muted"><?= e(auth('email')) ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
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
            <?php if (hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><?= getFlash('success') ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                <div><?= getFlash('error') ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (hasFlash('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div><?= getFlash('warning') ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?= $content ?>
        </div>
    </main>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggle = document.getElementById('sidebarToggle');
        
        toggle?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });
        
        backdrop?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
        
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                if (bootstrap.Alert.getInstance(alert)) {
                    bootstrap.Alert.getInstance(alert).close();
                }
            });
        }, 5000);
    </script>
</body>
</html>
