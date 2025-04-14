<?php

namespace App\Services;

class ClaimService {
    public static function computClaimFigures($claim)
    {
        $claim['sub_total'] = self::calculateSubTotal($claim['items']);
        $claim['items'] = array_map(function ($item) {
            return [
                ...$item,
                'total_price' => $item['quantity'] * $item['unit_price'],
            ];
        }, $claim['items']);

        return $claim;
    }

    /**
     * Calculate the subtotal amount for a collection of items
     *
     * @param array $items An array of items containing price and quantity information
     * @return float The calculated subtotal amount
     * @throws InvalidArgumentException If items array is empty or invalid
     */
    private static function calculateSubTotal($items)
    {
        $subTotal = 0;

        foreach ($items as $item) {
            $subTotal += $item['quantity'] * $item['unit_price'];
        }

        return $subTotal;
    }
}
