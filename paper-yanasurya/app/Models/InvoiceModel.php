<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_number',
        'partner_id',
        'issue_date',
        'due_date',
        'status', // draft, unpaid, partially_paid, paid, overdue, cancelled
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'notes',
        'terms',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get invoices list joined with partner details
     */
    public function getInvoicesWithPartner(?string $status = null, int $limit = 50)
    {
        $builder = $this->select('invoices.*, partners.name as partner_name, partners.email as partner_email, partners.phone as partner_phone')
                        ->join('partners', 'partners.id = invoices.partner_id', 'left')
                        ->orderBy('invoices.id', 'DESC');

        if ($status && $status !== 'all') {
            $builder->where('invoices.status', $status);
        }

        return $builder->limit($limit)->findAll();
    }

    /**
     * Get complete invoice details including customer information
     */
    public function getInvoiceDetails(int $id)
    {
        return $this->select('invoices.*, partners.name as partner_name, partners.email as partner_email, partners.phone as partner_phone, partners.address as partner_address, partners.tax_id as partner_tax_id')
                    ->join('partners', 'partners.id = invoices.partner_id', 'left')
                    ->where('invoices.id', $id)
                    ->first();
    }

    /**
     * Generate sequential invoice number (e.g. INV/YS/2024/0004)
     */
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "INV/YS/{$year}/";
        $last = $this->like('invoice_number', $prefix, 'after')->orderBy('id', 'DESC')->first();

        if ($last && preg_match('/(\d+)$/', $last['invoice_number'], $matches)) {
            $nextSeq = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $prefix . $nextSeq;
    }

    /**
     * Get financial KPIs
     */
    public function getFinancialStats(): array
    {
        $totalRevenue = $this->selectSum('paid_amount')->first()['paid_amount'] ?? 0;
        
        $unpaidReceivables = $this->select('SUM(total_amount - paid_amount) as outstanding')
                                  ->whereIn('status', ['unpaid', 'partially_paid', 'overdue'])
                                  ->first()['outstanding'] ?? 0;

        $overdueCount = $this->where('status', 'overdue')->countAllResults();
        $unpaidCount = $this->whereIn('status', ['unpaid', 'partially_paid'])->countAllResults();
        $paidCount = $this->where('status', 'paid')->countAllResults();

        return [
            'total_revenue'      => (float) $totalRevenue,
            'unpaid_receivables' => (float) $unpaidReceivables,
            'overdue_count'      => $overdueCount,
            'unpaid_count'       => $unpaidCount,
            'paid_count'         => $paidCount,
        ];
    }
}
