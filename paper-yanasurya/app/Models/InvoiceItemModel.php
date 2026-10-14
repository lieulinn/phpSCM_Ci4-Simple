<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItemModel extends Model
{
    protected $table            = 'invoice_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_id',
        'product_id',
        'item_name',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'total',
    ];

    protected $useTimestamps = false;

    /**
     * Get items for a given invoice
     */
    public function getItemsByInvoice(int $invoiceId)
    {
        return $this->select('invoice_items.*, products.code as product_code, products.unit')
                    ->join('products', 'products.id = invoice_items.product_id', 'left')
                    ->where('invoice_id', $invoiceId)
                    ->findAll();
    }
}
