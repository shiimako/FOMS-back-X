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

// Login User
$routes->post('user/login', 'UserController::login');

$routes->group('', ['filter' => 'jwt'], function ($routes) {
    // CRUD User
    $routes->resource('user', ['controller' => 'UserController', 'placeholder' => '(:segment)']);


    // Mahasiswa
    $routes->resource('mahasiswa', ['controller' => 'MahasiswaController', 'placeholder' => '(:segment)']);

    //Dosen
    $routes->resource('dosen', ['controller' => 'DosenController', 'placeholder' => '(:segment)']);

    //PengajuanDosen
    $routes->get('pengajuandosen/nidn/(:segment)', 'PengajuanDosenController::ambildariNIDN/$1');
    $routes->get('pengajuandosen/npm/(:segment)', 'PengajuanDosenController::ambildariNPM/$1');

    $routes->patch('pengajuandosen/decision/(:segment)', 'PengajuanDosenController::updateDecision/$1');

    $routes->resource('pengajuandosen', ['controller' => 'PengajuanDosenController', 'placeholder' => '(:segment)']);



    //PengajuanJudul
    $routes->get('pengajuanjudul/nidn/(:segment)', 'PengajuanjudulController::ambildariNIDN/$1');
    $routes->get('pengajuanjudul/npm/(:segment)', 'PengajuanjudulController::ambildariNPM/$1');

    $routes->post('pengajuanjudul/insert', 'PengajuanJudulController::insertJudul');
    $routes->patch('pengajuanjudul/decision/(:segment)', 'PengajuanJudulController::updateDecision/$1');

    $routes->resource('pengajuanjudul', ['controller' => 'PengajuanJudulController', 'placeholder' => '(:segment)']);
});