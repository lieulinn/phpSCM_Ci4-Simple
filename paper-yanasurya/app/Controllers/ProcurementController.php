<?php

namespace App\Controllers;

use App\Models\GoodsReceiptModel;
use App\Models\PartnerModel;
use App\Models\ProductModel;
use App\Models\PurchaseOrderModel;

class ProcurementController extends BaseController
{
    protected PurchaseOrderModel $poModel;
    protected GoodsReceiptModel $grModel;
    protected PartnerModel $partnerModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->poModel      = new PurchaseOrderModel();
        $this->grModel      = new GoodsReceiptModel();
        $this->partnerModel = new PartnerModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Procurement overview (Purchase Orders & Goods Receipts)
     */
    public function index()
    {
        $orders = $this->poModel->getOrdersWithPartner(100);
        $receipts = $this->grModel->getReceiptsWithPO(100);
        $suppliers = $this->partnerModel->getSuppliers();
        $newPoNumber = $this->poModel->generatePoNumber();
        $newGrNumber = $this->grModel->generateGrNumber();

        $activeOrders = array_filter($orders, fn ($o) => in_array($o['status'], ['ordered', 'draft']));

        return $this->render('procurement/index', [
            'title'        => 'Pengadaan & Pembelian (Procurement)',
            'pageTitle'    => 'Purchase Orders & Penerimaan Barang',
            'orders'       => $orders,
            'receipts'     => $receipts,
            'suppliers'    => $suppliers,
            'newPoNumber'  => $newPoNumber,
            'newGrNumber'  => $newGrNumber,
            'activeOrders' => $activeOrders,
        ]);
    }

    /**
     * Store new purchase order
     */
    public function storePO()
    {
        $partnerId   = (int) $this->request->getPost('partner_id');
        $poNumber    = trim((string) $this->request->getPost('po_number'));
        $orderDate   = (string) $this->request->getPost('order_date');
        $expectedDate = (string) $this->request->getPost('expected_date');
        $totalAmount = (float) $this->request->getPost('total_amount');
        $notes       = (string) $this->request->getPost('notes');

        if (! $partnerId) {
            return redirect()->back()->withInput()->with('error', 'Supplier wajib dipilih.');
        }

        $this->poModel->insert([
            'po_number'     => $poNumber ?: $this->poModel->generatePoNumber(),
            'partner_id'    => $partnerId,
            'order_date'    => $orderDate ?: date('Y-m-d'),
            'expected_date' => $expectedDate ?: null,
            'status'        => 'ordered',
            'total_amount'  => $totalAmount,
            'notes'         => $notes,
        ]);

        return redirect()->to(site_url('procurement'))->with('success', "Purchase Order {$poNumber} berhasil diterbitkan!");
    }

    /**
     * Record Goods Receipt (GR)
     */
    public function storeGR()
    {
        $poId        = (int) $this->request->getPost('po_id');
        $grNumber    = trim((string) $this->request->getPost('gr_number'));
        $receiptDate = (string) $this->request->getPost('receipt_date');
        $receivedBy  = (string) $this->request->getPost('received_by');
        $notes       = (string) $this->request->getPost('notes');

        $po = $this->poModel->find($poId);
        if (! $po) {
            return redirect()->back()->with('error', 'PO tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->grModel->insert([
            'gr_number'    => $grNumber ?: $this->grModel->generateGrNumber(),
            'po_id'        => $poId,
            'receipt_date' => $receiptDate ?: date('Y-m-d'),
            'received_by'  => $receivedBy ?: $this->session->get('full_name'),
            'notes'        => $notes,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->poModel->update($poId, ['status' => 'received']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses penerimaan barang.');
        }

        return redirect()->to(site_url('procurement'))->with('success', "Penerimaan barang untuk PO {$po['po_number']} berhasil dicatat!");
    }
}
