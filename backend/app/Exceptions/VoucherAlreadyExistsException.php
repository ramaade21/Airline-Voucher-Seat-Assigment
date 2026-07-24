<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when an attempt is made to generate voucher seats for a
 * flight number + flight date combination that already has vouchers.
 */
class VoucherAlreadyExistsException extends Exception
{
    public function __construct(string $flightNumber, string $flightDate)
    {
        parent::__construct(
            "Vouchers have already been generated for flight {$flightNumber} on {$flightDate}."
        );
    }
}
