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

// Login & Refresh - Bebas
$routes->post('user/login', 'UserController::login');
$routes->post('user/refresh', 'UserController::refresh');
$routes->delete('user/refresh', 'UserController::logout');

// Group dengan JWT (umum)
$routes->group('', ['filter' => 'jwt'], function ($routes) {

    $routes->get('user/profile', 'UserController::userprofile');
    $routes->put('akun/update', 'UserController::updateProfile');


    // Admin Only
    $routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
        $routes->resource('user', ['controller' => 'UserController', 'placeholder' => '(:segment)']);
        $routes->resource('dosen', ['controller' => 'DosenController', 'placeholder' => '(:segment)']);
        $routes->resource('mahasiswa', ['controller' => 'MahasiswaController', 'placeholder' => '(:segment)']);
        $routes->resource('pengajuandosen', ['controller' => 'PengajuanDosenController', 'placeholder' => '(:segment)']);
        $routes->resource('pengajuanjudul', ['controller' => 'PengajuanJudulController', 'placeholder' => '(:segment)']);
    });

    // Akun (Mahasiswa/Dosen) Only
    $routes->group('akun', ['filter' => 'role:dosen,mahasiswa'], function ($routes){
        $routes->get('pengajuandosen', 'PengajuanDosenController::ambilPengajuanUser');
        $routes->get('pengajuanjudul', 'PengajuanJudulController::ambilPengajuanUser');
        $routes->get('pengajuanjudul/(:segment)','PengajuanJudulController::show/$1');
        $routes->get('pengajuandosen/(:segment)','PengajuanDosenController::show/$1');
    });

    // Dosen Only
    $routes->group('dosen', ['filter' => 'role:dosen'], function ($routes) {
        $routes->patch('pengajuandosen/(:segment)', 'PengajuanDosenController::updateDecision/$1');
        $routes->patch('pengajuanjudul/(:segment)', 'PengajuanJudulController::updateDecision/$1');
    });

    // Mahasiswa Only
    $routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], function ($routes) {
        $routes->post('pengajuanjudul/insert', 'PengajuanJudulController::insertJudul');
        $routes->post('pengajuandosen/insert', 'PengajuanDosenController::create');
        $routes->put('pengajuandosen/(:segment)', 'PengajuanDosenController::update/$1');
        $routes->put('pengajuanjudul/(:segment)', 'PengajuanJudulController::update/$1');
        $routes->delete('pengajuandosen/(:segment)', 'PengajuanDosenController::delete/$1');
        $routes->delete('pengajuanjudul/(:segment)', 'PengajuanJudulController::delete/$1');
    });

});
