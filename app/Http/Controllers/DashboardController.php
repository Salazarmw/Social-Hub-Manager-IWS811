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
        $user = auth()->user();
        if ($user && $user->two_factor_enabled && !session('2fa_passed')) {
            return redirect()->route('2fa.verify');
        }
        return view('dashboard');
    }
}
