<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the trait
     */
    public static function bootAuditable()
    {
        // Log when a model is created
        static::created(function ($model) {
            $model->auditCreated();
        });

        // Log when a model is updated
        static::updated(function ($model) {
            $model->auditUpdated();
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            $model->auditDeleted();
        });
    }

    /**
     * Log model creation
     */
    protected function auditCreated()
    {
        AuditLog::logAction(
            AuditLog::ACTION_CREATE,
            $this->getAuditModule(),
            $this->getAuditDescription('created'),
            get_class($this),
            $this->getKey(),
            $this->getAuditIdentifier(),
            null,
            $this->getAuditableAttributes(),
            AuditLog::SEVERITY_INFO
        );
    }

    /**
     * Log model update
     */
    protected function auditUpdated()
    {
        $changes = $this->getChanges();
        
        if (empty($changes)) {
            return;
        }

        $original = collect($this->getOriginal())
            ->only(array_keys($changes))
            ->toArray();

        AuditLog::logAction(
            AuditLog::ACTION_UPDATE,
            $this->getAuditModule(),
            $this->getAuditDescription('updated'),
            get_class($this),
            $this->getKey(),
            $this->getAuditIdentifier(),
            $original,
            $changes,
            $this->getUpdateSeverity($changes)
        );
    }

    /**
     * Log model deletion
     */
    protected function auditDeleted()
    {
        AuditLog::logAction(
            AuditLog::ACTION_DELETE,
            $this->getAuditModule(),
            $this->getAuditDescription('deleted'),
            get_class($this),
            $this->getKey(),
            $this->getAuditIdentifier(),
            $this->getAuditableAttributes(),
            null,
            AuditLog::SEVERITY_WARNING
        );
    }

    /**
     * Get the module name for auditing
     */
    protected function getAuditModule(): string
    {
        // Override this method in your model to specify the module
        return strtolower(class_basename($this));
    }

    /**
     * Get human-readable identifier for the record
     */
    protected function getAuditIdentifier(): ?string
    {
        // Try common identifier fields
        foreach (['name', 'title', 'order_number', 'invoice_number', 'product_name', 'customer_name'] as $field) {
            if (isset($this->$field)) {
                return $this->$field;
            }
        }

        return "ID: {$this->getKey()}";
    }

    /**
     * Get audit description
     */
    protected function getAuditDescription(string $action): string
    {
        $identifier = $this->getAuditIdentifier();
        $modelName = class_basename($this);
        
        return ucfirst($action) . " {$modelName}: {$identifier}";
    }

    /**
     * Get auditable attributes (excluding timestamps and sensitive data)
     */
    protected function getAuditableAttributes(): array
    {
        $exclude = ['password', 'remember_token', 'created_at', 'updated_at'];
        
        return collect($this->getAttributes())
            ->except($exclude)
            ->toArray();
    }

    /**
     * Determine severity based on changes
     */
    protected function getUpdateSeverity(array $changes): string
    {
        $criticalFields = ['status', 'total_amount', 'quantity', 'price'];
        
        foreach ($criticalFields as $field) {
            if (array_key_exists($field, $changes)) {
                return AuditLog::SEVERITY_WARNING;
            }
        }

        return AuditLog::SEVERITY_INFO;
    }
}
