<?php

namespace App\Services\Inventory;

use InvalidArgumentException;

class CalculationService
{
    /**
     * Money precision.
     */
    private const SCALE = 2;

    /**
     * Calculate a single purchase/sales line.
     */
    public function calculateLine(
        float $qty,
        float $unitPrice,
        float $discountPercent = 0,
        float $discountAmount = 0
    ): array {

        if ($qty <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        if ($unitPrice < 0) {
            throw new InvalidArgumentException('Unit price cannot be negative.');
        }

        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new InvalidArgumentException('Discount percent must be between 0 and 100.');
        }

        if ($discountAmount < 0) {
            throw new InvalidArgumentException('Discount amount cannot be negative.');
        }

        $gross = $this->money($qty * $unitPrice);

        $percentDiscount = $this->money(
            $gross * ($discountPercent / 100)
        );

        $discount = $this->money(
            $percentDiscount + $discountAmount
        );

        if ($discount > $gross) {
            $discount = $gross;
        }

        $subtotal = $this->money(
            $gross - $discount
        );

        return [

            'qty' => $qty,

            'unit_price' => $this->money($unitPrice),

            'gross' => $gross,

            'discount_percent' => $discountPercent,

            'discount_amount' => $discount,

            'subtotal' => $subtotal,

        ];
    }

    /**
     * Calculate invoice totals.
     */
    public function calculateInvoice(
        array $items,
        float $invoiceDiscountPercent = 0,
        float $invoiceDiscountAmount = 0,
        float $taxPercent = 0,
        float $paidAmount = 0
    ): array {

        if ($invoiceDiscountPercent < 0 || $invoiceDiscountPercent > 100) {
            throw new InvalidArgumentException(
                'Invoice discount percent must be between 0 and 100.'
            );
        }

        if ($invoiceDiscountAmount < 0) {
            throw new InvalidArgumentException(
                'Invoice discount amount cannot be negative.'
            );
        }

        if ($taxPercent < 0) {
            throw new InvalidArgumentException(
                'Tax percent cannot be negative.'
            );
        }

        if ($paidAmount < 0) {
            throw new InvalidArgumentException(
                'Paid amount cannot be negative.'
            );
        }

        $subtotal = 0;

        foreach ($items as $item) {

            $subtotal += (float) ($item['subtotal'] ?? 0);

        }

        $subtotal = $this->money($subtotal);

        $discountFromPercent = $this->money(
            $subtotal * ($invoiceDiscountPercent / 100)
        );

        $invoiceDiscount = $this->money(
            $discountFromPercent + $invoiceDiscountAmount
        );

        if ($invoiceDiscount > $subtotal) {
            $invoiceDiscount = $subtotal;
        }

        $netSubtotal = $this->money(
            $subtotal - $invoiceDiscount
        );

        $taxAmount = $this->money(
            $netSubtotal * ($taxPercent / 100)
        );

        $grandTotal = $this->money(
            $netSubtotal + $taxAmount
        );

        $balance = $this->money(
            max(0, $grandTotal - $paidAmount)
        );

        $change = $this->money(
            max(0, $paidAmount - $grandTotal)
        );

        return [

            'subtotal' => $subtotal,

            'invoice_discount_percent' => $invoiceDiscountPercent,

            'invoice_discount_amount' => $invoiceDiscount,

            'net_subtotal' => $netSubtotal,

            'tax_percent' => $taxPercent,

            'tax_amount' => $taxAmount,

            'grand_total' => $grandTotal,

            'paid_amount' => $this->money($paidAmount),

            'balance' => $balance,

            'change' => $change,

        ];
    }

    /**
     * Round monetary values consistently.
     */
    private function money(float $value): float
    {
        return round($value, self::SCALE);
    }
}