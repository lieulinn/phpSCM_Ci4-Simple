<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'category',
        'unit',
        'unit_price',
        'cost_price',
        'stock',
        'min_stock',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get products where stock <= min_stock
     */
    public function getLowStockProducts()
    {
        return $this->where('stock <= min_stock')->orderBy('stock', 'ASC')->findAll();
    }

    /**
     * Adjust product stock atomically
     */
    public function adjustStock(int $productId, int $quantity, string $direction = 'in'): bool
    {
        $product = $this->find($productId);
        if (! $product) {
            return false;
        }

        $newStock = ($direction === 'out') ? ($product['stock'] - $quantity) : ($product['stock'] + $quantity);
        if ($newStock < 0) {
            $newStock = 0;
        }

        return (bool) $this->update($productId, ['stock' => $newStock]);
    }
}
