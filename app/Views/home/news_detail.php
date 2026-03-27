<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($post['title']) ?> - VanChuyenHongPhat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-red: #1d5d36; --primary-red-dark: #144a2a; --dark-bg: #2c2c2c; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        .top-bar { background: var(--dark-bg); padding: 10px 0; }
        .top-bar a { color: #fff; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .top-bar a:hover { color: var(--primary-red); }
        .top-bar .brand { color: var(--primary-red); font-weight: 900; font-size: 1.2rem; }
        .page-header { background: linear-gradient(135deg, var(--primary-red), #ff6659); padding: 40px 0; color: #fff; }
        .page-header h1 { font-weight: 800; font-size: 1.6rem; }
        .post-content { font-size: 1rem; line-height: 1.8; }
        .post-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 16px 0; }
        .related-card:hover { transform: translateY(-3px); }
        .related-card { transition: transform 0.3s; }
        .footer-bar { background: var(--dark-bg); color: #999; padding: 16px 0; text-align: center; font-size: 0.85rem; }
    </style>
</head>
<body>

    <nav class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="brand"><i class="fas fa-truck me-2"></i>VANCHUYENHONGPHAT</a>
            <div>
                <a href="/" class="me-3"><i class="fas fa-home me-1"></i>Trang chủ</a>
                <a href="/tin-tuc" class="me-3"><i class="fas fa-newspaper me-1"></i>Tin tức</a>
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
                    <li class="breadcrumb-item"><a href="/tin-tuc" style="color:rgba(255,255,255,0.7);">Tin tức</a></li>
                    <li class="breadcrumb-item active text-white"><?= esc(mb_substr($post['title'], 0, 40)) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <?php if (!empty($post['category_name'])): ?>
                                <span class="badge bg-danger me-2"><?= esc($post['category_name']) ?></span>
                            <?php endif; ?>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></small>
                        </div>

                        <?php if (!empty($post['image'])): ?>
                            <img src="/<?= esc($post['image']) ?>" alt="<?= esc($post['title']) ?>" class="w-100 mb-4" style="border-radius:8px;max-height:400px;object-fit:cover;">
                        <?php endif; ?>

                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="lead text-muted mb-4" style="font-size:1.05rem;border-left:4px solid var(--primary-red);padding-left:16px;">
                                <?= esc($post['excerpt']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="post-content">
                            <?= $post['content'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <?php if (!empty($related)): ?>
                    <h5 class="fw-bold mb-3"><i class="fas fa-list me-2 text-danger"></i>Bài viết liên quan</h5>
                    <?php foreach ($related as $r): ?>
                        <div class="card border-0 shadow-sm mb-3 related-card">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1" style="font-size:0.9rem;">
                                    <a href="/tin-tuc/<?= esc($r['slug']) ?>" style="color:inherit;text-decoration:none;"><?= esc($r['title']) ?></a>
                                </h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i><?= date('d/m/Y', strtotime($r['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                        <h6 class="fw-bold">Cần hỗ trợ?</h6>
                        <p class="text-muted small mb-2">Liên hệ hotline</p>
                        <a href="tel:0812882222" class="btn btn-danger"><i class="fas fa-phone me-1"></i>0812.882.222</a>
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
