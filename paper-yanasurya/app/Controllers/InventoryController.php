<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\StockMovementModel;

class InventoryController extends BaseController
{
    protected ProductModel $productModel;
    protected StockMovementModel $movementModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->movementModel = new StockMovementModel();
    }

    /**
     * Inventory list and stock movements
     */
    public function index()
    {
        $products = $this->productModel->orderBy('name', 'ASC')->findAll();
        $movements = $this->movementModel->getMovementsWithDetails(30);

        $totalStockItems = 0;
        $totalValuation = 0;
        $lowStockCount = 0;

        foreach ($products as $p) {
            $totalStockItems += (int) $p['stock'];
            $totalValuation += ((int) $p['stock'] * (float) $p['cost_price']);
            if ($p['stock'] <= $p['min_stock']) {
                $lowStockCount++;
            }
        }

        return $this->render('inventory/index', [
            'title'           => 'Manajemen Inventaris & Gudang',
            'pageTitle'       => 'Stok Barang & Log Pergudangan',
            'products'        => $products,
            'movements'       => $movements,
            'totalStockItems' => $totalStockItems,
            'totalValuation'  => $totalValuation,
            'lowStockCount'   => $lowStockCount,
        ]);
    }

    /**
     * Store new product
     */
    public function store()
    {
        $code = trim((string) $this->request->getPost('code'));
        $name = trim((string) $this->request->getPost('name'));

        if (empty($code) || empty($name)) {
            return redirect()->back()->withInput()->with('error', 'Kode produk dan nama barang wajib diisi.');
        }

        $existing = $this->productModel->where('code', $code)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Kode produk {$code} sudah terdaftar.");
        }

        $unitPrice = (float) $this->request->getPost('unit_price');
        $costPrice = (float) $this->request->getPost('cost_price');
        $stock     = (int) $this->request->getPost('stock');
        $minStock  = (int) $this->request->getPost('min_stock');

        $productId = $this->productModel->insert([
            'code'        => $code,
            'name'        => $name,
            'category'    => (string) $this->request->getPost('category') ?: 'General',
            'unit'        => (string) $this->request->getPost('unit') ?: 'pcs',
            'unit_price'  => $unitPrice,
            'cost_price'  => $costPrice,
            'stock'       => $stock,
            'min_stock'   => $minStock,
            'description' => (string) $this->request->getPost('description'),
        ], true);

        // Record initial stock movement if > 0
        if ($stock > 0) {
            $this->movementModel->insert([
                'product_id'     => $productId,
                'reference_type' => 'initial',
                'movement_type'  => 'in',
                'quantity'       => $stock,
                'unit_cost'      => $costPrice,
                'notes'          => 'Stok awal penambahan produk baru',
                'created_by'     => $this->session->get('user_id'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to(site_url('inventory'))->with('success', "Produk [{$code}] {$name} berhasil ditambahkan!");
    }

    /**
     * Record stock movement (in, out, adjustment)
     */
    public function movement()
    {
        $productId    = (int) $this->request->getPost('product_id');
        $movementType = (string) $this->request->getPost('movement_type'); // in, out, adjustment
        $quantity     = abs((int) $this->request->getPost('quantity'));
        $notes        = (string) $this->request->getPost('notes');

        $product = $this->productModel->find($productId);
        if (! $product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        if ($quantity <= 0) {
            return redirect()->back()->with('error', 'Jumlah kuantitas mutasi harus lebih besar dari 0.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->productModel->adjustStock($productId, $quantity, $movementType);

        $this->movementModel->insert([
            'product_id'     => $productId,
            'reference_type' => 'adjustment',
            'movement_type'  => $movementType,
            'quantity'       => $quantity,
            'unit_cost'      => $product['cost_price'],
            'notes'          => $notes ?: "Penyesuaian stok gudang ({$movementType})",
            'created_by'     => $this->session->get('user_id'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses mutasi stok.');
        }

        return redirect()->to(site_url('inventory'))->with('success', "Mutasi stok {$quantity} {$product['unit']} untuk {$product['name']} berhasil dicatat!");
    }
}
