<?php

namespace App\Models;

use CodeIgniter\Model;

class GoodsReceiptModel extends Model
{
    protected $table            = 'goods_receipts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'gr_number',
        'po_id',
        'receipt_date',
        'received_by',
        'notes',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get receipts joined with PO and supplier
     */
    public function getReceiptsWithPO(int $limit = 50)
    {
        return $this->select('goods_receipts.*, purchase_orders.po_number, purchase_orders.total_amount, partners.name as supplier_name')
                    ->join('purchase_orders', 'purchase_orders.id = goods_receipts.po_id', 'left')
                    ->join('partners', 'partners.id = purchase_orders.partner_id', 'left')
                    ->orderBy('goods_receipts.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Generate sequential Goods Receipt number (GR-YS-YYYY-XXX)
     */
    public function generateGrNumber(): string
    {
        $year = date('Y');
        $prefix = "GR-YS-{$year}-";
        $last = $this->like('gr_number', $prefix, 'after')->orderBy('id', 'DESC')->first();

        if ($last && preg_match('/(\d+)$/', $last['gr_number'], $matches)) {
            $nextSeq = str_pad((int) $matches[1] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '001';
        }

        return $prefix . $nextSeq;
    }
}
