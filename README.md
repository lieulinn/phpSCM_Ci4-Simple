# Paper-Yanasurya
**Supply Chain Management & Digital Invoicing Hub**  
Sistem manajemen rantai pasok dan invoice digital untuk **PT. Yanasurya Bhaktipersada** (Produsen Plastic Pallet & Packaging).

---

## Deskripsi
Paper-Yanasurya adalah sistem SCM + Invoicing berbasis web yang dibangun dengan **CodeIgniter 4**.  
Sistem ini mendukung alur kerja lengkap mulai dari manajemen vendor/customer, inventory multi-gudang, Sales Order, Purchase Order, Goods Receipt, hingga invoice digital bergaya Paper.id (dengan PPN 11%, status pembayaran, dan export PDF).

**Super Admin awal:**  
M.Lie Ulin Nuha – Super Administrator / SCM System Owner

---

## Fitur Utama

### 1. Authentication & RBAC
- Login aman dengan password hashing
- Role-Based Access Control:
  - Super Admin
  - Procurement Officer
  - Warehouse Manager
  - Finance/Sales Officer
  - Client/Vendor Access

### 2. Vendor & Customer Management (CRM/SRM)
- Registrasi Supplier & Customer
- Pengaturan Credit Term & Credit Limit per partner

### 3. Inventory & Warehouse Control
- Multi-warehouse (Raw Material, Finished Goods, Scrap/Regrind)
- Batch/Lot & Serial Tracking
- Stock Movement Log (IN / OUT / TRANSFER)
- Alert stok minimum otomatis

### 4. Sales Order & Invoicing (Paper.id Style)
- Generate Sales Invoice & Purchase Invoice
- Perhitungan PPN 11% & PPh
- Status: Unpaid, Partially Paid, Paid, Overdue
- Delivery Order (Surat Jalan) terintegrasi
- Export PDF dengan watermark/company stamp

### 5. Procurement
- Purchase Requisition → Purchase Order (dengan approval flow)
- Goods Receipt Note (GRN)
- 3-Way Matching (PO – GRN – Vendor Invoice)

### 6. Financial Dashboard
- Total Piutang (Accounts Receivable) & Hutang (Accounts Payable)
- Chart Revenue vs Procurement Cost
- Stock Turnover & Overdue Invoice Alert
- Dashboard khusus Super Admin (M.Lie Ulin Nuha)

---

## Tech Stack
- **Framework**: CodeIgniter 4 (MVC)
- **Database**: MySQL / MariaDB (dengan Foreign Key constraints)
- **Frontend**: Bootstrap 5 (Clean Paper/Dashboard Theme)
- **Auth**: Custom + Role-based (bisa diperluas dengan Shield)
- **PDF**: Dompdf / mPDF (opsional)

---

## Persyaratan Sistem
- PHP >= 8.1
- Composer
- MySQL 8.0+ / MariaDB 10.4+
- Extension: intl, mbstring, json, mysqlnd, curl

---

## Instalasi

1. **Clone / Download project**
   ```bash
   git clone <repository-url> paper-yanasurya
   cd paper-yanasurya

Install dependencyBashcomposer install
Setup environmentBashcp env .envEdit file .env:envCI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = paper_yanasurya
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
Buat databaseSQLCREATE DATABASE paper_yanasurya CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
Jalankan migration & seederBashphp spark migrate
php spark db:seed SuperAdminSeeder
php spark db:seed SampleSCMSeeder   # (opsional – data contoh)
Set permission writableBashchmod -R 777 writable/
Jalankan serverBashphp spark serveBuka: http://localhost:8080


Akun Default (Super Admin)

























FieldValueNamaM.Lie Ulin NuhaEmailadmin@yanasurya.co.idPasswordYanasurya@2024RoleSuper Administrator
Penting: Segera ganti password setelah login pertama kali di production.

Struktur Folder Utama
textapp/
├── Controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── InvoiceController.php
│   ├── InventoryController.php
│   ├── PartnerController.php
│   └── ProcurementController.php
├── Models/
├── Database/
│   ├── Migrations/
│   └── Seeds/
├── Views/
│   ├── layouts/main.php
│   ├── invoices/
│   ├── inventory/
│   └── dashboard/
└── Filters/

Modul yang Tersedia

/dashboard – Executive Dashboard
/invoices – Manajemen Invoice (Sales & Purchase)
/inventory – Stock & Warehouse
/partners – Vendor & Customer
/procurement – PR → PO → GRN


Pengembangan Lanjutan (Recommended)

Integrasi Dompdf untuk export PDF invoice
Notifikasi email (overdue, low stock)
API endpoint untuk mobile / partner portal
Audit log & activity tracking
Multi-currency (jika diperlukan)


Keamanan

Password di-hash menggunakan password_hash()
Query Builder + prepared statements
Foreign Key constraints
Transaction database untuk operasi stock + invoice
Role-based access control


Lisensi
Proprietary – Hanya untuk penggunaan internal PT. Yanasurya Bhaktipersada.

Dilarang mendistribusikan tanpa izin.

Kontak Support
System Owner: M.Lie Ulin Nuha

Email: admin@yanasurya.co.id

Paper-Yanasurya – Digital Supply Chain & Invoicing Solution

PT. Yanasurya Bhaktipersada © 2024–2026
text---

File `README.md` di atas sudah siap digunakan.  
Salin saja isinya ke file `README.md` di root project Anda.