<div class="page-header">
    <div>
        <h2 class="page-title"><?= esc($pageTitle) ?></h2>
        <p class="page-subtitle">Kontrol pergudangan, level stok minimum, valuasi aset inventaris, dan audit pergerakan barang.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-paper" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Produk</span>
        </button>
        <button type="button" class="btn btn-paper-outline" data-bs-toggle="modal" data-bs-target="#movementModal">
            <i class="bi bi-arrow-left-right"></i>
            <span>Catat Mutasi Stok</span>
        </button>
    </div>
</div>

<!-- Inventory Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card teal">
            <div>
                <div class="kpi-title">Total SKU Produk</div>
                <div class="kpi-value"><?= count($products) ?> Item</div>
                <div class="kpi-desc text-success">Aktif dalam katalog</div>
            </div>
            <div class="kpi-icon teal"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div>
                <div class="kpi-title">Total Kuantitas Fisik</div>
                <div class="kpi-value"><?= number_format($totalStockItems, 0, ',', '.') ?></div>
                <div class="kpi-desc text-primary">Unit barang di gudang</div>
            </div>
            <div class="kpi-icon blue"><i class="bi bi-stack"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div>
                <div class="kpi-title">Valuasi Stok Gudang</div>
                <div class="kpi-value">Rp <?= number_format($totalValuation, 0, ',', '.') ?></div>
                <div class="kpi-desc text-warning">Berdasarkan HPP barang</div>
            </div>
            <div class="kpi-icon amber"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card <?= $lowStockCount > 0 ? 'rose' : 'teal' ?>">
            <div>
                <div class="kpi-title">Stok Menipis (Alert)</div>
                <div class="kpi-value text-danger"><?= $lowStockCount ?> SKU</div>
                <div class="kpi-desc text-danger">Di bawah ambang minimum</div>
            </div>
            <div class="kpi-icon <?= $lowStockCount > 0 ? 'rose' : 'teal' ?>"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="card-paper overflow-hidden mb-4">
    <div class="p-3 border-bottom bg-light">
        <ul class="nav nav-tabs card-header-tabs" id="inventoryTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="catalog-tab" data-bs-toggle="tab" data-bs-target="#catalog" type="button">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Katalog Produk & Stok
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="movements-tab" data-bs-toggle="tab" data-bs-target="#movements" type="button">
                    <i class="bi bi-clock-history me-2"></i>Log Mutasi & Riwayat Stok
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="inventoryTabContent">
        <!-- Tab 1: Catalog -->
        <div class="tab-pane fade show active" id="catalog" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-paper table-hover">
                    <thead>
                        <tr>
                            <th>Kode SKU</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga Beli (HPP)</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-center">Sisa Stok</th>
                            <th class="text-center">Min. Stok</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada produk yang didaftarkan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <?php $isLow = $p['stock'] <= $p['min_stock']; ?>
                                <tr>
                                    <td><code><?= esc($p['code']) ?></code></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($p['name']) ?></div>
                                        <?php if (! empty($p['description'])): ?>
                                            <div class="small text-muted text-truncate" style="max-width: 280px;"><?= esc($p['description']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($p['category']) ?></span>
                                    </td>
                                    <td class="text-end text-muted">
                                        Rp <?= number_format($p['cost_price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        Rp <?= number_format($p['unit_price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center fw-bold fs-6 <?= $isLow ? 'text-danger' : 'text-success' ?>">
                                        <?= esc($p['stock']) ?> <?= esc($p['unit']) ?>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= esc($p['min_stock']) ?> <?= esc($p['unit']) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($p['stock'] <= 0): ?>
                                            <span class="badge-paper overdue">Habis</span>
                                        <?php elseif ($isLow): ?>
                                            <span class="badge-paper unpaid">Menipis</span>
                                        <?php else: ?>
                                            <span class="badge-paper paid">Tersedia</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Stock Movements Log -->
        <div class="tab-pane fade" id="movements" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-paper table-hover">
                    <thead>
                        <tr>
                            <th>Waktu & Tanggal</th>
                            <th>Kode / Nama Produk</th>
                            <th class="text-center">Jenis Mutasi</th>
                            <th class="text-end">Jumlah</th>
                            <th>Referensi</th>
                            <th>Keterangan</th>
                            <th>Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($movements)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada catatan mutasi barang.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($movements as $m): ?>
                                <tr>
                                    <td class="small text-muted">
                                        <?= date('d M Y H:i', strtotime($m['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= esc($m['product_name']) ?></div>
                                        <small class="text-muted"><code><?= esc($m['product_code']) ?></code></small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($m['movement_type'] === 'in'): ?>
                                            <span class="badge bg-success-subtle text-success"><i class="bi bi-box-arrow-in-down"></i> Masuk</span>
                                        <?php elseif ($m['movement_type'] === 'out'): ?>
                                            <span class="badge bg-danger-subtle text-danger"><i class="bi bi-box-arrow-up"></i> Keluar</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning"><i class="bi bi-sliders"></i> Adjustment</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold <?= $m['movement_type'] === 'out' ? 'text-danger' : 'text-success' ?>">
                                        <?= ($m['movement_type'] === 'out' ? '-' : '+') . esc($m['quantity']) ?> <?= esc($m['unit']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border small text-uppercase"><?= esc($m['reference_type']) ?></span>
                                    </td>
                                    <td class="small text-muted"><?= esc($m['notes'] ?? '-') ?></td>
                                    <td class="small text-dark"><?= esc($m['creator_name'] ?? 'Sistem') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Produk Baru -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('inventory/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Tambah Produk / Material Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Kode SKU Produk <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" required placeholder="Contoh: BOX-KRN-03">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Contoh: Corrugated Box Jumbo 60x40x40">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Kategori</label>
                            <input type="text" name="category" class="form-control" placeholder="Contoh: Corrugated Box, Film, Pallet" value="Corrugated Box">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Satuan Unit</label>
                            <select name="unit" class="form-select">
                                <option value="pcs">pcs (Pieces)</option>
                                <option value="roll">roll (Roll)</option>
                                <option value="unit">unit (Unit)</option>
                                <option value="pack">pack (Paket)</option>
                                <option value="kg">kg (Kilogram)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Harga Beli / HPP (Rp)</label>
                            <input type="number" name="cost_price" class="form-control" value="0" min="0" step="any">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Harga Jual Standar (Rp)</label>
                            <input type="number" name="unit_price" class="form-control" value="0" min="0" step="any">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Stok Awal Fisik</label>
                            <input type="number" name="stock" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Batas Minimum Peringatan (Min. Stock)</label>
                            <input type="number" name="min_stock" class="form-control" value="10" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Deskripsi / Spesifikasi</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Keterangan dimensi, bahan, atau spesifikasi barang..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-paper">
                        <i class="bi bi-check2"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Catat Mutasi Stok -->
<div class="modal fade" id="movementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('inventory/movement') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Pencatatan Mutasi Stok Gudang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Produk <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    [<?= esc($p['code']) ?>] <?= esc($p['name']) ?> (Stok saat ini: <?= $p['stock'] ?> <?= $p['unit'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jenis Mutasi <span class="text-danger">*</span></label>
                        <select name="movement_type" class="form-select" required>
                            <option value="in">Barang Masuk (Stock In / Pembelian)</option>
                            <option value="out">Barang Keluar (Stock Out / Rusak / Sample)</option>
                            <option value="adjustment">Penyesuaian / Stock Opname</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jumlah Kuantitas <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan / Alasan Mutasi</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Penerimaan tambahan vendor, sample display, atau hasil stock opname"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-paper">
                        <i class="bi bi-save"></i> Rekam Mutasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
