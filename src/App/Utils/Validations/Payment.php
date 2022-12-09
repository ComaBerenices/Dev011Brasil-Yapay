<?php

namespace Dev011Brasil\App\Utils\Validations;

use Exception;

class Payment
{

    static $PAYMENT_METHOD_ID = [7, 23, 6, 8, 27, 3, 4, 5, 16, 20, 25];

    /**
     * Validate the CARD NAME field, throws a exception if the value have numeric letters
     * @param String $data Receive the CARD NAME value
     * @return Void
     */
    static function cardName($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Payment 001 - The card name value is invalid, string or number is accepted');

            $cardName = preg_replace('/[^a-Z]/', '', $data);

            if (is_numeric($cardName))  throw new Exception('Payment 002 - The CARD NAME is not valid text, number is not allowed');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the CARD NUMBER field, throws a exception if the value not have numbers
     * @param String $data Receive the CARD NUMBERvalue
     * @return Void
     */
    static function cardNumber($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Payment 002 - The card number value is invalid, string or number is accepted');

            $cardNumber = trim(preg_replace('/[^0-9]/', '', $data) ?? '');

            if (!is_numeric($cardNumber))  throw new Exception('Payment 003 - The CARD NUMBER is not valid number, number is not allowed');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the CARD MONTH field, throws a exception if the value not have  valid mounth
     * @param String $data Receive the CARD MONTH value
     * @return Void
     */
    static function cardExpdateMonth($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Payment 003 - The card month value is invalid, string or number is accepted');

            if ((int)$data < (int)date("m"))  throw new Exception('Payment 004 - The CARD MONTH is not valid mounth, should be then or equal that ' . date('m'));
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the CARD YEAR field, throws a exception if the value not have  year
     * @param String $data Receive the CARD YEAR value
     * @return Void
     */
    static function cardExpdateYear($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Payment 004 - The card year value is invalid, string or number is accepted');

            if ((int)$data < (int)date("Y"))  throw new Exception('Payment 005 - The CARD YEAR is not valid year, should be then or equal that ' . date('Y'));
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the CARD CVV field, throws a exception if don't have field
     * @param String $data Receive the CARD CVV value
     * @return Void
     */
    static function cardCvv($data = "")
    {
        try {
            if ($data === "" || $data <= 0 || !$data) throw new Exception('Payment 006 - The card cvv value is invalid, string or number is accepted');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the PAYMENT METHOD ID field, throws a exception if don't have a valid ID
     * @param String $data Receive the PAYMENT METHOD ID value
     * @return Void
     */
    static function paymentMethod($data = "")
    {
        try {
            if ($data === "" || $data <= 0 || !$data) throw new Exception('Payment 007 - The payment method id value is invalid, string or number is accepted');

            $paymentMethodID = (int)$data;

            if (!array_search($data, self::$PAYMENT_METHOD_ID, true)) throw new Exception('Payment 007 - The payment method is not valid');
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
