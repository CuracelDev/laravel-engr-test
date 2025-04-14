<?php

namespace App\Services;

class ClaimService {

    /**
     * Calculate the subtotal amount for a collection of items
     *
     * @param array $items An array of items containing price and quantity information
     * @return float The calculated subtotal amount
     * @throws InvalidArgumentException If items array is empty or invalid
     */
    public static function calculateSubTotal($items)
    {
        $subTotal = 0;

        foreach ($items as $item) {
            $subTotal += $item['quantity'] * $item['unit_price'];
        }

        return $subTotal;
    }
}
