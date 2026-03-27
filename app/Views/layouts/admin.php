<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'VanChuyenHongPhat Admin' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #2d1b69;
            --primary-purple-light: #3d2b7a;
            --accent-color: #7c3aed;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        /* Navbar */
        .top-navbar {
            background-color: var(--primary-purple) !important;
            height: 56px;
            z-index: 1040;
        }
        .top-navbar .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .top-navbar .navbar-brand span {
            background-color: var(--accent-color);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 6px;
            vertical-align: middle;
        }
        .top-navbar .nav-link {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
        }
        .top-navbar .nav-link:hover,
        .top-navbar .nav-link.active {
            color: #fff;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--primary-purple);
            overflow-y: auto;
            z-index: 1030;
            transition: transform 0.3s ease;
        }
        .sidebar .sidebar-heading {
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 20px 20px 8px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 11px 20px;
            font-size: 0.88rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.08);
            border-left-color: var(--accent-color);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
            border-left-color: var(--accent-color);
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 24px;
            min-height: calc(100vh - 56px);
            margin-top: 56px;
        }

        /* Footer */
        .main-footer {
            margin-left: var(--sidebar-width);
            padding: 16px 24px;
            background-color: #fff;
            border-top: 1px solid #e2e8f0;
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 56px;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 1025;
        }

        /* Dark mode */
        [data-bs-theme="dark"] body {
            background-color: #0f172a;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .main-footer {
            background-color: #1e293b;
            border-top-color: #334155;
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .card {
            background-color: #1e293b;
            border-color: #334155;
        }
        [data-bs-theme="dark"] .table {
            --bs-table-bg: #1e293b;
            --bs-table-border-color: #334155;
            --bs-table-striped-bg: #1a2536;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .alert-success {
            background-color: #064e3b;
            border-color: #065f46;
            color: #a7f3d0;
        }
        [data-bs-theme="dark"] .alert-danger {
            background-color: #7f1d1d;
            border-color: #991b1b;
            color: #fecaca;
        }
        [data-bs-theme="dark"] .card-header {
            background-color: #253348;
            border-bottom-color: #334155;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .card-footer {
            background-color: #253348;
            border-top-color: #334155;
        }
        [data-bs-theme="dark"] .card-body {
            color: #cbd5e1;
        }
        [data-bs-theme="dark"] .list-group-item {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .modal-content {
            background-color: #1e293b;
            border-color: #334155;
        }
        [data-bs-theme="dark"] .modal-header {
            border-bottom-color: #334155;
        }
        [data-bs-theme="dark"] .modal-footer {
            border-top-color: #334155;
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b;
            border-color: #334155;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #253348;
            color: #fff;
        }
        [data-bs-theme="dark"] .dropdown-divider {
            border-top-color: #334155;
        }
        [data-bs-theme="dark"] .border,
        [data-bs-theme="dark"] .border-bottom,
        [data-bs-theme="dark"] .border-top {
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .bg-white {
            background-color: #1e293b !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #253348 !important;
        }
        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #e2e8f0 !important;
        }
        [data-bs-theme="dark"] hr {
            border-color: #334155;
        }
        [data-bs-theme="dark"] .breadcrumb {
            --bs-breadcrumb-divider-color: #64748b;
        }
        [data-bs-theme="dark"] .breadcrumb-item a {
            color: #7dd3fc;
        }
        [data-bs-theme="dark"] .breadcrumb-item.active {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .page-link {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .page-link:hover {
            background-color: #253348;
        }
        [data-bs-theme="dark"] .page-item.active .page-link {
            background-color: #7c3aed;
            border-color: #7c3aed;
        }
        [data-bs-theme="dark"] .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0,0,0,0.3) !important;
        }
        [data-bs-theme="dark"] .border-0 {
            border-color: transparent !important;
        }
        [data-bs-theme="dark"] .table-light th,
        [data-bs-theme="dark"] .table-light td {
            background-color: #253348 !important;
            color: #e2e8f0;
            border-color: #334155;
        }
        [data-bs-theme="dark"] thead {
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .bg-opacity-10 {
            background-color: rgba(255,255,255,0.06) !important;
        }
        [data-bs-theme="dark"] .btn-outline-dark {
            color: #94a3b8;
            border-color: #475569;
        }
        [data-bs-theme="dark"] .btn-outline-dark:hover {
            background-color: #334155;
            color: #fff;
            border-color: #475569;
        }
        [data-bs-theme="dark"] .btn-outline-secondary {
            color: #94a3b8;
            border-color: #475569;
        }
        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #334155;
            color: #fff;
        }
        [data-bs-theme="dark"] .small, [data-bs-theme="dark"] small {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .h3, [data-bs-theme="dark"] h3,
        [data-bs-theme="dark"] .h4, [data-bs-theme="dark"] h4,
        [data-bs-theme="dark"] .h5, [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] .h6, [data-bs-theme="dark"] h6 {
            color: #f1f5f9;
        }
        [data-bs-theme="dark"] .input-group-text {
            background-color: #253348;
            border-color: #334155;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .form-check-input {
            background-color: #1e293b;
            border-color: #475569;
        }
        [data-bs-theme="dark"] .nav-tabs .nav-link {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .nav-tabs .nav-link.active {
            background-color: #1e293b;
            border-color: #334155 #334155 #1e293b;
            color: #e2e8f0;
        }
        .theme-toggle {
            cursor: pointer;
            background: none;
            border: none;
            color: rgba(255,255,255,0.85);
            font-size: 1rem;
            padding: 8px;
        }
        .theme-toggle:hover {
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-content,
            .main-footer {
                margin-left: 0;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg top-navbar fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-lg-none me-2 sidebar-toggle" type="button">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="/admin">
                <?php $logo = get_setting('site_logo'); ?>
                <?php if (!empty($logo)): ?>
                    <img src="<?= esc($logo) ?>" alt="Admin" style="max-height:36px; margin-right:8px;">
                <?php endif; ?>
                <?= esc(get_setting('site_name', 'VanChuyenHongPhat')) ?> <span>Admin</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                <i class="fas fa-ellipsis-v text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="fas fa-external-link-alt me-1"></i> Trang nguoi dung</a>
                    </li>
                    <li class="nav-item">
                        <button class="theme-toggle nav-link" id="themeToggle" title="Dark/Light mode">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-1"></i> <?= esc(session()->get('user_name') ?? 'Admin') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-id-card me-2"></i> Ho so</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Dang xuat</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <ul class="nav flex-column">
            <li class="sidebar-heading">Tong quan</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin') || url_is('admin/dashboard*') ? 'active' : '' ?>" href="/admin">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <li class="sidebar-heading">Quan ly</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>" href="/admin/users">
                    <i class="fas fa-users"></i> Quan ly user
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/user-groups*') ? 'active' : '' ?>" href="/admin/user-groups">
                    <i class="fas fa-layer-group"></i> Nhom user
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/cn-warehouse*') ? 'active' : '' ?>" href="<?= site_url('admin/cn-warehouse') ?>">
                    <i class="fas fa-warehouse"></i> Kho Trung Quoc
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/bags*') ? 'active' : '' ?>" href="<?= site_url('admin/bags') ?>">
                    <i class="fas fa-box-open"></i> Dong bao
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/vn-receiving*') ? 'active' : '' ?>" href="<?= site_url('admin/vn-receiving') ?>">
                    <i class="fas fa-dolly"></i> Kho Viet Nam
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/consignments*') ? 'active' : '' ?>" href="/admin/consignments">
                    <i class="fas fa-box"></i> Quan ly don
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/topups*') ? 'active' : '' ?>" href="/admin/topups">
                    <i class="fas fa-plus-circle"></i> Quan ly nap tien
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/withdrawals*') ? 'active' : '' ?>" href="/admin/withdrawals">
                    <i class="fas fa-minus-circle"></i> Quan ly rut tien
                </a>
            </li>

            <li class="sidebar-heading">Noi dung</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/posts*') ? 'active' : '' ?>" href="/admin/posts">
                    <i class="fas fa-newspaper"></i> Bai viet
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/post-categories*') ? 'active' : '' ?>" href="/admin/post-categories">
                    <i class="fas fa-folder"></i> Danh muc tin tuc
                </a>
            </li>

            <li class="sidebar-heading">Van hanh</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/shipping-rates*') ? 'active' : '' ?>" href="<?= site_url('admin/shipping-rates') ?>">
                    <i class="fas fa-tags"></i> Cau hinh gia
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/shipments*') ? 'active' : '' ?>" href="/admin/shipments">
                    <i class="fas fa-shipping-fast"></i> Quan ly chuyen xe
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/deliveries*') ? 'active' : '' ?>" href="/admin/deliveries">
                    <i class="fas fa-truck"></i> Quan ly giao hang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/pickups*') ? 'active' : '' ?>" href="/admin/pickups">
                    <i class="fas fa-people-carry"></i> Quan ly lay hang
                </a>
            </li>

            <li class="sidebar-heading">He thong</li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('admin/settings*') ? 'active' : '' ?>" href="/admin/settings">
                    <i class="fas fa-cog"></i> Cai dat
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer -->
    <footer class="main-footer text-center">
        &copy; <?= date('Y') ?> VanChuyenHongPhat Admin Panel. Tat ca quyen duoc bao luu.
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
            // Sidebar toggle for mobile
            $('.sidebar-toggle').on('click', function() {
                $('#sidebar').toggleClass('show');
                $('#sidebarOverlay').toggleClass('show');
            });
            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('show');
                $(this).removeClass('show');
            });

            // Theme toggle
            var savedTheme = localStorage.getItem('nhc-theme') || 'light';
            $('html').attr('data-bs-theme', savedTheme);
            $('#themeIcon').removeClass('fa-moon fa-sun').addClass(savedTheme === 'dark' ? 'fa-sun' : 'fa-moon');

            $('#themeToggle').on('click', function() {
                var current = $('html').attr('data-bs-theme');
                var next = current === 'dark' ? 'light' : 'dark';
                $('html').attr('data-bs-theme', next);
                localStorage.setItem('nhc-theme', next);
                $('#themeIcon').removeClass('fa-moon fa-sun').addClass(next === 'dark' ? 'fa-sun' : 'fa-moon');
            });

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert-dismissible').alert('close');
            }, 5000);
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
