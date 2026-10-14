<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Masuk ke Sistem - Paper Yana Surya') ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Paper Theme -->
    <link rel="stylesheet" href="<?= base_url('assets/css/paper-yanasurya.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #00a896 0%, #028090 100%);
            padding: 2.25rem 2rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
        }
        .login-brand-icon {
            width: 58px;
            height: 58px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 0.85rem;
            backdrop-filter: blur(5px);
        }
        .demo-pill {
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.76rem;
            border: 1px dashed #cbd5e1;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            background: #f8fafc;
        }
        .demo-pill:hover {
            background: #e6f6f4;
            border-color: #00a896;
            color: #028090;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-brand-icon">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">Paper Yana Surya</h4>
            <p class="mb-0 text-white-50 small">Supply Chain & Invoicing Platform</p>
        </div>

        <div class="p-4 p-sm-4 pt-4">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show small py-2 px-3 border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-info alert-dismissible fade show small py-2 px-3 border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <div><?= session()->getFlashdata('info') ?></div>
                    <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('authenticate') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="login" class="form-label small fw-semibold text-secondary">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="login" name="login" value="<?= old('login', 'superadmin') ?>" required placeholder="Masukkan email atau username">
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                        <a href="javascript:void(0)" class="small text-decoration-none" style="color: #00a896;">Lupa sandi?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" value="password123" required placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-paper w-100 py-2 justify-content-center">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Quick Demo Login Helpers -->
            <div class="mt-4 pt-3 border-top">
                <div class="text-muted small fw-semibold mb-2">Akun Demo Cepat (Klik untuk isi):</div>
                <div class="d-flex flex-wrap gap-2">
                    <div class="demo-pill" onclick="fillLogin('superadmin', 'password123')">
                        <strong>Superadmin:</strong> superadmin
                    </div>
                    <div class="demo-pill" onclick="fillLogin('finance', 'password123')">
                        <strong>Finance:</strong> finance
                    </div>
                    <div class="demo-pill" onclick="fillLogin('warehouse', 'password123')">
                        <strong>Warehouse:</strong> warehouse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(user, pass) {
            document.getElementById('login').value = user;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
