<?php

namespace App\Models;

use CodeIgniter\Model;

class PartnerModel extends Model
{
    protected $table            = 'partners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'type', // customer, supplier, both
        'email',
        'phone',
        'address',
        'tax_id',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get customers only or both
     */
    public function getCustomers()
    {
        return $this->whereIn('type', ['customer', 'both'])->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get suppliers only or both
     */
    public function getSuppliers()
    {
        return $this->whereIn('type', ['supplier', 'both'])->orderBy('name', 'ASC')->findAll();
    }
}
