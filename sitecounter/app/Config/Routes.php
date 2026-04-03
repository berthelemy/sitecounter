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
$routes->get('/lang/(:alpha)', 'Home::setLanguage/$1');

// Installer routes
$routes->get('/install', 'Install::index');
$routes->post('/install/run', 'Install::run');

// Dashboard routes
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/profile', 'Dashboard::profile');
$routes->post('/dashboard/profile', 'Dashboard::updateProfile');
$routes->post('/dashboard/profile/password', 'Dashboard::changePassword');

// Website routes
$routes->get('/dashboard/websites', 'Website::index');
$routes->get('/dashboard/websites/create', 'Website::create');
$routes->post('/dashboard/websites/store', 'Website::store');
$routes->get('/dashboard/websites/(:num)', 'Website::show/$1');
$routes->get('/dashboard/websites/(:num)/edit', 'Website::edit/$1');
$routes->post('/dashboard/websites/(:num)/update', 'Website::update/$1');
$routes->post('/dashboard/websites/(:num)/delete', 'Website::delete/$1');

// Report routes
$routes->get('/dashboard/websites/(:num)/report', 'Website::report/$1');

// Tracking endpoint
$routes->post('/track', 'Track::index');
$routes->options('/track', 'Track::options');

service('auth')->routes($routes);
