<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case PENDING = 'pending';

    case BATCHED = 'batched';

    case PROCESSING = 'processing';

    case PROCESSED = 'processed';

    case REJECTED = 'rejected';
}
