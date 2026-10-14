<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\PurchaseOrderModel;
use App\Models\StockMovementModel;

class DashboardController extends BaseController
{
    protected InvoiceModel $invoiceModel;
    protected ProductModel $productModel;
    protected PurchaseOrderModel $poModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->invoiceModel       = new InvoiceModel();
        $this->productModel       = new ProductModel();
        $this->poModel            = new PurchaseOrderModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    public function index()
    {
        $financialStats = $this->invoiceModel->getFinancialStats();
        $lowStockProducts = $this->productModel->getLowStockProducts();
        $totalProductsCount = $this->productModel->countAllResults();

        $activePoCount = $this->poModel->whereIn('status', ['ordered', 'draft'])->countAllResults();
        $totalPoAmount = $this->poModel->selectSum('total_amount')->first()['total_amount'] ?? 0;

        $recentInvoices = $this->invoiceModel->getInvoicesWithPartner(null, 5);
        $recentMovements = $this->stockMovementModel->getMovementsWithDetails(5);

        return $this->render('dashboard/index', [
            'title'              => 'Dashboard Supply Chain & Finance',
            'pageTitle'          => 'Ringkasan Bisnis & Rantai Pasok',
            'financialStats'     => $financialStats,
            'lowStockProducts'   => $lowStockProducts,
            'totalProductsCount' => $totalProductsCount,
            'activePoCount'      => $activePoCount,
            'totalPoAmount'      => (float) $totalPoAmount,
            'recentInvoices'     => $recentInvoices,
            'recentMovements'    => $recentMovements,
        ]);
    }
}
