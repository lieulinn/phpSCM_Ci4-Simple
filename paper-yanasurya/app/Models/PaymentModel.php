<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'notes',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get payments for an invoice
     */
    public function getPaymentsByInvoice(int $invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->orderBy('payment_date', 'DESC')->findAll();
    }

    /**
     * Generate sequential payment number (PAY-YS-YYYY-XXXX)
     */
    public function generatePaymentNumber(): string
    {
        $year = date('Y');
        $prefix = "PAY-YS-{$year}-";
        $last = $this->like('payment_number', $prefix, 'after')->orderBy('id', 'DESC')->first();

        if ($last && preg_match('/(\d+)$/', $last['payment_number'], $matches)) {
            $nextSeq = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $prefix . $nextSeq;
    }
}
