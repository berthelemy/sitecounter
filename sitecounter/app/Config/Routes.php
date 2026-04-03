<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

// Installer routes
$routes->get('/install', 'Install::index');
$routes->post('/install/run', 'Install::run');

// Dashboard routes
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/profile', 'Dashboard::profile');
$routes->post('/dashboard/profile', 'Dashboard::updateProfile');

// Website routes
$routes->get('/dashboard/websites', 'Website::index');
$routes->get('/dashboard/websites/create', 'Website::create');
$routes->post('/dashboard/websites/store', 'Website::store');
$routes->get('/dashboard/websites/(:num)', 'Website::show/$1');
$routes->get('/dashboard/websites/(:num)/edit', 'Website::edit/$1');
$routes->post('/dashboard/websites/(:num)/update', 'Website::update/$1');
$routes->post('/dashboard/websites/(:num)/delete', 'Website::delete/$1');

service('auth')->routes($routes);
