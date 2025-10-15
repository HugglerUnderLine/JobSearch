<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'System\Index::index');

#Base
$routes->get('/login', 'System\Login::index'); # Client
$routes->post('/login', 'System\Login::auth_user'); # Server
$routes->post('/logout', 'System\Login::logout'); # Server
$routes->get('/about', 'System\About::index'); # Client

#Registration
$routes->post('/companies', 'Companies\Company::company_registration'); # Server
$routes->post('/users', 'Users\User::user_registration'); # Server

#Profile
$routes->get('/profile', 'Users\User::index'); # Client

#Read user data
$routes->get('/users/(:num)', 'Users\User::read_user_data/$1'); # Server
$routes->get('/company/(:num)', 'Companies\Company::read_company_data/$1'); # Server

#Update user data
$routes->patch('/users/(:num)', 'Users\User::user_edit/$1'); # Server
$routes->patch('/companies/(:num)', 'Companies\Company::company_edit/$1'); # Server

#Delete user account
$routes->delete('/users/(:num)', 'Users\User::user_delete/$1'); # Server
$routes->delete('/companies/(:num)', 'Companies\Company::company_delete/$1'); # Server

$routes->group('api', ['filter' => 'cors'], function($routes) {
    $routes->post('/companies', 'Companies\Company::company_registration'); # Server
    $routes->post('/users', 'Users\User::user_registration'); # Server
    $routes->post('/login', 'System\Login::auth_user'); # Server
    $routes->post('/logout', 'System\Login::logout'); # Server
    $routes->patch('/users/(:num)', 'Users\User::user_edit/$1'); # Server
    $routes->patch('/companies/(:num)', 'Companies\Company::company_edit/$1'); # Server
    $routes->delete('/users/(:num)', 'Users\User::user_delete/$1'); # Server
    $routes->delete('/companies/(:num)', 'Companies\Company::company_delete/$1'); # Server
});

#
// $routes->addRedirect('(.+)', '/');
