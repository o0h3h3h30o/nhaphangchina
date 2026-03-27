<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($post['title']) ?> - VanChuyenHongPhat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-red: #d32f2f; --dark-bg: #2c2c2c; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        .top-bar { background: var(--dark-bg); padding: 10px 0; }
        .top-bar a { color: #fff; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .top-bar a:hover { color: var(--primary-red); }
        .top-bar .brand { color: var(--primary-red); font-weight: 900; font-size: 1.2rem; }
        .page-header { background: linear-gradient(135deg, var(--primary-red), #ff6659); padding: 40px 0; color: #fff; }
        .page-header h1 { font-weight: 800; font-size: 1.8rem; }
        .post-content { font-size: 1rem; line-height: 1.8; }
        .post-content img { max-width: 100%; height: auto; border-radius: 8px; }
        .footer-bar { background: var(--dark-bg); color: #999; padding: 16px 0; text-align: center; font-size: 0.85rem; }
    </style>
</head>
<body>

    <nav class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="brand"><i class="fas fa-truck me-2"></i>VANCHUYENHONGPHAT</a>
            <div>
                <a href="/" class="me-3"><i class="fas fa-home me-1"></i>Trang chủ</a>
                <a href="/auth/login"><i class="fas fa-sign-in-alt me-1"></i>Đăng nhập</a>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1><?= esc($post['title']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" style="color:rgba(255,255,255,0.7);">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-white"><?= esc($post['title']) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if (!empty($post['image'])): ?>
                            <img src="/<?= esc($post['image']) ?>" alt="" class="w-100 mb-4" style="border-radius:8px;">
                        <?php endif; ?>

                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="lead text-muted mb-4" style="border-left:4px solid var(--primary-red);padding-left:16px;">
                                <?= esc($post['excerpt']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="post-content">
                            <?= $post['content'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bar">
        &copy; <?= date('Y') ?> VANCHUYENHONGPHAT - All Rights Reserved
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
