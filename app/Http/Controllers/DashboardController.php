<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard.
     */
    public function index()
    {
        return view('dashboard');
    }
}
