<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Paper Yana Surya - Supply Chain & Invoicing System') ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Paper Yanasurya Theme -->
    <link rel="stylesheet" href="<?= base_url('assets/css/paper-yanasurya.css') ?>">
    <!-- Chart.js for Visual Reporting -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                </div>
                <div>
                    <div class="brand-title">Paper Yana Surya</div>
                    <div class="brand-subtitle">SCM & Invoicing Cloud</div>
                </div>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section-title">Menu Utama</div>
                
                <a href="<?= site_url('dashboard') ?>" class="sidebar-link <?= (uri_string() === 'dashboard' || uri_string() === '') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                <div class="nav-section-title">Penjualan & Keuangan</div>

                <a href="<?= site_url('invoices') ?>" class="sidebar-link <?= str_starts_with(uri_string(), 'invoices') ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i>
                    <span>Invoice Penjualan</span>
                </a>

                <a href="<?= site_url('partners') ?>" class="sidebar-link <?= str_starts_with(uri_string(), 'partners') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Mitra & Pelanggan</span>
                </a>

                <div class="nav-section-title">Rantai Pasok & Gudang</div>

                <a href="<?= site_url('inventory') ?>" class="sidebar-link <?= str_starts_with(uri_string(), 'inventory') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Manajemen Inventaris</span>
                </a>

                <a href="<?= site_url('procurement') ?>" class="sidebar-link <?= str_starts_with(uri_string(), 'procurement') ? 'active' : '' ?>">
                    <i class="bi bi-cart-check"></i>
                    <span>Pengadaan & PO</span>
                </a>
            </div>

            <!-- Current User Status -->
            <div class="sidebar-user">
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar">
                        <?= strtoupper(substr(session()->get('full_name') ?? 'YS', 0, 2)) ?>
                    </div>
                    <div style="line-height: 1.2;">
                        <div class="text-white fw-semibold small text-truncate" style="max-width: 130px;">
                            <?= esc(session()->get('full_name') ?? 'Superadmin') ?>
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            <?= esc(session()->get('role_name') ?? 'Superadmin') ?>
                        </div>
                    </div>
                </div>
                <a href="<?= site_url('logout') ?>" class="text-secondary hover-white p-1" title="Keluar dari sistem">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="app-main">
            <!-- Top Header -->
            <header class="app-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-md-none">
                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="document.querySelector('.app-sidebar').classList.toggle('d-block')">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                    </div>
                    <div class="header-search d-none d-sm-block">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Cari invoice, pelanggan, kode produk...">
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="<?= site_url('invoices/create') ?>" class="btn btn-paper btn-sm">
                        <i class="bi bi-plus-circle"></i>
                        <span>Buat Invoice</span>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle p-2 border" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 250px;">
                            <li><h6 class="dropdown-header">Notifikasi Sistem</h6></li>
                            <li><a class="dropdown-item small" href="<?= site_url('inventory') ?>"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Periksa stok barang menipis</a></li>
                            <li><a class="dropdown-item small" href="<?= site_url('invoices?status=overdue') ?>"><i class="bi bi-clock-history text-danger me-2"></i> 1 tagihan lewat jatuh tempo</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Container -->
            <div class="page-container">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('info')): ?>
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div><?= session()->getFlashdata('info') ?></div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Render Page Content -->
                <?= $content ?? '' ?>
            </div>

            <!-- Footer -->
            <footer class="py-3 px-4 bg-white border-top text-center text-muted small mt-auto">
                &copy; <?= date('Y') ?> PT Yana Surya Packaging Industri &bull; Sistem Terpadu Supply Chain Management & Penagihan Paper.id
            </footer>
        </main>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
