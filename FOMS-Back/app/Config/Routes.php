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

    // Admin Only
    $routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
        $routes->resource('user', ['controller' => 'UserController', 'placeholder' => '(:segment)']);
        $routes->resource('dosen', ['controller' => 'DosenController', 'placeholder' => '(:segment)']);
        $routes->resource('mahasiswa', ['controller' => 'MahasiswaController', 'placeholder' => '(:segment)']);
    });

    // Dosen Only
    $routes->group('dosen', ['filter' => 'role:dosen'], function ($routes) {
        $routes->get('pengajuandosen/nidn/(:segment)', 'PengajuanDosenController::ambildariNIDN/$1');
        $routes->get('pengajuanjudul/nidn/(:segment)', 'PengajuanjudulController::ambildariNIDN/$1');
        $routes->patch('pengajuandosen/decision/(:segment)', 'PengajuanDosenController::updateDecision/$1');
        $routes->patch('pengajuanjudul/decision/(:segment)', 'PengajuanJudulController::updateDecision/$1');
    });

    // Mahasiswa Only
    $routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], function ($routes) {
        $routes->get('pengajuandosen/npm/(:segment)', 'PengajuanDosenController::ambildariNPM/$1');
        $routes->get('pengajuanjudul/npm/(:segment)', 'PengajuanjudulController::ambildariNPM/$1');
        $routes->post('pengajuanjudul/insert', 'PengajuanJudulController::insertJudul');
        $routes->resource('pengajuandosen', ['controller' => 'PengajuanDosenController', 'placeholder' => '(:segment)']);
    });
});
