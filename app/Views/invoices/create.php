<div class="page-header">
    <div>
        <h2 class="page-title"><?= esc($pageTitle) ?></h2>
        <p class="page-subtitle">Formulir penerbitan invoice penjualan standar Paper.id dengan kalkulasi otomatis.</p>
    </div>
    <div>
        <a href="<?= site_url('invoices') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<form action="<?= site_url('invoices/store') ?>" method="POST" id="invoiceForm">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Main Invoice Creator Card -->
        <div class="col-12 col-lg-8">
            <div class="card-paper p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">Informasi Pelanggan & Penagihan</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Pilih Pelanggan (Customer) <span class="text-danger">*</span></label>
                        <select name="partner_id" class="form-select" required id="customerSelect">
                            <option value="">-- Pilih Rekanan / Pelanggan --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c['id'] ?>" data-phone="<?= esc($c['phone'] ?? '') ?>" data-address="<?= esc($c['address'] ?? '') ?>">
                                    <?= esc($c['name']) ?> (<?= esc($c['phone'] ?? 'No telp -') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Atau tambahkan rekanan baru di menu <a href="<?= site_url('partners') ?>" target="_blank">Mitra Bisnis</a>.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Nomor Invoice <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_number" class="form-control fw-bold" value="<?= esc($newInvoiceNumber) ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Tanggal Terbit <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control" value="<?= esc($today) ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="<?= esc($defaultDueDate) ?>" required>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0 text-dark">Daftar Produk / Layanan</h5>
                    <button type="button" class="btn btn-sm btn-paper-outline" id="addRowBtn">
                        <i class="bi bi-plus-circle"></i> Tambah Baris
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="itemsTable">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th style="min-width: 240px;">Pilih Produk / Deskripsi Item</th>
                                <th style="width: 100px;">Qty</th>
                                <th style="width: 160px;">Harga Satuan (Rp)</th>
                                <th style="width: 130px;">Diskon (Rp)</th>
                                <th style="width: 160px;" class="text-end">Total (Rp)</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Default Row 1 -->
                            <tr class="item-row">
                                <td>
                                    <select class="form-select form-select-sm mb-1 product-picker" name="product_id[]" onchange="onProductChange(this)">
                                        <option value="">-- Custom / Manual Item --</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>" data-price="<?= $p['unit_price'] ?>">
                                                [<?= esc($p['code']) ?>] <?= esc($p['name']) ?> (Stok: <?= $p['stock'] ?> <?= $p['unit'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="item_name[]" class="form-control form-control-sm item-name-input" placeholder="Nama Barang / Jasa" required>
                                    <input type="text" name="item_description[]" class="form-control form-control-sm mt-1 text-muted small" placeholder="Catatan spesifikasi tambahan (opsional)">
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control form-control-sm qty-input" value="1" min="1" oninput="calculateTotals()">
                                </td>
                                <td>
                                    <input type="number" name="unit_price[]" class="form-control form-control-sm price-input" value="0" min="0" step="any" oninput="calculateTotals()">
                                </td>
                                <td>
                                    <input type="number" name="discount[]" class="form-control form-control-sm discount-input" value="0" min="0" step="any" oninput="calculateTotals()">
                                </td>
                                <td class="text-end fw-bold line-total-display">
                                    Rp 0
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm text-danger remove-row-btn" onclick="removeRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="card-paper p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Catatan untuk Pelanggan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Terima kasih atas pesanan Anda...">Terima kasih atas kepercayaan Anda bermitra dengan PT Yana Surya Packaging Industri.</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Syarat & Ketentuan (Terms of Payment)</label>
                        <textarea name="terms" class="form-control" rows="3" placeholder="Syarat pembayaran...">Pembayaran penuh jatuh tempo sesuai tanggal tertera. Transfer ke Rekening BCA: 8291-002-991 a/n PT Yana Surya Packaging.</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary & Submission Card -->
        <div class="col-12 col-lg-4">
            <div class="card-paper p-4 sticky-top" style="top: 80px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Ringkasan Tagihan</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold" id="subtotalDisplay">Rp 0</span>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted d-flex justify-content-between">
                        <span>Diskon Keseluruhan (Rp)</span>
                    </label>
                    <input type="number" name="discount_amount" id="globalDiscountInput" class="form-control form-control-sm" value="0" min="0" oninput="calculateTotals()">
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small text-muted mb-0">Pajak PPN (%)</label>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="taxToggle" checked onchange="calculateTotals()">
                            <label class="form-check-label small" for="taxToggle">PPN 11%</label>
                        </div>
                    </div>
                    <input type="number" name="tax_rate" id="taxRateInput" class="form-control form-control-sm" value="11" min="0" max="100" step="0.1" oninput="calculateTotals()">
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>Nominal PPN:</span>
                        <span id="taxAmountDisplay">Rp 0</span>
                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-6 fw-bold text-dark">Total Tagihan (Grand Total)</span>
                    <span class="fs-4 fw-black" style="color: #00a896;" id="grandTotalDisplay">Rp 0</span>
                </div>

                <button type="submit" class="btn btn-paper w-100 py-2.5 justify-content-center shadow-sm">
                    <i class="bi bi-send-check"></i>
                    <span>Terbitkan & Simpan Invoice</span>
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Template for Dynamic Rows -->
<template id="rowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-select form-select-sm mb-1 product-picker" name="product_id[]" onchange="onProductChange(this)">
                <option value="">-- Custom / Manual Item --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>" data-price="<?= $p['unit_price'] ?>">
                        [<?= esc($p['code']) ?>] <?= esc($p['name']) ?> (Stok: <?= $p['stock'] ?> <?= $p['unit'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="item_name[]" class="form-control form-control-sm item-name-input" placeholder="Nama Barang / Jasa" required>
            <input type="text" name="item_description[]" class="form-control form-control-sm mt-1 text-muted small" placeholder="Catatan spesifikasi tambahan (opsional)">
        </td>
        <td>
            <input type="number" name="quantity[]" class="form-control form-control-sm qty-input" value="1" min="1" oninput="calculateTotals()">
        </td>
        <td>
            <input type="number" name="unit_price[]" class="form-control form-control-sm price-input" value="0" min="0" step="any" oninput="calculateTotals()">
        </td>
        <td>
            <input type="number" name="discount[]" class="form-control form-control-sm discount-input" value="0" min="0" step="any" oninput="calculateTotals()">
        </td>
        <td class="text-end fw-bold line-total-display">
            Rp 0
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm text-danger remove-row-btn" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
function formatRupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
}

function onProductChange(selectElem) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const row = selectElem.closest('.item-row');
    const nameInput = row.querySelector('.item-name-input');
    const priceInput = row.querySelector('.price-input');

    if (selectedOption.value) {
        nameInput.value = selectedOption.dataset.name || '';
        priceInput.value = selectedOption.dataset.price || '0';
    }
    calculateTotals();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('.item-row').remove();
        calculateTotals();
    } else {
        alert('Minimal harus ada 1 baris item invoice.');
    }
}

function calculateTotals() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.item-row');

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

        const lineTotal = Math.max(0, (qty * price) - discount);
        row.querySelector('.line-total-display').innerText = formatRupiah(lineTotal);
        subtotal += lineTotal;
    });

    document.getElementById('subtotalDisplay').innerText = formatRupiah(subtotal);

    const globalDiscount = parseFloat(document.getElementById('globalDiscountInput').value) || 0;
    const taxToggle = document.getElementById('taxToggle').checked;
    const taxRate = taxToggle ? (parseFloat(document.getElementById('taxRateInput').value) || 0) : 0;

    const taxableAmount = Math.max(0, subtotal - globalDiscount);
    const taxAmount = taxableAmount * (taxRate / 100);

    document.getElementById('taxAmountDisplay').innerText = formatRupiah(taxAmount);

    const grandTotal = taxableAmount + taxAmount;
    document.getElementById('grandTotalDisplay').innerText = formatRupiah(grandTotal);
}

document.getElementById('addRowBtn').addEventListener('click', function() {
    const template = document.getElementById('rowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById('itemsContainer').appendChild(clone);
});

// Run initial calculation
document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
