<div class="page-header">
    <div>
        <h2 class="page-title"><?= esc($pageTitle) ?></h2>
        <p class="page-subtitle">Pusat data mitra pelanggan B2B dan pemasok material logistik PT Yana Surya.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-paper" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
            <i class="bi bi-person-plus"></i>
            <span>Tambah Rekanan Baru</span>
        </button>
    </div>
</div>

<!-- Partner Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card-paper p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon teal flex-shrink-0">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold text-uppercase">Total Rekanan</small>
                <div class="fs-5 fw-bold text-dark"><?= count($partners) ?> Mitra</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card-paper p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon blue flex-shrink-0">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold text-uppercase">Pelanggan Aktif</small>
                <div class="fs-5 fw-bold text-primary"><?= $totalCustomers ?> Customer</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card-paper p-3 d-flex align-items-center gap-3">
            <div class="kpi-icon amber flex-shrink-0">
                <i class="bi bi-truck"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold text-uppercase">Pemasok / Vendor</small>
                <div class="fs-5 fw-bold text-warning"><?= $totalSuppliers ?> Supplier</div>
            </div>
        </div>
    </div>
</div>

<!-- Partners Table -->
<div class="card-paper overflow-hidden">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
        <ul class="nav nav-pills gap-1">
            <li class="nav-item">
                <a class="nav-link <?= ($currentType === 'all') ? 'active bg-dark' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('partners') ?>">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentType === 'customer') ? 'active bg-primary' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('partners?type=customer') ?>">Pelanggan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentType === 'supplier') ? 'active bg-warning text-dark' : 'text-secondary' ?> py-1 px-3 small" href="<?= site_url('partners?type=supplier') ?>">Supplier / Vendor</a>
            </li>
        </ul>
    </div>

    <div class="table-responsive">
        <table class="table table-paper table-hover">
            <thead>
                <tr>
                    <th>Nama Perusahaan / Rekanan</th>
                    <th class="text-center">Kategori</th>
                    <th>Kontak / Telp</th>
                    <th>Email</th>
                    <th>NPWP / Pajak</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($partners)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada rekanan yang terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($partners as $pt): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($pt['name']) ?></div>
                            </td>
                            <td class="text-center">
                                <?php if ($pt['type'] === 'customer'): ?>
                                    <span class="badge bg-primary-subtle text-primary border small">Pelanggan</span>
                                <?php elseif ($pt['type'] === 'supplier'): ?>
                                    <span class="badge bg-warning-subtle text-warning border small">Pemasok</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info border small">Keduanya</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($pt['phone'] ?? '-') ?></td>
                            <td class="text-muted small"><?= esc($pt['email'] ?? '-') ?></td>
                            <td><code><?= esc($pt['tax_id'] ?? '-') ?></code></td>
                            <td class="small text-muted text-truncate" style="max-width: 260px;">
                                <?= esc($pt['address'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Tambah Mitra Baru -->
<div class="modal fade" id="addPartnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('partners/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Daftarkan Rekanan Bisnis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Perusahaan / Entitas <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: PT Sinar Mulia Perkasa">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jenis Hubungan Bisnis <span class="text-danger">*</span></label>
                        <select name="type" class="form-select">
                            <option value="customer">Pelanggan (Customer)</option>
                            <option value="supplier">Pemasok / Vendor (Supplier)</option>
                            <option value="both">Pelanggan & Pemasok (Both)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="021-xxxxxxx atau 0812xxxxxxxx">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Korespondensi</label>
                        <input type="email" name="email" class="form-control" placeholder="billing@perusahaan.co.id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">NPWP (Nomor Pokok Wajib Pajak)</label>
                        <input type="text" name="tax_id" class="form-control" placeholder="xx.xxx.xxx.x-xxx.xxx">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Lengkap Kantor / Pabrik</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Nama jalan, gedung, kawasan industri, kota..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-paper">
                        <i class="bi bi-check2"></i> Simpan Rekanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
