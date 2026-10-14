<div class="page-header">
    <div>
        <h2 class="page-title"><?= esc($pageTitle) ?></h2>
        <p class="page-subtitle">Pantau kesehatan finansial penagihan dan rantai pasokan logistik PT Yana Surya.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('invoices/create') ?>" class="btn btn-paper">
            <i class="bi bi-file-earmark-plus"></i>
            <span>Buat Invoice Baru</span>
        </a>
        <a href="<?= site_url('inventory') ?>" class="btn btn-paper-outline">
            <i class="bi bi-arrow-left-right"></i>
            <span>Mutasi Stok</span>
        </a>
    </div>
</div>

<!-- Low Stock Banner Alert if any -->
<?php if (! empty($lowStockProducts)): ?>
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" role="alert">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-warning text-dark p-2 rounded-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold">Peringatan: Terdapat <?= count($lowStockProducts) ?> Produk Mencapai Batas Minimum Stok!</h6>
            <span class="small text-muted">
                <?= implode(', ', array_map(fn($p) => esc($p['name']) . " (Sisa: {$p['stock']} {$p['unit']})", array_slice($lowStockProducts, 0, 2))) ?>
                <?= count($lowStockProducts) > 2 ? 'dan lainnya...' : '' ?>
            </span>
        </div>
    </div>
    <a href="<?= site_url('inventory') ?>" class="btn btn-sm btn-dark">Kelola Stok</a>
</div>
<?php endif; ?>

<!-- 4 Key Financial & Supply Chain KPIs -->
<div class="row g-3 mb-4">
    <!-- Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card teal">
            <div>
                <div class="kpi-title">Pendapatan Diterima</div>
                <div class="kpi-value">Rp <?= number_format($financialStats['total_revenue'] ?? 0, 0, ',', '.') ?></div>
                <div class="kpi-desc text-success"><i class="bi bi-arrow-up-right me-1"></i><?= $financialStats['paid_count'] ?> Invoice Lunas</div>
            </div>
            <div class="kpi-icon teal">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <!-- Outstanding Receivables -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div>
                <div class="kpi-title">Total Piutang Usaha</div>
                <div class="kpi-value">Rp <?= number_format($financialStats['unpaid_receivables'] ?? 0, 0, ',', '.') ?></div>
                <div class="kpi-desc text-warning"><i class="bi bi-clock-history me-1"></i><?= $financialStats['unpaid_count'] ?> Invoice Menunggu Bayar</div>
            </div>
            <div class="kpi-icon amber">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>

    <!-- Procurement Active Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div>
                <div class="kpi-title">Pengadaan Aktif (PO)</div>
                <div class="kpi-value"><?= $activePoCount ?> PO</div>
                <div class="kpi-desc text-primary"><i class="bi bi-truck me-1"></i>Rp <?= number_format($totalPoAmount, 0, ',', '.') ?> Total PO</div>
            </div>
            <div class="kpi-icon blue">
                <i class="bi bi-cart3"></i>
            </div>
        </div>
    </div>

    <!-- Inventory Alert -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card <?= count($lowStockProducts) > 0 ? 'rose' : 'teal' ?>">
            <div>
                <div class="kpi-title">Katalog & Stok SKU</div>
                <div class="kpi-value"><?= $totalProductsCount ?> Item</div>
                <div class="kpi-desc <?= count($lowStockProducts) > 0 ? 'text-danger' : 'text-success' ?>">
                    <i class="bi bi-shield-check me-1"></i><?= count($lowStockProducts) ?> SKU Perlu Restock
                </div>
            </div>
            <div class="kpi-icon <?= count($lowStockProducts) > 0 ? 'rose' : 'teal' ?>">
                <i class="bi bi-boxes"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart & SCM Analytics Section -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card-paper p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Tren Arus Kas & Penagihan (YTD)</h5>
                    <small class="text-muted">Perbandingan penerimaan invoice vs pengeluaran PO vendor</small>
                </div>
                <span class="badge bg-light text-secondary border">Tahun <?= date('Y') ?></span>
            </div>
            <div style="height: 280px;">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card-paper p-4 h-100">
            <h5 class="fw-bold mb-1">Status Penagihan Invoice</h5>
            <small class="text-muted d-block mb-3">Komposisi status faktur penjualan aktif</small>
            
            <div style="height: 200px;" class="d-flex justify-content-center">
                <canvas id="invoiceStatusChart"></canvas>
            </div>

            <div class="mt-3 pt-3 border-top small">
                <div class="d-flex justify-content-between py-1">
                    <span><i class="bi bi-circle-fill text-success me-2"></i>Lunas (Paid)</span>
                    <strong class="text-success"><?= $financialStats['paid_count'] ?></strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span><i class="bi bi-circle-fill text-warning me-2"></i>Belum Lunas (Unpaid)</span>
                    <strong class="text-warning"><?= $financialStats['unpaid_count'] ?></strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span><i class="bi bi-circle-fill text-danger me-2"></i>Jatuh Tempo (Overdue)</span>
                    <strong class="text-danger"><?= $financialStats['overdue_count'] ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Invoices & Stock Movements Tables -->
<div class="row g-4">
    <!-- Recent Invoices -->
    <div class="col-12 col-lg-7">
        <div class="card-paper overflow-hidden">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Invoice Penjualan Terbaru</span>
                <a href="<?= site_url('invoices') ?>" class="small text-decoration-none fw-semibold" style="color: #00a896;">Lihat Semua <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-paper">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Pelanggan</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentInvoices)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada invoice yang diterbitkan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentInvoices as $inv): ?>
                                <tr>
                                    <td>
                                        <a href="<?= site_url('invoices/view/' . $inv['id']) ?>" class="fw-bold text-decoration-none" style="color: #028090;">
                                            <?= esc($inv['invoice_number']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= esc($inv['partner_name']) ?></div>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d M Y', strtotime($inv['due_date'])) ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-paper <?= esc($inv['status']) ?>">
                                            <?= ucfirst(str_replace('_', ' ', $inv['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="col-12 col-lg-5">
        <div class="card-paper overflow-hidden">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Aktivitas Mutasi Gudang</span>
                <a href="<?= site_url('inventory') ?>" class="small text-decoration-none fw-semibold" style="color: #00a896;">Ke Gudang <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-paper">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentMovements)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada mutasi stok.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentMovements as $sm): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 170px;"><?= esc($sm['product_name']) ?></div>
                                        <div class="small text-muted"><?= esc($sm['product_code']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($sm['movement_type'] === 'in'): ?>
                                            <span class="badge bg-success-subtle text-success small"><i class="bi bi-arrow-down-left"></i> Masuk</span>
                                        <?php elseif ($sm['movement_type'] === 'out'): ?>
                                            <span class="badge bg-danger-subtle text-danger small"><i class="bi bi-arrow-up-right"></i> Keluar</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary small"><i class="bi bi-sliders"></i> Adjust</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?= ($sm['movement_type'] === 'out' ? '-' : '+') . esc($sm['quantity']) ?> <?= esc($sm['unit']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Cashflow Chart
    const ctxCashflow = document.getElementById('cashflowChart').getContext('2d');
    new Chart(ctxCashflow, {
        type: 'line',
        data: {
            labels: ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
            datasets: [
                {
                    label: 'Pendapatan Invoice (Rp Juta)',
                    data: [12.5, 18.2, 14.8, 22.4, 19.8, 25.6],
                    borderColor: '#00a896',
                    backgroundColor: 'rgba(0, 168, 150, 0.1)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5
                },
                {
                    label: 'Pengeluaran PO Bahan (Rp Juta)',
                    data: [8.5, 11.0, 9.4, 15.2, 13.0, 16.5],
                    borderColor: '#028090',
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    tension: 0.35,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, font: { family: 'Inter', size: 12 } } }
            },
            scales: {
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Inter', size: 11 } } },
                x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } }
            }
        }
    });

    // 2. Invoice Status Donut Chart
    const ctxStatus = document.getElementById('invoiceStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Bayar', 'Jatuh Tempo'],
            datasets: [{
                data: [
                    <?= (int) ($financialStats['paid_count'] ?? 1) ?>,
                    <?= (int) ($financialStats['unpaid_count'] ?? 1) ?>,
                    <?= (int) ($financialStats['overdue_count'] ?? 1) ?>
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '72%'
        }
    });
});
</script>
