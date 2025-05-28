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

// User
$routes->resource('user', ['controller' => 'UserController', 'placeholder' => '(:segment)']);
$routes->post('user/login', 'UserController::login');

// Mahasiswa
$routes->resource('mahasiswa', ['controller' => 'MahasiswaController', 'placeholder' => '(:segment)']);

//Dosen

//PengajuanDosen

//PengajuanJudul