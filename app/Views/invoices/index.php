<div class="page-header">
    <div>
        <h2 class="page-title"><?= esc($pageTitle) ?></h2>
        <p class="page-subtitle">Kelola seluruh tagihan penjualan B2B, status pelunasan, dan faktur Paper.id.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('invoices/create') ?>" class="btn btn-paper">
            <i class="bi bi-plus-lg"></i>
            <span>Buat Invoice Baru</span>
        </a>
    </div>
</div>

<!-- Stats Header Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card-paper p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon teal flex-shrink-0">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold text-uppercase">Total Terbayar</small>
                <div class="fs-5 fw-bold text-success">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card-paper p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon amber flex-shrink-0">
                <i class="bi bi-clock"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold text-uppercase">Total Belum Bayar</small>
                <div class="fs-5 fw-bold text-warning">Rp <?= number_format($stats['unpaid_receivables'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card-paper p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon rose flex-shrink-0">
                <i class="bi bi-exclamation-octagon"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold text-uppercase">Jatuh Tempo</small>
                <div class="fs-5 fw-bold text-danger"><?= $stats['overdue_count'] ?> Invoice</div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Filter Tabs & Table -->
<div class="card-paper overflow-hidden">
    <!-- Filter Pills -->
    <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 bg-light">
        <ul class="nav nav-pills gap-1">
            <li class="nav-item">
                <a class="nav-link <?= ($currentStatus === 'all') ? 'active bg-dark' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('invoices') ?>">Semua Status</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentStatus === 'unpaid') ? 'active bg-warning text-dark' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('invoices?status=unpaid') ?>">Belum Bayar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentStatus === 'overdue') ? 'active bg-danger' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('invoices?status=overdue') ?>">Jatuh Tempo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentStatus === 'paid') ? 'active bg-success' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('invoices?status=paid') ?>">Lunas</a>
            </li>
        </ul>

        <div class="small text-muted">
            Menampilkan <strong><?= count($invoices) ?></strong> dokumen
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-paper table-hover">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Pelanggan</th>
                    <th>Tgl Terbit</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Total Tagihan</th>
                    <th class="text-end">Sisa Tagihan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Tidak ada data invoice dengan filter ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                        <?php 
                            $balance = max(0, (float)$inv['total_amount'] - (float)$inv['paid_amount']);
                        ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('invoices/view/' . $inv['id']) ?>" class="fw-bold text-decoration-none" style="color: #00a896;">
                                    <?= esc($inv['invoice_number']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($inv['partner_name']) ?></div>
                                <div class="small text-muted"><?= esc($inv['partner_phone'] ?? '-') ?></div>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y', strtotime($inv['issue_date'])) ?>
                            </td>
                            <td class="small">
                                <span class="<?= (strtotime($inv['due_date']) < time() && $inv['status'] !== 'paid') ? 'text-danger fw-bold' : 'text-muted' ?>">
                                    <?= date('d M Y', strtotime($inv['due_date'])) ?>
                                </span>
                            </td>
                            <td class="text-end fw-semibold">
                                Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?>
                            </td>
                            <td class="text-end fw-bold <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                                Rp <?= number_format($balance, 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <span class="badge-paper <?= esc($inv['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $inv['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('invoices/view/' . $inv['id']) ?>" class="btn btn-outline-secondary" title="Lihat Faktur">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= site_url('invoices/pdf/' . $inv['id']) ?>" target="_blank" class="btn btn-outline-secondary" title="Cetak / PDF">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
