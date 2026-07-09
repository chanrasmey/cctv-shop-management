<?php

namespace App\Services\Shared;

use Illuminate\Database\Eloquent\Model;

class NumberGeneratorService
{
    /**
     * Generate document number.
     *
     * Example:
     * PO-20260707-000001
     */
    public static function generate(
        string $prefix,
        string $modelClass,
        string $column
    ): string {

        $today = now()->format('Ymd');

        $prefix = strtoupper($prefix) . '-' . $today . '-';

        $last = $modelClass::query()
            ->whereDate('created_at', today())
            ->latest('id')
            ->first();

        if (!$last) {

            return $prefix . '000001';

        }

        $running = (int) substr($last->{$column}, -6);

        return $prefix .
            str_pad(
                $running + 1,
                6,
                '0',
                STR_PAD_LEFT
            );
    }
}