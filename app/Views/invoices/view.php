<?php 
    $balanceDue = max(0, (float)$invoice['total_amount'] - (float)$invoice['paid_amount']);
    $isPaid = ($invoice['status'] === 'paid' || $balanceDue == 0);
    $isOverdue = ($invoice['status'] === 'overdue' || (strtotime($invoice['due_date']) < time() && ! $isPaid));
?>

<?php if (empty($printOnly)): ?>
<!-- Invoice Actions Bar -->
<div class="page-header invoice-actions-bar">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= site_url('invoices') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="page-title mb-0"><?= esc($invoice['invoice_number']) ?></h2>
                <span class="badge-paper <?= esc($invoice['status']) ?> fs-6 py-1 px-3">
                    <?= ucfirst(str_replace('_', ' ', $invoice['status'])) ?>
                </span>
            </div>
            <p class="page-subtitle">Diterbitkan untuk <?= esc($invoice['partner_name']) ?></p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <?php if (! $isPaid): ?>
            <button type="button" class="btn btn-paper" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-credit-card"></i>
                <span>Catat Pembayaran</span>
            </button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-dark" onclick="window.print()">
            <i class="bi bi-printer"></i>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Realistic Paper.id Style Invoice Document Container -->
<div class="paper-invoice-container mb-4">
    <!-- Watermark Stamp -->
    <?php if ($isPaid): ?>
        <div class="invoice-watermark">LUNAS</div>
    <?php elseif ($isOverdue): ?>
        <div class="invoice-watermark overdue">JATUH TEMPO</div>
    <?php endif; ?>

    <!-- Document Header -->
    <div class="paper-invoice-header d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div class="invoice-brand">
            <div class="invoice-brand-logo">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="color: #028090;">PT YANA SURYA PACKAGING</h4>
                <div class="small text-muted">
                    Jl. Industri Rungkut Megah No. 88, Surabaya, Jawa Timur 60293<br>
                    Email: finance@yanasurya.com &bull; Telp: (031) 843-9900<br>
                    NPWP: 01.988.765.4-604.000
                </div>
            </div>
        </div>

        <div class="paper-invoice-meta">
            <div class="invoice-title-text">INVOICE</div>
            <div class="fw-bold fs-5 text-dark"><?= esc($invoice['invoice_number']) ?></div>
            <div class="small text-muted mt-2">
                <div><strong>Tanggal Terbit:</strong> <?= date('d F Y', strtotime($invoice['issue_date'])) ?></div>
                <div class="<?= $isOverdue ? 'text-danger fw-bold' : '' ?>">
                    <strong>Jatuh Tempo:</strong> <?= date('d F Y', strtotime($invoice['due_date'])) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dual Parties Box -->
    <div class="paper-invoice-parties">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="party-box">
                    <div class="party-label">Ditagihkan Kepada (Bill To):</div>
                    <div class="party-name"><?= esc($invoice['partner_name']) ?></div>
                    <div class="party-text"><?= nl2br(esc($invoice['partner_address'] ?? 'Alamat tidak dicantumkan')) ?></div>
                    <div class="party-text"><strong>Telp:</strong> <?= esc($invoice['partner_phone'] ?? '-') ?></div>
                    <div class="party-text"><strong>NPWP:</strong> <?= esc($invoice['partner_tax_id'] ?? '-') ?></div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="party-box">
                    <div class="party-label">Informasi Rekening Pembayaran:</div>
                    <div class="party-name text-primary">Bank Central Asia (BCA)</div>
                    <div class="party-text"><strong>No. Rekening:</strong> 8291-002-991</div>
                    <div class="party-text"><strong>Atas Nama:</strong> PT Yana Surya Packaging</div>
                    <div class="party-text mt-2 small text-muted">
                        <i class="bi bi-info-circle me-1"></i> Mohon sertakan nomor faktur <code><?= esc($invoice['invoice_number']) ?></code> pada berita transfer.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="paper-invoice-body">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr class="invoice-table-header">
                        <th style="width: 45px;" class="text-center">No</th>
                        <th>Item / Deskripsi Produk</th>
                        <th style="width: 90px;" class="text-center">Qty</th>
                        <th style="width: 140px;" class="text-end">Harga Satuan</th>
                        <th style="width: 110px;" class="text-end">Diskon</th>
                        <th style="width: 150px;" class="text-end">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($item['item_name']) ?></div>
                                <?php if (! empty($item['description'])): ?>
                                    <div class="small text-muted"><?= esc($item['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-semibold">
                                <?= esc($item['quantity']) ?> <?= esc($item['unit'] ?? '') ?>
                            </td>
                            <td class="text-end">
                                Rp <?= number_format($item['unit_price'], 0, ',', '.') ?>
                            </td>
                            <td class="text-end text-muted">
                                <?= $item['discount'] > 0 ? ('Rp ' . number_format($item['discount'], 0, ',', '.')) : '-' ?>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp <?= number_format($item['total'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary & Signatures -->
    <div class="paper-invoice-summary">
        <div class="row g-4 align-items-start">
            <div class="col-12 col-md-7">
                <?php if (! empty($invoice['notes'])): ?>
                    <div class="mb-3">
                        <h6 class="small fw-bold text-secondary text-uppercase mb-1">Catatan Tambahan:</h6>
                        <p class="small text-muted mb-0"><?= nl2br(esc($invoice['notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (! empty($invoice['terms'])): ?>
                    <div class="mb-3">
                        <h6 class="small fw-bold text-secondary text-uppercase mb-1">Syarat & Ketentuan:</h6>
                        <p class="small text-muted mb-0"><?= nl2br(esc($invoice['terms'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="pt-3 mt-4 text-center d-inline-block" style="min-width: 200px;">
                    <div class="small text-muted mb-5">Hormat Kami,<br><strong>PT YANA SURYA PACKAGING</strong></div>
                    <div class="border-top pt-1 fw-bold small text-dark">( Siti Rahmawati )<br><span class="text-muted fw-normal">Finance Dept.</span></div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="bg-light p-3 rounded-3 border">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span class="fw-semibold">Rp <?= number_format($invoice['subtotal'], 0, ',', '.') ?></span>
                    </div>

                    <?php if ($invoice['discount_amount'] > 0): ?>
                    <div class="summary-row text-danger">
                        <span>Diskon Tambahan:</span>
                        <span>- Rp <?= number_format($invoice['discount_amount'], 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>PPN (<?= (float)$invoice['tax_rate'] ?>%):</span>
                        <span>Rp <?= number_format($invoice['tax_amount'], 0, ',', '.') ?></span>
                    </div>

                    <div class="summary-row grand-total">
                        <span>Total Tagihan:</span>
                        <span>Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?></span>
                    </div>

                    <div class="summary-row text-success pt-2">
                        <span>Sudah Dibayar:</span>
                        <span>Rp <?= number_format($invoice['paid_amount'], 0, ',', '.') ?></span>
                    </div>

                    <div class="summary-row pt-1 <?= $balanceDue > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                        <span>Sisa Tagihan:</span>
                        <span>Rp <?= number_format($balanceDue, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($printOnly)): ?>
<!-- Payment History Card -->
<div class="card-paper p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Riwayat Pembayaran & Pelunasan</h5>
        <?php if (! $isPaid): ?>
            <button type="button" class="btn btn-sm btn-paper" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-plus-lg"></i> Tambah Pelunasan
            </button>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table table-paper">
            <thead>
                <tr>
                    <th>No. Bukti Bayar</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th>No. Referensi</th>
                    <th>Catatan</th>
                    <th class="text-end">Jumlah Terbayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada catatan pembayaran untuk invoice ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $pay): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= esc($pay['payment_number']) ?></td>
                            <td><?= date('d M Y', strtotime($pay['payment_date'])) ?></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary small text-uppercase">
                                    <?= str_replace('_', ' ', esc($pay['payment_method'])) ?>
                                </span>
                            </td>
                            <td><code><?= esc($pay['reference_no'] ?? '-') ?></code></td>
                            <td class="small text-muted"><?= esc($pay['notes'] ?? '-') ?></td>
                            <td class="text-end fw-bold text-success">
                                Rp <?= number_format($pay['amount'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Catat Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('invoices/payment/' . $invoice['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Catat Pembayaran Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 small mb-3">
                        Sisa tagihan untuk invoice ini: <strong>Rp <?= number_format($balanceDue, 0, ',', '.') ?></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control fw-bold fs-5 text-success" value="<?= $balanceDue ?>" max="<?= $balanceDue ?>" min="1000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select">
                            <option value="bank_transfer">Transfer Bank (BCA / Mandiri / BRI)</option>
                            <option value="qris">QRIS / E-Wallet</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="cash">Tunai / Cash</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor Referensi Transaksi (Opsional)</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Contoh: TRF-BCA-981273941">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan Pembayaran</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Pelunasan via transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-paper">
                        <i class="bi bi-check2-circle"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
