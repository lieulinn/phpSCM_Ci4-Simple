<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root redirect
$routes->get('/', static function () {
    return redirect()->to(site_url('dashboard'));
});

// Authentication Routes (Public)
$routes->get('login', 'AuthController::login');
$routes->post('authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');
$routes->post('logout', 'AuthController::logout');

// Protected Routes (Protected by AuthFilter)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Invoices
    $routes->group('invoices', static function ($routes) {
        $routes->get('/', 'InvoiceController::index');
        $routes->get('create', 'InvoiceController::create');
        $routes->post('store', 'InvoiceController::store');
        $routes->get('view/(:num)', 'InvoiceController::view/$1');
        $routes->get('pdf/(:num)', 'InvoiceController::pdf/$1');
        $routes->post('payment/(:num)', 'InvoiceController::payment/$1');
    });

    // Inventory & Warehousing
    $routes->group('inventory', static function ($routes) {
        $routes->get('/', 'InventoryController::index');
        $routes->post('store', 'InventoryController::store');
        $routes->post('movement', 'InventoryController::movement');
    });

    // Partners (Customers & Suppliers)
    $routes->group('partners', static function ($routes) {
        $routes->get('/', 'PartnerController::index');
        $routes->post('store', 'PartnerController::store');
    });

    // Procurement (Purchase Orders & Goods Receipt)
    $routes->group('procurement', static function ($routes) {
        $routes->get('/', 'ProcurementController::index');
        $routes->post('po/store', 'ProcurementController::storePO');
        $routes->post('gr/store', 'ProcurementController::storeGR');
    });
});
