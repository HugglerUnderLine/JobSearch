<?php

namespace App\Controllers\System;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Index extends BaseController
{
    public $session;

    public function __construct() {
        $this->session = session();
    }

    public function index() {
        return redirect()->to(base_url('login'));
    }

}
