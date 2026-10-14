<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    /**
     * Session key for logged in user state
     */
    public string $sessionLoggedInKey = 'isLoggedIn';

    /**
     * Session key for user data
     */
    public string $sessionUserKey = 'userData';

    /**
     * Redirect route after successful login
     */
    public string $loginRedirect = 'dashboard';

    /**
     * Redirect route after logout or when unauthorized
     */
    public string $logoutRedirect = 'login';

    /**
     * Allow registration of new users
     */
    public bool $allowRegistration = false;

    /**
     * Default role for new users
     */
    public string $defaultRole = 'Staff';

    /**
     * Password hash algorithm
     */
    public string $hashAlgorithm = PASSWORD_BCRYPT;

    /**
     * Password hash cost
     */
    public int $hashCost = 10;

    /**
     * Role definitions and descriptions
     */
    public array $roles = [
        'Superadmin' => [
            'name' => 'Superadmin',
            'description' => 'Akses penuh ke seluruh sistem SCM dan Invoice',
            'permissions' => ['*'],
        ],
        'Finance' => [
            'name' => 'Finance',
            'description' => 'Akses pengelolaan invoice, pembayaran, dan laporan keuangan',
            'permissions' => ['invoice.*', 'payment.*', 'report.finance'],
        ],
        'Procurement' => [
            'name' => 'Procurement',
            'description' => 'Akses pengelolaan pengadaan (PO), vendor, dan penerimaan barang',
            'permissions' => ['procurement.*', 'partner.supplier.*', 'goods_receipt.*'],
        ],
        'Warehouse' => [
            'name' => 'Warehouse',
            'description' => 'Akses persediaan barang, stok, dan mutasi gudang',
            'permissions' => ['inventory.*', 'stock_movement.*'],
        ],
        'Sales' => [
            'name' => 'Sales',
            'description' => 'Akses pembuatan penawaran, penjualan, dan invoice pelanggan',
            'permissions' => ['invoice.create', 'partner.customer.*'],
        ],
    ];
}
