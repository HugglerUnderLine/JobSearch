<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Seeders extends Seeder
{
    public function run()
    {
        $this->call('Companies');
        $this->call('Jobs');
        $this->call('Users');
    }
}
