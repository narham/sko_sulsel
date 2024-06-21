<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        //  
        $data = [
            'title' => 'Dashboard',
        ];
        return view('user/dashboard', $data);
    }
}
