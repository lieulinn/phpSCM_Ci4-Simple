<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Show login form
     */
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/login', [
            'title' => 'Masuk ke Sistem - Paper Yana Surya',
        ]);
    }

    /**
     * Process authentication
     */
    public function authenticate()
    {
        $login    = trim((string) $this->request->getPost('login'));
        $password = (string) $this->request->getPost('password');

        if (empty($login) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Username / Email dan Password wajib diisi.');
        }

        $user = $this->userModel->findByLogin($login);

        if (! $user || ! $this->userModel->verifyPassword($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Kredensial tidak valid. Silakan periksa kembali email/username dan password Anda.');
        }

        // Set session
        $this->session->set([
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'username'   => $user['username'],
            'full_name'  => $user['full_name'],
            'email'      => $user['email'],
            'role_id'    => (int) $user['role_id'],
            'role_name'  => $user['role_name'] ?? 'Staff',
        ]);

        $redirectUrl = $this->session->get('redirect_url') ?: site_url('dashboard');
        $this->session->remove('redirect_url');

        return redirect()->to($redirectUrl)->with('success', 'Selamat datang kembali, ' . esc($user['full_name']) . '!');
    }

    /**
     * Logout and destroy session
     */
    public function logout()
    {
        $this->session->destroy();

        return redirect()->to(site_url('login'))->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
