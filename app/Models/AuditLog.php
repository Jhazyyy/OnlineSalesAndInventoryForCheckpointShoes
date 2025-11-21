<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'audit_id';

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'record_type',
        'record_id',
        'record_identifier',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'severity',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Action constants
     */
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_VIEW = 'view';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';
    const ACTION_CANCEL = 'cancel';
    const ACTION_RESTORE = 'restore';

    /**
     * Module constants
     */
    const MODULE_INVENTORY = 'inventory';
    const MODULE_SALES = 'sales';
    const MODULE_PURCHASES = 'purchases';
    const MODULE_USERS = 'users';
    const MODULE_CUSTOMERS = 'customers';
    const MODULE_SUPPLIERS = 'suppliers';
    const MODULE_REPORTS = 'reports';
    const MODULE_SETTINGS = 'settings';
    const MODULE_AUTH = 'authentication';

    /**
     * Severity constants
     */
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Static method to log an action
     */
    public static function logAction(
        string $action,
        string $module,
        string $description,
        ?string $recordType = null,
        ?int $recordId = null,
        ?string $recordIdentifier = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = self::SEVERITY_INFO
    ): self {
        return self::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'System',
            'action' => $action,
            'module' => $module,
            'record_type' => $recordType,
            'record_id' => $recordId,
            'record_identifier' => $recordIdentifier,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'severity' => $severity,
        ]);
    }

    /**
     * Scope to filter by module
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by severity
     */
    public function scopeSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATE => 'green',
            self::ACTION_UPDATE => 'blue',
            self::ACTION_DELETE => 'red',
            self::ACTION_LOGIN => 'indigo',
            self::ACTION_LOGOUT => 'gray',
            self::ACTION_APPROVE => 'green',
            self::ACTION_REJECT => 'red',
            self::ACTION_CANCEL => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get severity badge color
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            self::SEVERITY_INFO => 'blue',
            self::SEVERITY_WARNING => 'yellow',
            self::SEVERITY_CRITICAL => 'red',
            default => 'gray',
        };
    }
}
