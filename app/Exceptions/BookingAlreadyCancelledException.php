<?php

namespace App\Exceptions;

use RuntimeException;

class BookingAlreadyCancelledException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('This booking has already been cancelled.');
    }
}
