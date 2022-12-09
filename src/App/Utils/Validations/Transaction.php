<?php

namespace Dev011Brasil\App\Utils\Validations;

use Exception;

class Transaction
{
    /**
     * Validate the FINGER PRINT field, throws a exception if the value not have 
     * @param String $data Receive the FINGER PRINT value
     * @return Void
     */
    static function finger($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Transaction 001 - The finger_print value is invalid, string or number is accepted');
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
