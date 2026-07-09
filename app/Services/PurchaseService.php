<?php

namespace App\Services;

use App\Models\Purchase;
use Carbon\Carbon;

class PurchaseService
{
    /**
     * Generate Purchase Number
     *
     * Example:
     * PO-20260707-000001
     */
    public static function generatePurchaseNumber(): string
    {
        $date = Carbon::now()->format('Ymd');

        $prefix = 'PO-' . $date . '-';

        $lastPurchase = Purchase::whereDate(
            'created_at',
            Carbon::today()
        )
        ->latest('id')
        ->first();

        if (!$lastPurchase) {

            return $prefix . '000001';

        }

        $lastNumber = (int) substr($lastPurchase->purchase_no, -6);

        $nextNumber = str_pad(
            $lastNumber + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        return $prefix . $nextNumber;
    }
        /**
     * Delete Draft Purchase.
     *
     * Only purchases with Draft status can be deleted.
     *
     * @throws \InvalidArgumentException
     */
    public function deleteDraft(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {

            $purchase->refresh();

            if ($purchase->status !== 'Draft') {
                throw new InvalidArgumentException(
                    'Only Draft purchases can be deleted.'
                );
            }

            $purchase->details()->delete();

            $purchase->delete();
        });
    }
}