<div class="page-header">
    <div>
        <h2 class="page-title"><?= esc($pageTitle) ?></h2>
        <p class="page-subtitle">Siklus pengadaan bahan baku, manajemen Purchase Order (PO) supplier, dan penerimaan barang gudang.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-paper" data-bs-toggle="modal" data-bs-target="#createPoModal">
            <i class="bi bi-file-earmark-plus"></i>
            <span>Terbitkan PO Baru</span>
        </button>
        <?php if (! empty($activeOrders)): ?>
            <button type="button" class="btn btn-paper-outline" data-bs-toggle="modal" data-bs-target="#createGrModal">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Catat Penerimaan (GR)</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Tabs Card -->
<div class="card-paper overflow-hidden">
    <div class="p-3 border-bottom bg-light">
        <ul class="nav nav-tabs card-header-tabs" id="procurementTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="po-tab" data-bs-toggle="tab" data-bs-target="#po" type="button">
                    <i class="bi bi-cart3 me-2"></i>Daftar Purchase Orders (PO)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="gr-tab" data-bs-toggle="tab" data-bs-target="#gr" type="button">
                    <i class="bi bi-truck me-2"></i>Penerimaan Barang (Goods Receipt)
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="procurementTabContent">
        <!-- Tab 1: PO List -->
        <div class="tab-pane fade show active" id="po" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-paper table-hover">
                    <thead>
                        <tr>
                            <th>No. PO</th>
                            <th>Supplier / Vendor</th>
                            <th>Tanggal Order</th>
                            <th>Estimasi Tiba</th>
                            <th class="text-end">Nilai Pengadaan</th>
                            <th class="text-center">Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada dokumen Purchase Order yang dibuat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark"><code><?= esc($order['po_number']) ?></code></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($order['supplier_name']) ?></div>
                                        <small class="text-muted"><?= esc($order['supplier_phone'] ?? '-') ?></small>
                                    </td>
                                    <td class="small text-muted"><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                                    <td class="small text-muted"><?= $order['expected_date'] ? date('d M Y', strtotime($order['expected_date'])) : '-' ?></td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($order['status'] === 'received'): ?>
                                            <span class="badge-paper paid">Diterima</span>
                                        <?php elseif ($order['status'] === 'ordered'): ?>
                                            <span class="badge-paper unpaid">Dipesan</span>
                                        <?php else: ?>
                                            <span class="badge-paper draft"><?= esc($order['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted text-truncate" style="max-width: 200px;">
                                        <?= esc($order['notes'] ?? '-') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Goods Receipts List -->
        <div class="tab-pane fade" id="gr" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-paper table-hover">
                    <thead>
                        <tr>
                            <th>No. Surat Masuk (GR)</th>
                            <th>Referensi PO</th>
                            <th>Supplier</th>
                            <th>Tanggal Tiba</th>
                            <th>Diterima Oleh</th>
                            <th>Catatan Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($receipts)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada rekaman tanda terima penerimaan barang.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($receipts as $r): ?>
                                <tr>
                                    <td><code><?= esc($r['gr_number']) ?></code></td>
                                    <td class="fw-semibold"><?= esc($r['po_number']) ?></td>
                                    <td><?= esc($r['supplier_name']) ?></td>
                                    <td class="small text-muted"><?= date('d M Y', strtotime($r['receipt_date'])) ?></td>
                                    <td><i class="bi bi-person me-1"></i><?= esc($r['received_by']) ?></td>
                                    <td class="small text-muted"><?= esc($r['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create PO -->
<div class="modal fade" id="createPoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('procurement/po/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Terbitkan Purchase Order (PO) Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Supplier / Vendor <span class="text-danger">*</span></label>
                        <select name="partner_id" class="form-select" required>
                            <option value="">-- Pilih Supplier --</option>
                            <?php foreach ($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor PO <span class="text-danger">*</span></label>
                        <input type="text" name="po_number" class="form-control" value="<?= esc($newPoNumber) ?>" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Tanggal Order <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Estimasi Tiba</label>
                            <input type="date" name="expected_date" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Total Nilai Pengadaan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" value="0" min="0" step="any" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan / Rincian Material Pesanan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Material box gelombang, pengiriman via armada vendor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-paper">
                        <i class="bi bi-send"></i> Terbitkan PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Create Goods Receipt (GR) -->
<?php if (! empty($activeOrders)): ?>
<div class="modal fade" id="createGrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('procurement/gr/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Catat Penerimaan Barang (GR)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Purchase Order <span class="text-danger">*</span></label>
                        <select name="po_id" class="form-select" required>
                            <?php foreach ($activeOrders as $ao): ?>
                                <option value="<?= $ao['id'] ?>">
                                    <?= esc($ao['po_number']) ?> - <?= esc($ao['supplier_name']) ?> (Rp <?= number_format($ao['total_amount'], 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor Bukti GR <span class="text-danger">*</span></label>
                        <input type="text" name="gr_number" class="form-control" value="<?= esc($newGrNumber) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" name="receipt_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Diterima Oleh (Petugas Gudang)</label>
                        <input type="text" name="received_by" class="form-control" value="<?= esc(session()->get('full_name') ?? 'Petugas Gudang') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan Kondisi Barang</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Barang diterima lengkap dan lolos uji QC fisik gudang."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-paper">
                        <i class="bi bi-check-circle"></i> Rekam Penerimaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
