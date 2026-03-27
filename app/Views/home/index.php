<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VanChuyenHongPhat - Vận chuyển hàng từ Trung Quốc về Việt Nam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-red: #d32f2f;
            --primary-red-dark: #b71c1c;
            --dark-bg: #2c2c2c;
            --gray-bg: #f0f0f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* ===== TOP HEADER ===== */
        .top-header {
            background: #fff;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .logo-img {
            max-height: 70px;
        }
        .header-slogan {
            font-size: 0.85rem;
            color: #333;
            text-transform: uppercase;
            font-weight: 600;
            line-height: 1.5;
            text-align: center;
        }
        .header-slogan .highlight {
            color: var(--primary-red);
            font-weight: 700;
            font-size: 0.95rem;
        }
        .hotline-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hotline-icon {
            width: 45px;
            height: 45px;
            background: var(--primary-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }
        .hotline-text small {
            font-size: 0.75rem;
            color: #666;
        }
        .hotline-text .phone {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary-red);
            letter-spacing: 1px;
        }

        /* ===== MAIN NAV ===== */
        .main-nav {
            background: var(--dark-bg);
        }
        .main-nav .navbar {
            padding: 0;
        }
        .main-nav .nav-link {
            color: #fff !important;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 14px 16px !important;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }
        .main-nav .nav-link:hover,
        .main-nav .nav-link.active {
            background: var(--primary-red);
        }
        .main-nav .auth-links {
            color: #fff;
            font-size: 0.85rem;
        }
        .main-nav .auth-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
        .main-nav .auth-links a:hover {
            color: var(--primary-red);
        }
        .main-nav .navbar-toggler {
            border: none;
            color: #fff;
            font-size: 1.5rem;
            padding: 8px 12px;
        }
        .main-nav .navbar-toggler:focus {
            box-shadow: none;
        }

        /* Mobile auth bar */
        .mobile-auth-bar {
            display: none;
            background: var(--primary-red);
            padding: 8px 16px;
        }
        .mobile-auth-bar a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* ===== HERO ===== */
        .hero-section {
            position: relative;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.5)),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 600"><rect fill="%23345" width="1440" height="600"/><text x="720" y="300" text-anchor="middle" fill="%23567" font-size="40" font-family="sans-serif">Shipping Container Port</text></svg>');
            background-size: cover;
            background-position: center;
            min-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 16px;
        }

        .tracking-box {
            background: rgba(128, 128, 128, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 8px;
            padding: 30px 35px;
            max-width: 700px;
            width: 100%;
        }
        .tracking-box h2 {
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
            text-transform: uppercase;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        .tracking-input-wrap {
            position: relative;
        }
        .tracking-input-wrap input {
            width: 100%;
            padding: 12px 120px 12px 16px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            outline: none;
        }
        .tracking-input-wrap button {
            position: absolute;
            right: 4px;
            top: 4px;
            bottom: 4px;
            padding: 0 24px;
            background: var(--primary-red);
            color: #fff;
            border: none;
            border-radius: 3px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .tracking-input-wrap button:hover {
            background: var(--primary-red-dark);
        }

        .tracking-result {
            margin-top: 16px;
            background: rgba(255,255,255,0.95);
            border-radius: 6px;
            padding: 16px 20px;
        }
        .tracking-result h6 {
            color: var(--primary-red);
            font-weight: 700;
        }

        .app-promo {
            text-align: center;
            margin-top: 24px;
            color: #fff;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .app-badges {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 12px;
        }
        .app-badges img {
            height: 40px;
            border-radius: 6px;
        }

        /* ===== FEATURES ===== */
        .features-section {
            padding: 60px 0;
            background: #fff;
        }
        .features-section .section-title {
            text-align: center;
            margin-bottom: 48px;
        }
        .features-section .section-title h2 {
            font-weight: 800;
            color: var(--primary-red);
            text-transform: uppercase;
            font-size: 1.8rem;
        }
        .features-section .section-title p {
            color: #666;
            font-size: 1rem;
        }
        .feature-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #fff;
            font-size: 2rem;
        }
        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        .feature-card p {
            color: #666;
            font-size: 0.9rem;
        }

        /* ===== PRICING PREVIEW ===== */
        .pricing-section {
            padding: 60px 0;
            background: var(--gray-bg);
        }
        .pricing-section .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .pricing-section .section-title h2 {
            font-weight: 800;
            color: var(--primary-red);
            text-transform: uppercase;
            font-size: 1.8rem;
        }
        .pricing-table {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .pricing-table thead th {
            background: var(--primary-red);
            color: #fff;
            font-weight: 700;
            padding: 14px 16px;
            border: none;
            font-size: 0.9rem;
        }
        .pricing-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        .pricing-table tbody tr:last-child td {
            border-bottom: none;
        }
        .pricing-table .price-highlight {
            color: var(--primary-red);
            font-weight: 700;
            font-size: 1.05rem;
        }

        /* ===== STATS ===== */
        .stats-section {
            padding: 50px 0;
            background: var(--dark-bg);
            color: #fff;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        .stat-item .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-red);
        }
        .stat-item .stat-label {
            font-size: 0.9rem;
            color: #ccc;
            margin-top: 4px;
        }

        /* ===== FOOTER ===== */
        .main-footer {
            background: var(--gray-bg);
            padding: 50px 0 0;
            border-top: 4px solid var(--primary-red);
        }
        .footer-heading {
            color: var(--primary-red);
            font-weight: 800;
            font-size: 1.1rem;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .footer-info {
            font-size: 0.9rem;
            color: #555;
            line-height: 1.8;
        }
        .footer-info strong {
            color: #333;
        }
        .footer-links {
            list-style: none;
            padding: 0;
        }
        .footer-links li {
            margin-bottom: 8px;
        }
        .footer-links a {
            color: #555;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: var(--primary-red);
        }
        .footer-map iframe {
            width: 100%;
            height: 220px;
            border: 0;
            border-radius: 8px;
        }
        .footer-social {
            display: flex;
            gap: 12px;
            margin-top: 12px;
        }
        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-red);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            text-decoration: none;
            transition: background 0.2s;
        }
        .footer-social a:hover {
            background: var(--primary-red-dark);
        }

        .footer-bottom {
            background: #e0e0e0;
            text-align: center;
            padding: 14px;
            margin-top: 40px;
            font-size: 0.85rem;
            color: #666;
        }

        /* Zalo floating button */
        .zalo-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: #0068ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0,104,255,0.4);
            z-index: 1000;
            transition: transform 0.2s;
        }
        .zalo-float:hover {
            transform: scale(1.1);
            color: #fff;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .top-header .header-slogan {
                display: none;
            }
            .top-header .hotline-box {
                display: none;
            }
            .mobile-auth-bar {
                display: block;
            }
            .main-nav .auth-links {
                display: none !important;
            }
            .main-nav .nav-link {
                padding: 10px 16px !important;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .hero-section {
                min-height: 350px;
                padding: 40px 12px;
            }
            .tracking-box {
                padding: 24px 20px;
            }
            .tracking-box h2 {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 575.98px) {
            .hero-section {
                min-height: 300px;
                padding: 30px 10px;
            }
            .tracking-box {
                padding: 20px 16px;
            }
            .tracking-box h2 {
                font-size: 1.1rem;
            }
            .tracking-input-wrap input {
                padding: 10px 110px 10px 12px;
                font-size: 0.9rem;
            }
            .tracking-input-wrap button {
                padding: 0 16px;
                font-size: 0.8rem;
            }
            .stat-item .stat-number {
                font-size: 2rem;
            }
            .feature-icon {
                width: 64px;
                height: 64px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- ===== TOP HEADER ===== -->
    <header class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-6">
                    <a href="/" style="text-decoration:none;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="background:var(--primary-red);color:#fff;font-weight:900;font-size:1.6rem;padding:6px 14px;border-radius:6px;">VCHP</div>
                            <div>
                                <div style="font-weight:800;font-size:1.1rem;color:var(--primary-red);line-height:1.2;">VANCHUYENHONGPHAT</div>
                                <div style="font-size:0.65rem;color:#999;letter-spacing:1px;">EXPRESS SHIPPING</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="header-slogan">
                        VANCHUYENHONGPHAT VẬN CHUYỂN HÀNG TỪ TRUNG QUỐC VỀ VIỆT NAM<br>
                        <span class="highlight">GIÁ RẺ - AN TOÀN - DỄ DÀNG - NHANH CHÓNG</span>
                    </div>
                </div>
                <div class="col-lg-3 col-6 d-flex justify-content-end">
                    <div class="hotline-box">
                        <div class="hotline-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="hotline-text">
                            <small>Hỗ trợ trực tuyến</small>
                            <div class="phone"><?= get_setting('hotline', '0343269115') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== MOBILE AUTH BAR ===== -->
    <div class="mobile-auth-bar d-lg-none">
        <i class="fas fa-user-plus me-1"></i> <a href="/auth/register">ĐĂNG KÝ</a>
        &nbsp;/&nbsp;
        <i class="fas fa-sign-in-alt me-1"></i> <a href="/auth/login">ĐĂNG NHẬP</a>
    </div>

    <!-- ===== MAIN NAVIGATION ===== -->
    <nav class="main-nav sticky-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="/">TRANG CHỦ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#gioi-thieu">GIỚI THIỆU</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#bang-gia">BẢNG BÁO GIÁ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#chinh-sach">CHÍNH SÁCH</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#quy-dinh">QUY ĐỊNH VẬN CHUYỂN</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#huong-dan">HƯỚNG DẪN</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/tin-tuc">TIN TỨC</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#lien-he">LIÊN HỆ</a>
                        </li>
                    </ul>

                    <div class="auth-links d-none d-lg-flex align-items-center">
                        <a href="/auth/register"><i class="fas fa-user-plus me-1"></i> Đăng ký</a>
                        <span class="mx-2">/</span>
                        <a href="/auth/login"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a>
                    </div>
                </div>
            </nav>
        </div>
    </nav>

    <!-- ===== HERO + TRACKING ===== -->
    <section class="hero-section">
        <div class="tracking-box">
            <h2><i class="fas fa-search me-2"></i>TRA MÃ VẬN ĐƠN</h2>

            <form method="post" action="/tracking">
                <?= csrf_field() ?>
                <div class="tracking-input-wrap">
                    <input type="text" name="tracking_code" placeholder="Nhập mã vận đơn..." value="<?= esc(session()->getFlashdata('tracking_code') ?? '') ?>" required>
                    <button type="submit">Tra mã</button>
                </div>
            </form>

            <?php if (session()->getFlashdata('tracking_error')): ?>
                <div class="tracking-result">
                    <p class="text-danger mb-0"><i class="fas fa-exclamation-circle me-1"></i><?= session()->getFlashdata('tracking_error') ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('tracking_result')):
                $result = json_decode(session()->getFlashdata('tracking_result'), true);
            ?>
                <div class="tracking-result">
                    <h6><i class="fas fa-box me-1"></i> Thông tin đơn hàng</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size:0.85rem;">
                        <tr><td class="text-muted" style="width:140px;">Mã đơn:</td><td><strong><?= esc($result['order_code'] ?? '') ?></strong></td></tr>
                        <tr><td class="text-muted">Mã vận đơn TQ:</td><td><?= esc($result['cn_tracking_code'] ?? '-') ?></td></tr>
                        <tr><td class="text-muted">Sản phẩm:</td><td><?= esc(mb_substr($result['product_name'] ?? '-', 0, 50)) ?></td></tr>
                        <tr><td class="text-muted">Trạng thái:</td><td>
                            <?php
                                $statusMap = [
                                    'submitted' => ['Đã tạo', 'secondary'],
                                    'received_cn' => ['Đã nhận tại TQ', 'info'],
                                    'in_transit_cn_vn' => ['Đang vận chuyển', 'primary'],
                                    'received_vn' => ['Đã về VN', 'success'],
                                    'fee_calculated' => ['Đã tính phí', 'warning'],
                                    'paid' => ['Đã thanh toán', 'success'],
                                    'ready_for_pickup' => ['Sẵn sàng lấy', 'info'],
                                    'completed' => ['Hoàn thành', 'success'],
                                ];
                                $st = $result['status'] ?? 'submitted';
                                $label = $statusMap[$st][0] ?? $st;
                                $color = $statusMap[$st][1] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                        </td></tr>
                        <?php if (!empty($result['actual_weight'])): ?>
                        <tr><td class="text-muted">Cân nặng:</td><td><?= number_format($result['actual_weight'], 2) ?> kg</td></tr>
                        <?php endif; ?>
                        <?php if (!empty($result['total_fee'])): ?>
                        <tr><td class="text-muted">Tổng phí:</td><td class="text-danger fw-bold"><?= number_format($result['total_fee'], 0, ',', '.') ?> VNĐ</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php endif; ?>

            <div class="app-promo">
                App VanChuyenHongPhat đã có mặt trên Appstore và CH Play.<br>
                Quý khách hàng vui lòng tải app theo link sau:
                <div class="app-badges">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/200px-Google_Play_Store_badge_EN.svg.png" alt="Google Play">
                    <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store">
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES (Giới thiệu) ===== -->
    <section class="features-section" id="gioi-thieu">
        <div class="container">
            <div class="section-title">
                <h2>Tại sao chọn VanChuyenHongPhat?</h2>
                <p>Dịch vụ vận chuyển hàng Trung Quốc - Việt Nam uy tín hàng đầu</p>
            </div>
            <div class="row g-4">
                <?php if (!empty($sections['gioi-thieu'])): ?>
                    <?php foreach ($sections['gioi-thieu'] as $item): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="<?= esc($item['icon'] ?? 'fas fa-star') ?>"></i></div>
                                <h5><?= esc($item['title']) ?></h5>
                                <p><?= esc($item['excerpt']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== PRICING PREVIEW ===== -->
    <section class="pricing-section" id="bang-gia">
        <div class="container">
            <div class="section-title">
                <h2>Bảng báo giá</h2>
                <p>Giá cước vận chuyển hàng ký gửi Trung Quốc - Việt Nam</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="pricing-table">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Tuyến đường</th>
                                        <th>Loại hàng</th>
                                        <th>Giá / kg</th>
                                        <th>KL tối thiểu</th>
                                        <th>Hệ số khối</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $db = \Config\Database::connect();
                                        $rates = $db->table('shipping_rates')
                                            ->where('is_active', 1)
                                            ->where('user_group_id IS NULL')
                                            ->orderBy('route')
                                            ->get()
                                            ->getResultArray();
                                    ?>
                                    <?php if (!empty($rates)): ?>
                                        <?php foreach ($rates as $r): ?>
                                            <tr>
                                                <td><?= esc($r['route'] ?? '-') ?></td>
                                                <td><?= esc($r['cargo_type'] ?? '-') ?></td>
                                                <td class="price-highlight"><?= number_format($r['rate_per_kg'] ?? 0, 0, ',', '.') ?> VNĐ</td>
                                                <td><?= number_format($r['min_weight'] ?? 0, 1) ?> kg</td>
                                                <td><?= number_format($r['volume_divisor'] ?? 6000, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4">Đang cập nhật bảng giá...</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Bảng giá trên áp dụng cho khách hàng thường. Liên hệ để nhận giá đại lý ưu đãi.</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STATS ===== -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">5+</div>
                        <div class="stat-label">Năm kinh nghiệm</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Khách hàng</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">50K+</div>
                        <div class="stat-label">Đơn hàng/tháng</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Kho hàng TQ</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== HOW IT WORKS ===== -->
    <section class="features-section" id="huong-dan">
        <div class="container">
            <div class="section-title">
                <h2>Hướng dẫn sử dụng</h2>
                <p>Quy trình ký gửi hàng đơn giản</p>
            </div>
            <div class="row g-4">
                <?php
                    $stepColors = ['#1976d2', '#388e3c', '#f57c00', '#7b1fa2', '#00838f', '#c62828'];
                    $stepIndex = 0;
                ?>
                <?php if (!empty($sections['huong-dan'])): ?>
                    <?php foreach ($sections['huong-dan'] as $step): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="feature-card">
                                <div class="feature-icon" style="background:<?= $stepColors[$stepIndex % count($stepColors)] ?>;"><strong style="font-size:1.8rem;"><?= ++$stepIndex ?></strong></div>
                                <h5><?= esc($step['title']) ?></h5>
                                <p><?= esc($step['excerpt']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== POLICIES ===== -->
    <section class="pricing-section" id="chinh-sach">
        <div class="container">
            <div class="section-title">
                <h2>Chính sách & Quy định</h2>
            </div>
            <div class="row g-4">
                <?php
                    $policyPosts = array_merge($sections['chinh-sach'] ?? [], $sections['quy-dinh'] ?? []);
                ?>
                <?php foreach ($policyPosts as $policy): ?>
                    <div class="col-md-4" <?= $policy['section'] === 'quy-dinh' ? 'id="quy-dinh"' : '' ?>>
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="<?= esc($policy['icon'] ?? 'fas fa-file') ?> fa-3x text-danger mb-3"></i>
                                <h5 class="fw-bold"><?= esc($policy['title']) ?></h5>
                                <p class="text-muted small"><?= esc($policy['excerpt']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ===== TIN TỨC ===== -->
    <?php if (!empty($latestNews)): ?>
    <section class="features-section" id="tin-tuc">
        <div class="container">
            <div class="section-title">
                <h2>Tin tức mới nhất</h2>
                <p>Cập nhật thông tin, khuyến mãi và kiến thức vận chuyển</p>
            </div>
            <div class="row g-4">
                <?php foreach ($latestNews as $news): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <?php if (!empty($news['image'])): ?>
                                <img src="/<?= esc($news['image']) ?>" class="card-img-top" alt="<?= esc($news['title']) ?>" style="height:200px;object-fit:cover;">
                            <?php else: ?>
                                <div style="height:200px;background:linear-gradient(135deg,var(--primary-red),#ff6659);display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-newspaper fa-4x text-white" style="opacity:0.3;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <?php if (!empty($news['category_name'])): ?>
                                    <span class="badge bg-danger mb-2"><?= esc($news['category_name']) ?></span>
                                <?php endif; ?>
                                <h5 class="card-title fw-bold" style="font-size:1rem;">
                                    <a href="/tin-tuc/<?= esc($news['slug']) ?>" style="color:inherit;text-decoration:none;"><?= esc($news['title']) ?></a>
                                </h5>
                                <p class="card-text text-muted small"><?= esc(mb_substr($news['excerpt'] ?? '', 0, 120)) ?></p>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i><?= date('d/m/Y', strtotime($news['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="/tin-tuc" class="btn btn-outline-danger"><i class="fas fa-arrow-right me-1"></i> Xem tất cả tin tức</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== FOOTER ===== -->
    <footer class="main-footer" id="lien-he">
        <div class="container">
            <div class="row g-4">
                <!-- Liên hệ -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Liên hệ</h5>
                    <div class="footer-info">
                        <strong>VANCHUYENHONGPHAT</strong><br><br>
                        <i class="fas fa-phone-alt text-danger me-2"></i>Điện thoại: <strong><?= get_setting('hotline_cskh', '0812882222') ?></strong><br><br>
                        <i class="fas fa-envelope text-danger me-2"></i>Email: <strong><?= get_setting('email', 'info@vanchuyenhongphat.com') ?></strong><br><br>
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>Địa chỉ: <?= get_setting('address', 'Hà Nội, Việt Nam') ?><br><br>
                        <small>
                            Hotline CSKH: <?= get_setting('hotline_cskh', '0812882222') ?><br>
                            Hotline Khiếu nại: <?= get_setting('hotline_khieunai', '0812882222') ?><br>
                            Hotline Kho: <?= get_setting('hotline_kho', '0812882222') ?>
                        </small>
                    </div>
                </div>

                <!-- Liên kết -->
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Liên kết</h5>
                    <ul class="footer-links">
                        <li><a href="#bang-gia"><i class="fas fa-angle-right me-2"></i>Bảng báo giá</a></li>
                        <li><a href="#chinh-sach"><i class="fas fa-angle-right me-2"></i>Chính sách</a></li>
                        <li><a href="#quy-dinh"><i class="fas fa-angle-right me-2"></i>Quy định vận chuyển</a></li>
                        <li><a href="#huong-dan"><i class="fas fa-angle-right me-2"></i>Hướng dẫn</a></li>
                        <li><a href="#gioi-thieu"><i class="fas fa-angle-right me-2"></i>Giới thiệu</a></li>
                        <li><a href="#lien-he"><i class="fas fa-angle-right me-2"></i>Liên hệ</a></li>
                    </ul>
                </div>

                <!-- Bản đồ -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">Bản đồ</h5>
                    <div class="footer-map">
                        <iframe src="<?= get_setting('google_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.2!2d105.76!3d20.98!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjDCsDU4JzQ4LjAiTiAxMDXCsDQ1JzM2LjAiRQ!5e0!3m2!1svi!2svn!4v1') ?>" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>

                <!-- Kết nối -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Kết nối với chúng tôi</h5>
                    <div class="footer-social">
                        <a href="<?= get_setting('facebook_url', '#') ?>"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="<?= get_setting('zalo_url', '#') ?>" style="background:#0068ff;"><i class="fas fa-comment-dots"></i></a>
                    </div>
                    <div class="mt-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/200px-Google_Play_Store_badge_EN.svg.png" alt="Google Play" style="height:36px;margin-right:8px;border-radius:4px;">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store" style="height:36px;border-radius:4px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <?= get_setting('footer_text', 'Copyright&copy; ' . date('Y') . ' VANCHUYENHONGPHAT - All Rights Reserved') ?>
        </div>
    </footer>

    <!-- Zalo floating button -->
    <a href="<?= get_setting('zalo_url', '#') ?>" class="zalo-float" title="Chat Zalo">
        Zalo
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(function(a) {
            a.addEventListener('click', function(e) {
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    var offset = 60;
                    var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });

                    // Close mobile nav
                    var navCollapse = document.getElementById('mainNavbar');
                    if (navCollapse.classList.contains('show')) {
                        bootstrap.Collapse.getInstance(navCollapse).hide();
                    }
                }
            });
        });
    </script>
</body>
</html>
