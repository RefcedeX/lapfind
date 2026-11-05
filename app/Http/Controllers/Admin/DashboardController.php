<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\AuditLog;
use App\Models\Criterion;
use App\Models\SpkResult;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display a concise overview of the DSS system.
     */
    public function index(): View
    {
        $stats = [
            'total_laptops' => Alternative::count(),
            'total_criteria' => Criterion::count(),
            'total_users' => User::count(),
            'total_spk_results' => SpkResult::count(),
            'total_audit_logs' => AuditLog::count(),
            'failed_logins_week' => AuditLog::where('action', 'auth.failed')->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentResults = SpkResult::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentLaptops = Alternative::with('values.criterion')
            ->latest()
            ->take(5)
            ->get();

        $criteria = Criterion::orderBy('code')->get();

        $recentAuditLogs = AuditLog::with('user')
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentResults', 'recentLaptops', 'criteria', 'recentAuditLogs'));
    }
}
