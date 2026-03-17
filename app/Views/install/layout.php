<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cai dat he thong - Van Chuyen Hong Phat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; }
        .install-card { max-width: 600px; margin: 40px auto; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3); border: none; }
        .install-header { background: linear-gradient(135deg, #e94560, #c62828); color: #fff; border-radius: 16px 16px 0 0; padding: 30px; text-align: center; }
        .install-header h2 { margin: 0; font-weight: 700; }
        .install-header p { margin: 10px 0 0; opacity: .85; }
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
        .step-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,.3); }
        .step-dot.active { background: #fff; }
        .step-dot.done { background: #4caf50; }
        .install-body { padding: 30px; }
        .btn-install { background: linear-gradient(135deg, #e94560, #c62828); border: none; color: #fff; padding: 12px 40px; border-radius: 8px; font-weight: 600; font-size: 1.05rem; }
        .btn-install:hover { background: linear-gradient(135deg, #c62828, #b71c1c); color: #fff; transform: translateY(-1px); }
        .check-item { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .check-item:last-child { border-bottom: none; }
        .check-icon { width: 28px; text-align: center; }
    </style>
</head>
<body>
    <?= $this->renderSection('content') ?>
    <div class="text-center text-white-50 py-3">
        <small>&copy; <?= date('Y') ?> Van Chuyen Hong Phat</small>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
