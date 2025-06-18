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
$routes->post('/login', 'UserController::login'); // login
$routes->post('/refresh', 'UserController::refresh'); // refresh token
$routes->delete('/logout', 'UserController::logout'); // logout

// Group dengan JWT (umum)
$routes->group('', ['filter' => 'jwt'], function ($routes) {

    $routes->get('user/profile', 'UserController::userprofile'); // R user
    $routes->patch('user/profile', 'UserController::updateProfile'); // U profile user


    // Admin Only
    $routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'UserController::welcome'); // Welcome admin
        $routes->resource('user', ['controller' => 'UserController', 'placeholder' => '(:segment)']); // CRUD user
        $routes->resource('dosen', ['controller' => 'DosenController', 'placeholder' => '(:segment)']); // CRUD dosen
        $routes->resource('mahasiswa', ['controller' => 'MahasiswaController', 'placeholder' => '(:segment)']); // CRUD mahasiswa
        $routes->get('pengajuandosen', 'PengajuanDosenController'); // R semua pengajuan dosen
        $routes->get('pengajuandosen/(:segment)', 'PengajuanDosenController::show/$1'); // R pengajuan dosen berdasarkan id
        $routes->get('pengajuanjudul', 'PengajuanJudulController'); // R semua pengajuan judul
        $routes->get('pengajuanjudul/(:segment)', 'PengajuanJudulController::show/$1'); // R pengajuan judul berdasarkan id
    });

    // Dosen Only
    $routes->group('dosen', ['filter' => 'role:dosen'], function ($routes) {
        $routes->get('/', 'UserController::welcome'); // Welcome dosen
        $routes->get('pengajuandosen', 'PengajuanDosenController::ambilPengajuanUser'); // R semua pengajuan dosen
        $routes->get('bimbingan', 'PengajuanDosenController::ambilBimbingan'); // R semua bimbingan dosen
        $routes->get('pengajuanjudul', 'PengajuanJudulController::ambilPengajuanUser'); // R semua pengajuan judul
        $routes->get('pengajuanjudul/(:segment)', 'PengajuanJudulController::show/$1'); // R pengajuan judul berdasarkan id
        $routes->get('pengajuandosen/(:segment)', 'PengajuanDosenController::show/$1'); // R pengajuan dosen berdasarkan id
        $routes->get('pengajuandosen/cari/(:segment)', 'PengajuanDosenController::ambilbyNama/$1'); // R pengajuan dosen berdasarkan nama
        $routes->patch('pengajuandosen/(:segment)', 'PengajuanDosenController::updateDecision/$1'); // U pengajuan dosen berdasarkan id
        $routes->patch('pengajuanjudul/(:segment)', 'PengajuanJudulController::updateDecision/$1'); // U pengajuan judul berdasarkan id
    });

    // Mahasiswa Only
    $routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], function ($routes) {
        $routes->get('/', 'UserController::welcome'); // Welcome mahasiswa
        $routes->get('pengajuandosen', 'PengajuanDosenController::ambilPengajuanUser'); // R semua pengajuan dosen
        $routes->get('pengajuanjudul', 'PengajuanJudulController::ambilPengajuanUser'); // R semua pengajuan judul
        $routes->get('pembimbing', 'PengajuanDosenController::ambilPembimbing'); // R semua pembimbing mahasiswa
        $routes->get('pengajuanjudul/(:segment)', 'PengajuanJudulController::show/$1'); // R pengajuan judul berdasarkan id
        $routes->get('pengajuandosen/(:segment)', 'PengajuanDosenController::show/$1'); // R pengajuan dosen berdasarkan id
        $routes->get('pengajuandosen/cari/(:segment)', 'PengajuanDosenController::ambilbyNama/$1'); // R pengajuan dosen berdasarkan nama
        $routes->post('pengajuanjudul', 'PengajuanJudulController::insertJudul'); // C pengajuan judul
        $routes->post('pengajuandosen', 'PengajuanDosenController::create'); // C pengajuan dosen
        $routes->delete('pengajuandosen/(:segment)', 'PengajuanDosenController::delete/$1'); // D pengajuan dosen
        $routes->delete('pengajuanjudul/(:segment)', 'PengajuanJudulController::delete/$1'); // D pengajuan judul
        $routes->get('dosen/', 'DosenController::index'); //Read semua dosen
    });
});
