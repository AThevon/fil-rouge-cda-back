<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELED = 'canceled';
    case REFUNDED = 'refunded';
    case DISPUTED = 'disputed';
    case EXPIRED = 'expired';
}