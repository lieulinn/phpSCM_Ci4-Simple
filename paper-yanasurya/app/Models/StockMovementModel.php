<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table            = 'stock_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'reference_type', // purchase_order, invoice, adjustment, initial, goods_receipt
        'reference_id',
        'movement_type', // in, out, adjustment
        'quantity',
        'unit_cost',
        'notes',
        'created_by',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get stock movements with joined product & user details
     */
    public function getMovementsWithDetails(int $limit = 20)
    {
        return $this->select('stock_movements.*, products.name as product_name, products.code as product_code, products.unit, users.full_name as creator_name')
                    ->join('products', 'products.id = stock_movements.product_id', 'left')
                    ->join('users', 'users.id = stock_movements.created_by', 'left')
                    ->orderBy('stock_movements.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
