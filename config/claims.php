<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Claims Batch Optimizer Interval
    |--------------------------------------------------------------------------
    |
    | This value determines how frequently (in minutes) the batch optimizer
    | command should run. The optimizer re-evaluates all pending and batched
    | claims to minimize processing costs.
    |
    | Default: 5 minutes
    |
    */

    'optimizer_interval' => env('RUN_OPTIMIZER_EVERY_X_MINS', 5),

    /*
    |--------------------------------------------------------------------------
    | Batch Optimization Window
    |--------------------------------------------------------------------------
    |
    | The number of days ahead the optimizer should consider when assigning
    | claims to batches. This balances cost optimization with timely processing.
    |
    | Default: 4 days
    |
    */

    'optimization_window_days' => env('OPTIMIZATION_WINDOW_DAYS', 4),

];

