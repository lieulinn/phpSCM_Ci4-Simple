<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table            = 'purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_number',
        'partner_id',
        'order_date',
        'expected_date',
        'status', // draft, ordered, partially_received, received, cancelled
        'total_amount',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get purchase orders joined with supplier partner
     */
    public function getOrdersWithPartner(int $limit = 50)
    {
        return $this->select('purchase_orders.*, partners.name as supplier_name, partners.phone as supplier_phone, partners.email as supplier_email')
                    ->join('partners', 'partners.id = purchase_orders.partner_id', 'left')
                    ->orderBy('purchase_orders.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Generate sequential PO number (PO-YS-YYYY-XXX)
     */
    public function generatePoNumber(): string
    {
        $year = date('Y');
        $prefix = "PO-YS-{$year}-";
        $last = $this->like('po_number', $prefix, 'after')->orderBy('id', 'DESC')->first();

        if ($last && preg_match('/(\d+)$/', $last['po_number'], $matches)) {
            $nextSeq = str_pad((int) $matches[1] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '001';
        }

        return $prefix . $nextSeq;
    }
}
