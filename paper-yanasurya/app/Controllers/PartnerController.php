<?php

namespace App\Controllers;

use App\Models\PartnerModel;

class PartnerController extends BaseController
{
    protected PartnerModel $partnerModel;

    public function __construct()
    {
        $this->partnerModel = new PartnerModel();
    }

    /**
     * List all partners (customers & suppliers)
     */
    public function index()
    {
        $type = $this->request->getGet('type') ?: 'all';

        $builder = $this->partnerModel->orderBy('name', 'ASC');
        if ($type !== 'all') {
            $builder->whereIn('type', [$type, 'both']);
        }

        $partners = $builder->findAll();
        $totalCustomers = count($this->partnerModel->getCustomers());
        $totalSuppliers = count($this->partnerModel->getSuppliers());

        return $this->render('partners/index', [
            'title'          => 'Mitra Bisnis - Pelanggan & Pemasok',
            'pageTitle'      => 'Direktori Rekanan & Kontak Bisnis',
            'partners'       => $partners,
            'currentType'    => $type,
            'totalCustomers' => $totalCustomers,
            'totalSuppliers' => $totalSuppliers,
        ]);
    }

    /**
     * Store new partner
     */
    public function store()
    {
        $name = trim((string) $this->request->getPost('name'));
        if (empty($name)) {
            return redirect()->back()->withInput()->with('error', 'Nama mitra bisnis wajib diisi.');
        }

        $this->partnerModel->insert([
            'name'    => $name,
            'type'    => (string) $this->request->getPost('type') ?: 'customer',
            'email'   => (string) $this->request->getPost('email'),
            'phone'   => (string) $this->request->getPost('phone'),
            'address' => (string) $this->request->getPost('address'),
            'tax_id'  => (string) $this->request->getPost('tax_id'),
        ]);

        return redirect()->to(site_url('partners'))->with('success', "Mitra bisnis '{$name}' berhasil didaftarkan!");
    }
}
