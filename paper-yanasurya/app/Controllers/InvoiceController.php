<?php

namespace App\Controllers;

use App\Models\InvoiceItemModel;
use App\Models\InvoiceModel;
use App\Models\PartnerModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\StockMovementModel;

class InvoiceController extends BaseController
{
    protected InvoiceModel $invoiceModel;
    protected InvoiceItemModel $itemModel;
    protected PartnerModel $partnerModel;
    protected ProductModel $productModel;
    protected PaymentModel $paymentModel;
    protected StockMovementModel $movementModel;

    public function __construct()
    {
        $this->invoiceModel  = new InvoiceModel();
        $this->itemModel     = new InvoiceItemModel();
        $this->partnerModel  = new PartnerModel();
        $this->productModel  = new ProductModel();
        $this->paymentModel  = new PaymentModel();
        $this->movementModel = new StockMovementModel();
    }

    /**
     * List all invoices
     */
    public function index()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $invoices = $this->invoiceModel->getInvoicesWithPartner($status, 100);
        $stats = $this->invoiceModel->getFinancialStats();

        return $this->render('invoices/index', [
            'title'          => 'Daftar Invoice Penjualan',
            'pageTitle'      => 'Kelola Invoice & Penagihan',
            'invoices'       => $invoices,
            'currentStatus'  => $status,
            'stats'          => $stats,
        ]);
    }

    /**
     * Create invoice form (Paper.id style)
     */
    public function create()
    {
        $customers = $this->partnerModel->getCustomers();
        $products = $this->productModel->orderBy('name', 'ASC')->findAll();
        $newInvoiceNumber = $this->invoiceModel->generateInvoiceNumber();

        return $this->render('invoices/create', [
            'title'            => 'Buat Invoice Baru - Paper Yana Surya',
            'pageTitle'        => 'Buat Invoice Penjualan',
            'customers'        => $customers,
            'products'         => $products,
            'newInvoiceNumber' => $newInvoiceNumber,
            'today'            => date('Y-m-d'),
            'defaultDueDate'   => date('Y-m-d', strtotime('+14 days')),
        ]);
    }

    /**
     * Store new invoice
     */
    public function store()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $partnerId      = (int) $this->request->getPost('partner_id');
        $invoiceNumber  = trim((string) $this->request->getPost('invoice_number'));
        $issueDate      = (string) $this->request->getPost('issue_date');
        $dueDate        = (string) $this->request->getPost('due_date');
        $taxRate        = (float) ($this->request->getPost('tax_rate') ?? 11);
        $discountAmount = (float) ($this->request->getPost('discount_amount') ?? 0);
        $notes          = (string) $this->request->getPost('notes');
        $terms          = (string) $this->request->getPost('terms');

        $itemProductIds = $this->request->getPost('product_id') ?? [];
        $itemNames      = $this->request->getPost('item_name') ?? [];
        $itemDescs      = $this->request->getPost('item_description') ?? [];
        $itemQuantities = $this->request->getPost('quantity') ?? [];
        $itemPrices     = $this->request->getPost('unit_price') ?? [];
        $itemDiscounts  = $this->request->getPost('discount') ?? [];

        $subtotal = 0;
        $itemsToInsert = [];

        foreach ($itemNames as $i => $name) {
            if (empty(trim($name))) {
                continue;
            }

            $qty   = max(1, (int) ($itemQuantities[$i] ?? 1));
            $price = (float) ($itemPrices[$i] ?? 0);
            $disc  = (float) ($itemDiscounts[$i] ?? 0);
            $lineTotal = max(0, ($qty * $price) - $disc);

            $subtotal += $lineTotal;

            $prodId = ! empty($itemProductIds[$i]) ? (int) $itemProductIds[$i] : null;

            $itemsToInsert[] = [
                'product_id'  => $prodId,
                'item_name'   => $name,
                'description' => $itemDescs[$i] ?? '',
                'quantity'    => $qty,
                'unit_price'  => $price,
                'discount'    => $disc,
                'total'       => $lineTotal,
            ];
        }

        if (empty($itemsToInsert)) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Invoice harus memiliki minimal 1 baris item produk.');
        }

        $taxAmount = ($subtotal - $discountAmount > 0) ? (($subtotal - $discountAmount) * ($taxRate / 100)) : 0;
        $totalAmount = max(0, ($subtotal - $discountAmount) + $taxAmount);

        $invoiceData = [
            'invoice_number'  => $invoiceNumber ?: $this->invoiceModel->generateInvoiceNumber(),
            'partner_id'      => $partnerId,
            'issue_date'      => $issueDate,
            'due_date'        => $dueDate,
            'status'          => 'unpaid',
            'subtotal'        => $subtotal,
            'tax_rate'        => $taxRate,
            'tax_amount'      => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount'    => $totalAmount,
            'paid_amount'     => 0.00,
            'notes'           => $notes,
            'terms'           => $terms,
        ];

        $invoiceId = $this->invoiceModel->insert($invoiceData, true);

        // Insert items and adjust stock
        foreach ($itemsToInsert as $item) {
            $item['invoice_id'] = $invoiceId;
            $this->itemModel->insert($item);

            // Deduct stock if product selected
            if ($item['product_id']) {
                $this->productModel->adjustStock($item['product_id'], $item['quantity'], 'out');
                $this->movementModel->insert([
                    'product_id'     => $item['product_id'],
                    'reference_type' => 'invoice',
                    'reference_id'   => $invoiceId,
                    'movement_type'  => 'out',
                    'quantity'       => $item['quantity'],
                    'unit_cost'      => $item['unit_price'],
                    'notes'          => 'Pengurangan stok untuk Invoice #' . $invoiceData['invoice_number'],
                    'created_by'     => $this->session->get('user_id'),
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan invoice. Terjadi kesalahan database.');
        }

        return redirect()->to(site_url("invoices/view/{$invoiceId}"))->with('success', "Invoice #{$invoiceData['invoice_number']} berhasil diterbitkan!");
    }

    /**
     * View Invoice in Paper.id style
     */
    public function view(int $id)
    {
        $invoice = $this->invoiceModel->getInvoiceDetails($id);
        if (! $invoice) {
            return redirect()->to(site_url('invoices'))->with('error', 'Invoice tidak ditemukan.');
        }

        $items = $this->itemModel->getItemsByInvoice($id);
        $payments = $this->paymentModel->getPaymentsByInvoice($id);

        return $this->render('invoices/view', [
            'title'     => 'Invoice ' . $invoice['invoice_number'],
            'pageTitle' => 'Rincian Faktur Penjualan',
            'invoice'   => $invoice,
            'items'     => $items,
            'payments'  => $payments,
        ]);
    }

    /**
     * Clean PDF / Print View
     */
    public function pdf(int $id)
    {
        $invoice = $this->invoiceModel->getInvoiceDetails($id);
        if (! $invoice) {
            return redirect()->to(site_url('invoices'))->with('error', 'Invoice tidak ditemukan.');
        }

        $items = $this->itemModel->getItemsByInvoice($id);
        $payments = $this->paymentModel->getPaymentsByInvoice($id);

        return view('invoices/view', [
            'title'     => 'Print Invoice ' . $invoice['invoice_number'],
            'invoice'   => $invoice,
            'items'     => $items,
            'payments'  => $payments,
            'printOnly' => true,
        ]);
    }

    /**
     * Record payment for invoice
     */
    public function payment(int $id)
    {
        $invoice = $this->invoiceModel->find($id);
        if (! $invoice) {
            return redirect()->back()->with('error', 'Invoice tidak ditemukan.');
        }

        $amount        = (float) $this->request->getPost('amount');
        $paymentDate   = (string) $this->request->getPost('payment_date');
        $paymentMethod = (string) $this->request->getPost('payment_method');
        $referenceNo   = (string) $this->request->getPost('reference_no');
        $notes         = (string) $this->request->getPost('notes');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Nominal pembayaran harus lebih besar dari 0.');
        }

        $newPaidAmount = (float) $invoice['paid_amount'] + $amount;
        $totalAmount   = (float) $invoice['total_amount'];

        $newStatus = ($newPaidAmount >= $totalAmount) ? 'paid' : 'partially_paid';

        $db = \Config\Database::connect();
        $db->transStart();

        $this->paymentModel->insert([
            'invoice_id'     => $id,
            'payment_number' => $this->paymentModel->generatePaymentNumber(),
            'payment_date'   => $paymentDate ?: date('Y-m-d'),
            'amount'         => $amount,
            'payment_method' => $paymentMethod,
            'reference_no'   => $referenceNo,
            'notes'          => $notes,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->invoiceModel->update($id, [
            'paid_amount' => $newPaidAmount,
            'status'      => $newStatus,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran.');
        }

        return redirect()->to(site_url("invoices/view/{$id}"))->with('success', 'Pembayaran sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dicatat!');
    }
}
