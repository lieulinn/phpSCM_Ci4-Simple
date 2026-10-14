<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_id',
        'username',
        'email',
        'password_hash',
        'full_name',
        'phone',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Find user by username or email
     */
    public function findByLogin(string $login)
    {
        return $this->select('users.*, roles.name as role_name, roles.description as role_description')
                    ->join('roles', 'roles.id = users.role_id', 'left')
                    ->groupStart()
                        ->where('users.username', $login)
                        ->orWhere('users.email', $login)
                    ->groupEnd()
                    ->where('users.status', 'active')
                    ->first();
    }

    /**
     * Verify plain password with stored hash
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
