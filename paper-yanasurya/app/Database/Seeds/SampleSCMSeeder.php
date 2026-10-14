<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleSCMSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        // 1. Seed Partners
        $partners = [
            [
                'id'         => 1,
                'name'       => 'PT Surya Packaging Pratama',
                'type'       => 'supplier',
                'email'      => 'sales@suryapackaging.co.id',
                'phone'      => '021-5551234',
                'address'    => 'Kawasan Industri MM2100 Blok C-4, Cikarang Barat, Bekasi',
                'tax_id'     => '01.234.567.8-412.000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 2,
                'name'       => 'CV Mitra Kertas Indonesia',
                'type'       => 'supplier',
                'email'      => 'info@mitrakertas.com',
                'phone'      => '031-7788990',
                'address'    => 'Jl. Rungkut Industri Raya No. 45, Surabaya, Jawa Timur',
                'tax_id'     => '02.345.678.9-604.000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 3,
                'name'       => 'PT Sinar Logistik Nusantara',
                'type'       => 'customer',
                'email'      => 'procurement@sinarlogistik.co.id',
                'phone'      => '021-88991122',
                'address'    => 'Jl. Gatot Subroto Kav. 18, Jakarta Selatan',
                'tax_id'     => '03.456.789.0-015.000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 4,
                'name'       => 'CV Maju Bersama Sentosa',
                'type'       => 'customer',
                'email'      => 'admin@majubersama.id',
                'phone'      => '022-7201992',
                'address'    => 'Jl. Soekarno Hatta No. 204, Bandung, Jawa Barat',
                'tax_id'     => '04.567.890.1-428.000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 5,
                'name'       => 'Toko Retail Surya Abadi',
                'type'       => 'both',
                'email'      => 'suryaabadi@gmail.com',
                'phone'      => '081288997766',
                'address'    => 'Jl. Pahlawan No. 88, Semarang, Jawa Tengah',
                'tax_id'     => '05.678.901.2-503.000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($partners as $partner) {
            $existing = $this->db->table('partners')->where('id', $partner['id'])->get()->getRow();
            if (! $existing) {
                $this->db->table('partners')->insert($partner);
            }
        }

        // 2. Seed Products
        $products = [
            [
                'id'          => 1,
                'code'        => 'BOX-KRN-01',
                'name'        => 'Corrugated Box K125/M125 B/F (Uk. 40x30x30 cm)',
                'category'    => 'Corrugated Box',
                'unit'        => 'pcs',
                'unit_price'  => 14500.00,
                'cost_price'  => 9800.00,
                'stock'       => 1250,
                'min_stock'   => 200,
                'description' => 'Karton box standar double wall untuk pengemasan ekspedisi kargo berat.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 2,
                'code'        => 'BOX-KRN-02',
                'name'        => 'Corrugated Box Single Wall K150 (Uk. 25x20x15 cm)',
                'category'    => 'Corrugated Box',
                'unit'        => 'pcs',
                'unit_price'  => 6800.00,
                'cost_price'  => 4200.00,
                'stock'       => 85, // Low stock alert!
                'min_stock'   => 150,
                'description' => 'Karton box ukuran sedang untuk e-commerce dan pengiriman cepat.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 3,
                'code'        => 'FLM-STR-50',
                'name'        => 'Stretch Film Plastik Wrapping 50cm x 200m (17 Micron)',
                'category'    => 'Packaging Film',
                'unit'        => 'roll',
                'unit_price'  => 68000.00,
                'cost_price'  => 49000.00,
                'stock'       => 320,
                'min_stock'   => 50,
                'description' => 'Plastik wrapping bening elastis kualitas tinggi untuk pengikatan pallet gudang.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 4,
                'code'        => 'PLT-WDN-12',
                'name'        => 'Pallet Kayu Standar Ekspor (120 x 100 x 14 cm)',
                'category'    => 'Pallet & Handling',
                'unit'        => 'unit',
                'unit_price'  => 185000.00,
                'cost_price'  => 130000.00,
                'stock'       => 180,
                'min_stock'   => 30,
                'description' => 'Pallet kayu oven ISPM 15 anti jamur untuk distribusi pergudangan logistik.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 5,
                'code'        => 'LKB-CLT-48',
                'name'        => 'Lakban Coklat Opp Tape 48mm x 100 Yard',
                'category'    => 'Adhesive Tape',
                'unit'        => 'roll',
                'unit_price'  => 12500.00,
                'cost_price'  => 8200.00,
                'stock'       => 540,
                'min_stock'   => 100,
                'description' => 'Lakban coklat daya rekat prima untuk penutupan kardus kargo.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 6,
                'code'        => 'BBL-WRP-12',
                'name'        => 'Bubble Wrap Roll Tebal 1.25m x 50m (Bening)',
                'category'    => 'Cushioning',
                'unit'        => 'roll',
                'unit_price'  => 165000.00,
                'cost_price'  => 115000.00,
                'stock'       => 45, // Low stock alert!
                'min_stock'   => 50,
                'description' => 'Plastik gelembung pelindung benturan peredam guncangan barang pecah belah.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        foreach ($products as $product) {
            $existing = $this->db->table('products')->where('id', $product['id'])->get()->getRow();
            if (! $existing) {
                $this->db->table('products')->insert($product);
            }
        }

        // 3. Seed Stock Movements
        $movements = [
            [
                'product_id'     => 1,
                'reference_type' => 'initial',
                'reference_id'   => null,
                'movement_type'  => 'in',
                'quantity'       => 1500,
                'unit_cost'      => 9800.00,
                'notes'          => 'Saldo awal stok gudang utama Yana Surya',
                'created_by'     => 1,
                'created_at'     => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
            [
                'product_id'     => 1,
                'reference_type' => 'invoice',
                'reference_id'   => 1,
                'movement_type'  => 'out',
                'quantity'       => 250,
                'unit_cost'      => 9800.00,
                'notes'          => 'Pengeluaran barang untuk pesanan PT Sinar Logistik Nusantara',
                'created_by'     => 1,
                'created_at'     => date('Y-m-d H:i:s', strtotime('-4 days')),
            ],
            [
                'product_id'     => 3,
                'reference_type' => 'initial',
                'reference_id'   => null,
                'movement_type'  => 'in',
                'quantity'       => 350,
                'unit_cost'      => 49000.00,
                'notes'          => 'Penerimaan stok dari supplier PT Surya Packaging Pratama',
                'created_by'     => 1,
                'created_at'     => date('Y-m-d H:i:s', strtotime('-8 days')),
            ],
        ];

        foreach ($movements as $m) {
            $this->db->table('stock_movements')->insert($m);
        }

        // 4. Seed Purchase Orders
        $pos = [
            [
                'id'            => 1,
                'po_number'     => 'PO-YS-2024-001',
                'partner_id'    => 1,
                'order_date'    => date('Y-m-d', strtotime('-15 days')),
                'expected_date' => date('Y-m-d', strtotime('-10 days')),
                'status'        => 'received',
                'total_amount'  => 18500000.00,
                'notes'         => 'Pengadaan material stretch film dan box karton gelombang.',
                'created_at'    => date('Y-m-d H:i:s', strtotime('-15 days')),
                'updated_at'    => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
            [
                'id'            => 2,
                'po_number'     => 'PO-YS-2024-002',
                'partner_id'    => 2,
                'order_date'    => date('Y-m-d', strtotime('-2 days')),
                'expected_date' => date('Y-m-d', strtotime('+5 days')),
                'status'        => 'ordered',
                'total_amount'  => 12400000.00,
                'notes'         => 'Restock kertas karton single wall untuk pengiriman akhir bulan.',
                'created_at'    => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'    => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
        ];

        foreach ($pos as $po) {
            $existing = $this->db->table('purchase_orders')->where('id', $po['id'])->get()->getRow();
            if (! $existing) {
                $this->db->table('purchase_orders')->insert($po);
            }
        }

        // 5. Seed Goods Receipt
        $grs = [
            [
                'id'           => 1,
                'gr_number'    => 'GR-YS-2024-001',
                'po_id'        => 1,
                'receipt_date' => date('Y-m-d', strtotime('-10 days')),
                'received_by'  => 'Budi Santoso (Warehouse)',
                'notes'        => 'Barang tiba dalam kondisi prima sesuai surat jalan No. SJ-SPP-9921',
                'created_at'   => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
        ];

        foreach ($grs as $gr) {
            $existing = $this->db->table('goods_receipts')->where('id', $gr['id'])->get()->getRow();
            if (! $existing) {
                $this->db->table('goods_receipts')->insert($gr);
            }
        }

        // 6. Seed Invoices (Paper.id style)
        $invoices = [
            [
                'id'              => 1,
                'invoice_number'  => 'INV/YS/2024/0001',
                'partner_id'      => 3,
                'issue_date'      => date('Y-m-d', strtotime('-5 days')),
                'due_date'        => date('Y-m-d', strtotime('+25 days')),
                'status'          => 'paid',
                'subtotal'        => 4975000.00,
                'tax_rate'        => 11.00,
                'tax_amount'      => 547250.00,
                'discount_amount' => 0.00,
                'total_amount'    => 5522250.00,
                'paid_amount'     => 5522250.00,
                'notes'           => 'Terima kasih atas kepercayaan Anda bermitra dengan PT Yana Surya Packaging.',
                'terms'           => 'Pembayaran lunas via transfer Bank BCA / Mandiri. Faktur pajak terbit sesuai tanggal invoice.',
                'created_at'      => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at'      => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'id'              => 2,
                'invoice_number'  => 'INV/YS/2024/0002',
                'partner_id'      => 4,
                'issue_date'      => date('Y-m-d', strtotime('-2 days')),
                'due_date'        => date('Y-m-d', strtotime('+12 days')),
                'status'          => 'unpaid',
                'subtotal'        => 8490000.00,
                'tax_rate'        => 11.00,
                'tax_amount'      => 933900.00,
                'discount_amount' => 150000.00,
                'total_amount'    => 9273900.00,
                'paid_amount'     => 0.00,
                'notes'           => 'Pesanan packaging logistik tahap I CV Maju Bersama Sentosa.',
                'terms'           => 'Jatuh tempo 14 hari sejak invoice diterbitkan. Dikenakan denda keterlambatan 1%/minggu.',
                'created_at'      => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'      => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'id'              => 3,
                'invoice_number'  => 'INV/YS/2024/0003',
                'partner_id'      => 5,
                'issue_date'      => date('Y-m-d', strtotime('-18 days')),
                'due_date'        => date('Y-m-d', strtotime('-3 days')),
                'status'          => 'overdue',
                'subtotal'        => 3700000.00,
                'tax_rate'        => 11.00,
                'tax_amount'      => 407000.00,
                'discount_amount' => 50000.00,
                'total_amount'    => 4057000.00,
                'paid_amount'     => 1500000.00,
                'notes'           => 'Pengiriman retail lakban & bubble wrap Semarang.',
                'terms'           => 'Telah lewat jatuh tempo. Harap segera melakukan sisa pelunasan.',
                'created_at'      => date('Y-m-d H:i:s', strtotime('-18 days')),
                'updated_at'      => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
        ];

        foreach ($invoices as $inv) {
            $existing = $this->db->table('invoices')->where('id', $inv['id'])->get()->getRow();
            if (! $existing) {
                $this->db->table('invoices')->insert($inv);
            }
        }

        // 7. Seed Invoice Items
        $items = [
            // Items for INV 1
            [
                'invoice_id'  => 1,
                'product_id'  => 1,
                'item_name'   => 'Corrugated Box K125/M125 B/F (Uk. 40x30x30 cm)',
                'description' => '250 pcs kardus gelombang standar',
                'quantity'    => 250,
                'unit_price'  => 14500.00,
                'discount'    => 0.00,
                'total'       => 3625000.00,
            ],
            [
                'invoice_id'  => 1,
                'product_id'  => 3,
                'item_name'   => 'Stretch Film Plastik Wrapping 50cm x 200m',
                'description' => '20 roll plastik wrapping bening',
                'quantity'    => 20,
                'unit_price'  => 67500.00,
                'discount'    => 0.00,
                'total'       => 1350000.00,
            ],
            // Items for INV 2
            [
                'invoice_id'  => 2,
                'product_id'  => 4,
                'item_name'   => 'Pallet Kayu Standar Ekspor (120 x 100 x 14 cm)',
                'description' => '30 unit pallet kayu oven standar ISPM',
                'quantity'    => 30,
                'unit_price'  => 185000.00,
                'discount'    => 0.00,
                'total'       => 5550000.00,
            ],
            [
                'invoice_id'  => 2,
                'product_id'  => 6,
                'item_name'   => 'Bubble Wrap Roll Tebal 1.25m x 50m (Bening)',
                'description' => '15 roll bubble wrap',
                'quantity'    => 15,
                'unit_price'  => 165000.00,
                'discount'    => 0.00,
                'total'       => 2475000.00,
            ],
            [
                'invoice_id'  => 2,
                'product_id'  => 5,
                'item_name'   => 'Lakban Coklat Opp Tape 48mm x 100 Yard',
                'description' => '37 roll lakban',
                'quantity'    => 37,
                'unit_price'  => 12500.00,
                'discount'    => 0.00,
                'total'       => 465000.00,
            ],
            // Items for INV 3
            [
                'invoice_id'  => 3,
                'product_id'  => 4,
                'item_name'   => 'Pallet Kayu Standar Ekspor (120 x 100 x 14 cm)',
                'description' => '20 unit pallet kayu',
                'quantity'    => 20,
                'unit_price'  => 185000.00,
                'discount'    => 0.00,
                'total'       => 3700000.00,
            ],
        ];

        foreach ($items as $item) {
            $this->db->table('invoice_items')->insert($item);
        }

        // 8. Seed Payments
        $payments = [
            [
                'invoice_id'     => 1,
                'payment_number' => 'PAY-YS-2024-0001',
                'payment_date'   => date('Y-m-d', strtotime('-3 days')),
                'amount'         => 5522250.00,
                'payment_method' => 'bank_transfer',
                'reference_no'   => 'TRF-BCA-981273941',
                'notes'          => 'Pelunasan penuh tagihan INV/YS/2024/0001 via BCA Virtual Account',
                'created_at'     => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'invoice_id'     => 3,
                'payment_number' => 'PAY-YS-2024-0002',
                'payment_date'   => date('Y-m-d', strtotime('-10 days')),
                'amount'         => 1500000.00,
                'payment_method' => 'bank_transfer',
                'reference_no'   => 'TRF-MND-481920381',
                'notes'          => 'Pembayaran uang muka / termin 1 sebesar Rp 1.500.000',
                'created_at'     => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
        ];

        foreach ($payments as $pay) {
            $existing = $this->db->table('payments')->where('payment_number', $pay['payment_number'])->get()->getRow();
            if (! $existing) {
                $this->db->table('payments')->insert($pay);
            }
        }
    }
}
