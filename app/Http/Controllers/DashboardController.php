<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;

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

        $stats = [
            'scheduled' => ScheduledPost::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'queued' => ScheduledPost::where('user_id', Auth::id())
                ->where('status', 'processing')
                ->count(),
            'accounts' => SocialAccount::where('user_id', Auth::id())->count()
        ];

        return view('dashboard', compact('stats'));
    }
}
