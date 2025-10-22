<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Centralized reference number generator for documents
 * Pattern: {PREFIX}{YYYYMMDD}-{4-digit sequence}
 */
class ReferenceNumberService
{
    /**
     * Generate a unique reference number for a given table/column with prefix.
     *
     * @param string $table Table name to check for uniqueness
     * @param string $column Column name to check for uniqueness
     * @param string $prefix Prefix to prepend (e.g., PO-, GR-)
     * @return string
     */
    public static function generate(string $table, string $column, string $prefix): string
    {
        $date = Carbon::now()->format('Ymd');
        $base = rtrim($prefix, '-') . '-' . $date . '-';

        // Find last sequence for today
        $last = DB::table($table)
            ->where($column, 'like', $base . '%')
            ->orderBy($column, 'desc')
            ->value($column);

        $nextSeq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $lastSeq = intval(end($parts));
            $nextSeq = $lastSeq + 1;
        }

        return $base . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
