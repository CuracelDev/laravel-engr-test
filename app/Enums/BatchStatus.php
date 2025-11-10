<?php

namespace App\Enums;

enum BatchStatus: string
{
    case PENDING = 'pending';

    case READY = 'ready';

    case PROCESSING = 'processing';

    case COMPLETED = 'completed';

    case FAILED = 'failed';
}
