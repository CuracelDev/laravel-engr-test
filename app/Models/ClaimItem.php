<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        // Automatically calculate 'sub_total' before saving
        static::saving(function ($transaction) {
            $transaction->sub_total = $transaction->unit_price * $transaction->quantity;
        });
    }

    public static function save_claim_items($payloads, $id)
    {
        if (isset($payloads['claim_items']) && is_array($payloads['claim_items'])) {
            foreach ($payloads['claim_items'] as $key => $value) {
                self::create([
                    'item_name' => $value['item_name'],
                    'unit_price' => $value['unit_price'],
                    'quantity' => $value['quantity'],
                    'claim_id' => $id,
                ]);
            }
            return true;
        } else {
            return false;
        }
    }
}