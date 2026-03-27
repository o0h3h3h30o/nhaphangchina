<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức - VanChuyenHongPhat</title>
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
        .page-header h1 { font-weight: 800; font-size: 2rem; }
        .news-card { transition: transform 0.3s; }
        .news-card:hover { transform: translateY(-4px); }
        .category-btn.active { background: var(--primary-red) !important; color: #fff !important; border-color: var(--primary-red) !important; }
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
            <h1><i class="fas fa-newspaper me-2"></i>Tin tức</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" style="color:rgba(255,255,255,0.7);">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-white">Tin tức</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        <!-- Category filter -->
        <div class="mb-4">
            <a href="/tin-tuc" class="btn btn-sm <?= empty($currentCategory) ? 'btn-success' : 'btn-outline-secondary' ?> me-1">Tất cả</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/tin-tuc?category=<?= esc($cat['slug']) ?>" class="btn btn-sm <?= $currentCategory === $cat['slug'] ? 'btn-success' : 'btn-outline-secondary' ?> me-1"><?= esc($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($news)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                <p class="text-muted">Chưa có tin tức nào.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($news as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 news-card">
                            <?php if (!empty($item['image'])): ?>
                                <img src="/<?= esc($item['image']) ?>" class="card-img-top" alt="<?= esc($item['title']) ?>" style="height:200px;object-fit:cover;">
                            <?php else: ?>
                                <div style="height:200px;background:linear-gradient(135deg,#1d5d36,#2e8b57);display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-newspaper fa-4x text-white" style="opacity:0.3;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <?php if (!empty($item['category_name'])): ?>
                                    <span class="badge bg-danger mb-2"><?= esc($item['category_name']) ?></span>
                                <?php endif; ?>
                                <h5 class="card-title fw-bold" style="font-size:1rem;">
                                    <a href="/tin-tuc/<?= esc($item['slug']) ?>" style="color:inherit;text-decoration:none;"><?= esc($item['title']) ?></a>
                                </h5>
                                <p class="card-text text-muted small"><?= esc(mb_substr($item['excerpt'] ?? '', 0, 150)) ?></p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <small class="text-muted"><i class="fas fa-clock me-1"></i><?= date('d/m/Y', strtotime($item['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $currentCategory ? 'category=' . esc($currentCategory) . '&' : '' ?>page=<?= $currentPage - 1 ?>">&laquo;</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= $currentCategory ? 'category=' . esc($currentCategory) . '&' : '' ?>page=<?= $i ?>" style="<?= $i === $currentPage ? 'background:var(--primary-red);border-color:var(--primary-red);' : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $currentCategory ? 'category=' . esc($currentCategory) . '&' : '' ?>page=<?= $currentPage + 1 ?>">&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <p class="text-center text-muted small">Hiển thị trang <?= $currentPage ?>/<?= $totalPages ?> (<?= $total ?> bài viết)</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="footer-bar">
        &copy; <?= date('Y') ?> VANCHUYENHONGPHAT - All Rights Reserved
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
