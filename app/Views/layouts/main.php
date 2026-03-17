<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'VanChuyenHongPhat' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a73e8;
            --sidebar-dark: #1e293b;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        /* Navbar */
        .top-navbar {
            background-color: var(--primary-blue) !important;
            height: 56px;
            z-index: 1040;
        }
        .top-navbar .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .top-navbar .nav-link {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
        }
        .top-navbar .nav-link:hover,
        .top-navbar .nav-link.active {
            color: #fff;
        }
        .top-navbar .dropdown-toggle::after {
            color: #fff;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--sidebar-dark);
            overflow-y: auto;
            z-index: 1030;
            transition: transform 0.3s ease;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.08);
            border-left-color: var(--primary-blue);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
            border-left-color: var(--primary-blue);
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
        [data-bs-theme="dark"] .page-link {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .page-link:hover {
            background-color: #253348;
        }
        [data-bs-theme="dark"] .page-item.active .page-link {
            background-color: #1a73e8;
            border-color: #1a73e8;
        }
        [data-bs-theme="dark"] .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0,0,0,0.3) !important;
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
            <a class="navbar-brand" href="/">VanChuyenHongPhat</a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="fas fa-ellipsis-v text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto d-none d-lg-flex">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard"><i class="fas fa-home me-1"></i> Tong quan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/consignments"><i class="fas fa-box me-1"></i> Don ky gui</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wallet"><i class="fas fa-wallet me-1"></i> Vi tien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/deliveries"><i class="fas fa-truck me-1"></i> Giao hang</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="theme-toggle nav-link" id="themeToggle" title="Dark/Light mode">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </li>
                    <?php if (session()->get('user_role') === 'admin' || session()->get('user_role') === 'staff'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin"><i class="fas fa-cog me-1"></i> Quan tri</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= esc(session()->get('user_name') ?? 'Tai khoan') ?>
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
        <ul class="nav flex-column pt-3">
            <li class="nav-item">
                <a class="nav-link <?= url_is('dashboard*') ? 'active' : '' ?>" href="/dashboard">
                    <i class="fas fa-home"></i> Tong quan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('consignments*') ? 'active' : '' ?>" href="/consignments">
                    <i class="fas fa-box"></i> Don ky gui
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('wallet*') ? 'active' : '' ?>" href="/wallet">
                    <i class="fas fa-wallet"></i> Vi tien
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('topup*') ? 'active' : '' ?>" href="/topup">
                    <i class="fas fa-plus-circle"></i> Nap tien
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('withdrawal*') ? 'active' : '' ?>" href="/withdrawal">
                    <i class="fas fa-minus-circle"></i> Rut tien
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('bank-accounts*') ? 'active' : '' ?>" href="/bank-accounts">
                    <i class="fas fa-university"></i> Tai khoan ngan hang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('deliveries*') ? 'active' : '' ?>" href="/deliveries">
                    <i class="fas fa-truck"></i> Giao hang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= url_is('pickup*') ? 'active' : '' ?>" href="/pickup">
                    <i class="fas fa-hand-holding-box"></i> Lay hang
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
        &copy; <?= date('Y') ?> VanChuyenHongPhat. Tat ca quyen duoc bao luu.
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
