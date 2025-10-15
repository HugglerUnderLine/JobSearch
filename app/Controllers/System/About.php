<?php

namespace App\Controllers\System;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class About extends BaseController
{
    public function index() {
        return view('system/about');
    }
}
