<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Trait for enforcing referential integrity at model level.
 * 
 * This trait validates foreign key relationships before saving,
 * providing the same protection as database constraints but at the application level.
 * 
 * Usage in Models:
 * use ValidatesForeignKeys;
 * 
 * protected array $foreignKeys = [
 *     'product_id' => ['table' => 'products', 'column' => 'product_id'],
 *     'customer_id' => ['table' => 'customers', 'column' => 'customer_id', 'nullable' => true],
 * ];
 */
trait ValidatesForeignKeys
{
    /**
     * Boot the trait.
     */
    protected static function bootValidatesForeignKeys()
    {
        // Validate foreign keys before creating
        static::creating(function (Model $model) {
            $model->validateForeignKeys();
        });

        // Validate foreign keys before updating
        static::updating(function (Model $model) {
            $model->validateForeignKeys();
        });
    }

    /**
     * Validate all foreign key relationships.
     */
    protected function validateForeignKeys(): void
    {
        if (!property_exists($this, 'foreignKeys')) {
            return;
        }

        $rules = [];
        $data = [];

        foreach ($this->foreignKeys as $attribute => $config) {
            $value = $this->getAttribute($attribute);
            
            // Skip if nullable and value is null
            if (($config['nullable'] ?? false) && is_null($value)) {
                continue;
            }

            $table = $config['table'];
            $column = $config['column'] ?? 'id';
            $nullable = $config['nullable'] ?? false;

            $rules[$attribute] = $nullable ? "nullable|exists:{$table},{$column}" : "required|exists:{$table},{$column}";
            $data[$attribute] = $value;
        }

        if (empty($rules)) {
            return;
        }

        $validator = Validator::make($data, $rules, $this->getForeignKeyMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Get custom validation messages for foreign keys.
     */
    protected function getForeignKeyMessages(): array
    {
        return [
            'product_id.exists' => 'The selected product does not exist or has been deleted.',
            'customer_id.exists' => 'The selected customer does not exist or has been deleted.',
            'user_id.exists' => 'The selected user does not exist or has been deleted.',
            'supplier_id.exists' => 'The selected supplier does not exist or has been deleted.',
            'order_id.exists' => 'The selected order does not exist or has been deleted.',
            'sales_order_id.exists' => 'The selected sales order does not exist or has been deleted.',
            'purchase_order_id.exists' => 'The selected purchase order does not exist or has been deleted.',
            'exchange_id.exists' => 'The selected exchange does not exist or has been deleted.',
        ];
    }

    /**
     * Check if a related record exists (for soft cascade deletes).
     */
    protected function hasRelatedRecords(string $relation): bool
    {
        return $this->{$relation}()->exists();
    }

    /**
     * Prevent deletion if related records exist (simulate RESTRICT).
     */
    protected function restrictDelete(array $relations): bool
    {
        foreach ($relations as $relation) {
            if ($this->hasRelatedRecords($relation)) {
                throw new \Exception("Cannot delete this record because it has related {$relation}.");
            }
        }
        return true;
    }

    /**
     * Cascade delete related records (simulate CASCADE DELETE).
     */
    protected function cascadeDelete(array $relations): void
    {
        foreach ($relations as $relation) {
            $this->{$relation}()->delete();
        }
    }

    /**
     * Set related foreign keys to null (simulate SET NULL).
     */
    protected function nullifyRelated(array $relations): void
    {
        foreach ($relations as $relation => $foreignKey) {
            $this->{$relation}()->update([$foreignKey => null]);
        }
    }
}
