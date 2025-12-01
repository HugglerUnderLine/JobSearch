<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LoggedUsersModel;

class LoggedUsers extends BaseController
{
    #|********************************|
    #|* Render View Logged Users     *|
    #|********************************|
    public function index()
    {
        return view('logged_users');
    }


    #|********************************|
    #|* Fetch Logged Users (AJAX)    *|
    #|********************************|
    public function fetch()
    {
        try {
            $model = new LoggedUsersModel();
            $users = $model->getLoggedUsers();

            return $this->response->setJSON([
                'success' => true,
                'users'   => $users
            ]);

        } catch (\Throwable $e) {
            log_message('critical', "[Fetch Logged Users] Error: " . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => "Failed to fetch logged users."
            ]);
        }
    }
}
