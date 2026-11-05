<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs with search, category filtering, and statistics.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->orderByDesc('created_at');

        // Filter Category
        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        // Filter Action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter Search Term
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filter Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->paginate(20)->withQueryString();

        // Statistics for Telemetry
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'failed_logins' => AuditLog::where('action', 'auth.failed')->where('created_at', '>=', now()->subDays(7))->count(),
            'mutations' => AuditLog::whereIn('category', ['catalog', 'criterion', 'user'])->count(),
        ];

        // Categories List with Counts
        $categories = [
            'all' => ['label' => 'Semua Aktivitas', 'count' => $stats['total']],
            'auth' => ['label' => 'Autentikasi (Sesi)', 'count' => AuditLog::where('category', 'auth')->count()],
            'security' => ['label' => 'Peringatan Keamanan', 'count' => AuditLog::where('category', 'security')->count()],
            'catalog' => ['label' => 'Katalog Laptop', 'count' => AuditLog::where('category', 'catalog')->count()],
            'criterion' => ['label' => 'Kriteria Evaluasi', 'count' => AuditLog::where('category', 'criterion')->count()],
            'user' => ['label' => 'Akun Pengguna', 'count' => AuditLog::where('category', 'user')->count()],
            'spk' => ['label' => 'Komputasi SPK', 'count' => AuditLog::where('category', 'spk')->count()],
        ];

        return view('admin.audit_logs.index', compact('logs', 'stats', 'categories'));
    }

    /**
     * Display the specified audit log entry details.
     */
    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('admin.audit_logs.show', compact('auditLog'));
    }

    /**
     * Export filtered audit logs as a downloadable CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::orderByDesc('created_at');

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $filename = 'audit-logs-export-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, ['ID', 'Waktu', 'Pengguna', 'Email', 'Peran', 'Aksi', 'Kategori', 'Deskripsi', 'IP Address', 'Method', 'URL']);

            $query->chunk(200, function ($records) use ($handle) {
                foreach ($records as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->user_name,
                        $log->user_email,
                        $log->user_role,
                        $log->action,
                        $log->category,
                        $log->description,
                        $log->ip_address,
                        $log->method,
                        $log->url,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
