<?php

use CodeIgniter\Controller;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');

// Admin API Group
$routes->group('api/admin', function($routes) {
    // User routes
    $routes->get('/', 'Api\AdminController::index'); // GET semua user
    $routes->get('user/(:num)', 'Api\AdminController::show/$1'); // GET user by ID
    $routes->post('user', 'Api\AdminController::create'); // POST user
    $routes->put('user/(:num)', 'Api\AdminController::update/$1'); // PUT user
    $routes->delete('user/(:num)', 'Api\AdminController::delete/$1'); // DELETE user

    // Mahasiswa routes
    $routes->get('mahasiswa', 'Api\AdminController::indexMahasiswa'); // GET semua mahasiswa
    $routes->get('mahasiswa/(:num)', 'Api\AdminController::showMahasiswa/$1'); // GET mahasiswa by ID
    $routes->post('mahasiswa', 'Api\AdminController::createMahasiswa'); // POST mahasiswa
    $routes->put('mahasiswa/(:num)', 'Api\AdminController::updateMahasiswa/$1'); // PUT mahasiswa
    $routes->delete('mahasiswa/(:num)', 'Api\AdminController::deleteMahasiswa/$1'); // DELETE mahasiswa

    // Dosen routes
    $routes->get('dosen', 'Api\AdminController::indexDosen'); // GET semua dosen
    $routes->get('dosen/(:num)', 'Api\AdminController::showDosen/$1'); // GET dosen by ID
    $routes->post('dosen', 'Api\AdminController::createDosen'); // POST dosen
    $routes->put('dosen/(:num)', 'Api\AdminController::updateDosen/$1'); // PUT dosen
    $routes->delete('dosen/(:num)', 'Api\AdminController::deleteDosen/$1'); // DELETE dosen
});



$routes->resource('pengajuandosen', ['controller' => 'PengajuanDosenController']);
$routes->resource('pengajuanjudul', ['controller' => 'PengajuanJudulController']);


