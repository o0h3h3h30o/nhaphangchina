<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'VanChuyenHongPhat - Dang nhap' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a73e8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-brand h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 4px;
        }
        .auth-brand p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
        }
        .auth-brand .brand-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-blue);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .auth-brand .brand-icon i {
            font-size: 1.75rem;
            color: #fff;
        }

        .auth-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        .btn-primary:hover {
            background-color: #1557b0;
            border-color: #1557b0;
        }

        a {
            color: var(--primary-blue);
        }

        /* Dark mode */
        [data-bs-theme="dark"] body {
            background-color: #0f172a;
        }
        [data-bs-theme="dark"] .auth-card {
            background-color: #1e293b;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }
        [data-bs-theme="dark"] .auth-brand p {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .form-control {
            background-color: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        .theme-toggle-auth {
            position: fixed;
            top: 16px;
            right: 16px;
            cursor: pointer;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            color: #64748b;
            font-size: 1rem;
            padding: 8px 10px;
            border-radius: 8px;
            backdrop-filter: blur(8px);
        }
        .theme-toggle-auth:hover {
            background: rgba(255,255,255,0.2);
            color: #1a73e8;
        }
        [data-bs-theme="dark"] .theme-toggle-auth {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .theme-toggle-auth:hover {
            color: #7dd3fc;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <button class="theme-toggle-auth" id="themeToggle" title="Dark/Light mode">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <div class="auth-wrapper">
        <!-- Brand -->
        <div class="auth-brand">
            <div class="brand-icon">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <h1>VanChuyenHongPhat</h1>
            <p>Dich vu nhap hang Trung Quoc</p>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Auth Card -->
        <div class="auth-card">
            <?= $this->renderSection('content') ?>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            &copy; <?= date('Y') ?> VanChuyenHongPhat. Tat ca quyen duoc bao luu.
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
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
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
