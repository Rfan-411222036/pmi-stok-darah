<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login/process', 'Auth::processLogin');
$routes->get('/logout', 'Auth::logout');

// Protected routes - require authentication
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/dashboard/laporan/download', 'Dashboard::downloadLaporan');
    $routes->get('/dashboard/laporan/distribusi', 'Dashboard::downloadDistribusi');
    $routes->get('/dashboard/laporan/pemusnahan', 'Dashboard::downloadPemusnahan');
    $routes->get('/dashboard/laporan/retur', 'Dashboard::downloadRetur');
    $routes->post('/dashboard/check-low-stock', 'Dashboard::checkLowStock');

    // User Management
    $routes->get('/users', 'Users::index');
    $routes->get('/users/create', 'Users::create');
    $routes->post('/users/store', 'Users::store');
    $routes->get('/users/edit/(:num)', 'Users::edit/$1');
    $routes->post('/users/update/(:num)', 'Users::update/$1');
    $routes->get('/users/delete/(:num)', 'Users::delete/$1');

    // Master Data - Produsen
    $routes->get('/produsen', 'Produsen::index');
    $routes->get('/produsen/create', 'Produsen::create');
    $routes->post('/produsen/store', 'Produsen::store');
    $routes->get('/produsen/edit/(:num)', 'Produsen::edit/$1');
    $routes->post('/produsen/update/(:num)', 'Produsen::update/$1');
    $routes->get('/produsen/delete/(:num)', 'Produsen::delete/$1');

    // Master Data - Rumah Sakit
    $routes->get('/rumahsakit', 'RumahSakit::index');
    $routes->get('/rumahsakit/create', 'RumahSakit::create');
    $routes->post('/rumahsakit/store', 'RumahSakit::store');
    $routes->get('/rumahsakit/edit/(:num)', 'RumahSakit::edit/$1');
    $routes->post('/rumahsakit/update/(:num)', 'RumahSakit::update/$1');
    $routes->get('/rumahsakit/delete/(:num)', 'RumahSakit::delete/$1');

    $routes->get('/stok', 'Stok::index');
    $routes->get('/stok/download', 'Stok::downloadReport');
    $routes->get('/stok/create', 'Stok::create');
    $routes->post('/stok/store', 'Stok::store');
    $routes->get('/stok/edit/(:num)', 'Stok::edit/$1');
    $routes->post('/stok/update/(:num)', 'Stok::update/$1');
    $routes->get('/stok/delete/(:num)', 'Stok::delete/$1');

    $routes->get('/distribusi', 'Distribusi::index');
    $routes->get('/distribusi/create', 'Distribusi::create');
    $routes->post('/distribusi/store', 'Distribusi::store');
    $routes->get('/distribusi/edit/(:num)', 'Distribusi::edit/$1');
    $routes->post('/distribusi/update/(:num)', 'Distribusi::update/$1');
    $routes->get('/distribusi/delete/(:num)', 'Distribusi::delete/$1');

    // Permintaan (Requests)
    $routes->get('/permintaan', 'Permintaan::index');
    $routes->get('/permintaan/download', 'Permintaan::downloadReport');
    $routes->get('/permintaan/create', 'Permintaan::create');
    $routes->post('/permintaan/store', 'Permintaan::store');
    $routes->get('/permintaan/approve/(:num)', 'Permintaan::approve/$1');
    $routes->post('/permintaan/approve/(:num)', 'Permintaan::approve/$1');
    $routes->get('/permintaan/reject/(:num)', 'Permintaan::reject/$1');

    // Notifications
    $routes->get('/notifications', 'Notifications::index');
    $routes->get('/notifications/mark-read/(:num)', 'Notifications::markRead/$1');

    $routes->get('/pemusnahan', 'Pemusnahan::index');
    $routes->get('/pemusnahan/create', 'Pemusnahan::create');
    $routes->post('/pemusnahan/store', 'Pemusnahan::store');
    $routes->get('/pemusnahan/edit/(:num)', 'Pemusnahan::edit/$1');
    $routes->post('/pemusnahan/update/(:num)', 'Pemusnahan::update/$1');
    $routes->get('/pemusnahan/delete/(:num)', 'Pemusnahan::delete/$1');

    $routes->get('/return', 'Retur::index');
    $routes->get('/return/download', 'Retur::downloadReport');
    $routes->get('/return/edit/(:num)', 'Retur::edit/$1');
    $routes->post('/return/update/(:num)', 'Retur::update/$1');
    $routes->get('/return/create', 'Retur::create');
    $routes->post('/return/store', 'Retur::store');
    $routes->get('/return/delete/(:num)', 'Retur::delete/$1');
    $routes->get('/return/getDistribusiInfo/(:num)', 'Retur::getDistribusiInfo/$1');
});

// Catch all - 404
$routes->set404Override(function () {
    return view('errors/html/error_404');
});