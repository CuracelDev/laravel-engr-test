<?php

namespace App\Exception;

use Exception;

class BatchingLimitReachedException extends Exception
{
    public function __construct(string $insurerName, string $date)
    {
        $message = "Insurer '{$insurerName}' has reached their daily capacity for {$date}.";
        parent::__construct($message);
    }
}
