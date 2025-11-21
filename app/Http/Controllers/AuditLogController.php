<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('record_identifier', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(20)->withQueryString();

        // Get filter options
        $modules = AuditLog::select('module')->distinct()->pluck('module');
        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $users = User::select('id', 'name')->get();

        // Get statistics
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'critical' => AuditLog::where('severity', AuditLog::SEVERITY_CRITICAL)->count(),
        ];

        // Check if AJAX request for modal
        if ($request->ajax()) {
            return view('audit-logs.index-modal', compact('auditLogs', 'modules', 'actions', 'users', 'stats'));
        }

        return view('audit-logs.index', compact('auditLogs', 'modules', 'actions', 'users', 'stats'));
    }

    /**
     * Display the specified audit log
     */
    public function show(Request $request, AuditLog $auditLog)
    {
        $auditLog->load('user');

        // Check if AJAX request for modal
        if ($request->ajax()) {
            return view('audit-logs.show-modal', compact('auditLog'));
        }

        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Get module-specific logs
     */
    public function moduleLog(Request $request, string $module)
    {
        $query = AuditLog::with('user')
            ->where('module', $module)
            ->orderBy('created_at', 'desc');

        if ($request->filled('record_id')) {
            $query->where('record_id', $request->record_id);
        }

        $auditLogs = $query->paginate(20);

        if ($request->ajax()) {
            return view('audit-logs.module-logs-modal', compact('auditLogs', 'module'));
        }

        return view('audit-logs.module-logs', compact('auditLogs', 'module'));
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AuditLog::whereMonth('created_at', now()->month)->count(),
            'critical_count' => AuditLog::where('severity', AuditLog::SEVERITY_CRITICAL)->count(),
            
            'by_module' => AuditLog::select('module', DB::raw('count(*) as count'))
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->get(),
            
            'by_action' => AuditLog::select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            
            'top_users' => AuditLog::select('user_id', 'user_name', DB::raw('count(*) as count'))
                ->whereNotNull('user_id')
                ->groupBy('user_id', 'user_name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            
            'recent_critical' => AuditLog::with('user')
                ->where('severity', AuditLog::SEVERITY_CRITICAL)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        // Log the export action
        AuditLog::logAction(
            AuditLog::ACTION_EXPORT,
            AuditLog::MODULE_REPORTS,
            'Exported audit logs',
            null,
            null,
            null,
            null,
            ['filters' => $request->all()],
            AuditLog::SEVERITY_INFO
        );

        // Implementation for export (CSV, Excel, etc.)
        // This is a placeholder - you can implement actual export logic
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
