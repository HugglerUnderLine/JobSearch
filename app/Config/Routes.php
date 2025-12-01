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
$routes->get('/companies/(:num)', 'Companies\Company::read_company_data/$1'); # Server

#Update user data
$routes->patch('/users/(:num)', 'Users\User::user_edit/$1'); # Server
$routes->patch('/companies/(:num)', 'Companies\Company::company_edit/$1'); # Server

#Delete user account
$routes->delete('/users/(:num)', 'Users\User::user_delete/$1'); # Server
$routes->delete('/companies/(:num)', 'Companies\Company::company_delete/$1'); # Server

#Create job vacancy -> Company
$routes->post('/jobs', 'System\Job::create_job'); # Server
$routes->get('/(company)/jobs', 'System\Job::index/$1'); # Client
$routes->get('/(user)/jobs', 'System\Job::index/$1'); # Client

#Read job details -> Common user reading job details
$routes->get('/jobs/(:num)', 'System\Job::read_job_details/$1'); # Server

#Read Available Jobs -> Common User reading jobs
$routes->post('/jobs/search', 'System\Job::list_available_jobs'); # Server

#Read Jobs By Company ID -> Company
$routes->post('/companies/(:num)/jobs', 'System\Job::list_company_jobs/$1'); # Server

#Edit Job -> Company
$routes->patch('/jobs/(:num)', 'System\Job::edit_job/$1'); # Server

#Delete Job -> Company
$routes->delete('/jobs/(:num)', 'System\Job::delete_job/$1'); # Server

#Apply to Job -> User
$routes->post('/jobs/(:num)', 'System\Job::apply_to_job/$1'); # Server

#Send Feedback to candidate
$routes->post('/jobs/(:num)/feedback', 'System\Job::send_feedback/$1'); # Server

#List User Applications
$routes->get('/users/(:num)/jobs', 'System\Job::list_applications/$1'); # Server

$routes->get('/user/applications', 'Users\User::list_applications/$1'); # Server

#List Candidates
$routes->get('/companies/(:num)/jobs/(:num)', 'System\Job::list_candidates/$1/$2'); # Server

#Error Fallback
$routes->post('/error', 'System\Error::receive_error'); # Server

# Show logged users
$routes->get('/logged-users', 'LoggedUsers::index');
$routes->get('/logged-users/fetch', 'LoggedUsers::fetch');

$routes->group('api', ['filter' => 'cors'], function($routes) {
    $routes->post('/companies', 'Companies\Company::company_registration'); # Server
    $routes->post('/users', 'Users\User::user_registration'); # Server
    $routes->post('/login', 'System\Login::auth_user'); # Server
    $routes->post('/logout', 'System\Login::logout'); # Server
    $routes->patch('/users/(:num)', 'Users\User::user_edit/$1'); # Server
    $routes->patch('/companies/(:num)', 'Companies\Company::company_edit/$1'); # Server
    $routes->delete('/users/(:num)', 'Users\User::user_delete/$1'); # Server
    $routes->delete('/companies/(:num)', 'Companies\Company::company_delete/$1'); # Server
    $routes->post('/jobs', 'System\Job::create_job');
    $routes->get('/jobs/(:num)', 'System\Job::read_job_details/$1');
    $routes->post('/jobs/search', 'System\Job::list_available_jobs');
    $routes->post('/companies/(:num)/jobs', 'Companies\Company::list_company_jobs/$1');
    $routes->patch('/jobs/(:num)', 'System\Job::edit_job/$1');
    $routes->delete('/jobs/(:num)', 'System\Job::delete_job/$1');
    $routes->post('/jobs/(:num)', 'System\Job::apply_to_job/$1');
    $routes->post('/jobs/(:num)/feedback', 'System\Job::get_feedback/$1');
    $routes->get('/users/(:num)/jobs', 'Users\User::list_applications/$1');
    $routes->get('/companies/(:num)/jobs/(:num)', 'Companies\Company::list_candidates/$1');
    $routes->post('/error', 'System\Error::receive_error');
});

#
// $routes->addRedirect('(.+)', '/');
