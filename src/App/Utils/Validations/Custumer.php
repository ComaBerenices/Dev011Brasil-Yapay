<?php

namespace Dev011Brasil\App\Utils\Validations;

use Exception;

class Custumer
{
    public function __construct()
    {
        // do anything
    }

    /**
     * Validate the CPF field, throws a exception if the value contains letters
     * @param String $data Receive the CPF value
     * @return Void
     */
    static function cpf($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Custumer 001 - The CPF value is invalid, string or number is accepted');

            $cpfRaw = trim($data);
            $cpfCleared = preg_replace('/[^0-9]/', '', $cpfRaw);

            if (!is_numeric($cpfCleared))  throw new Exception('Custumer 002 - The CPF is not valid number');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the CNPJ field, throws a exception if the value contains letters
     * @param String $data Receive the CNPJ value
     * @return Void
     */
    static function cnpj($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Custumer 003 - The CNPJ value is invalid, string or number is accepted');

            $cnpjRaw = trim($data);
            $cnpjCleared = preg_replace('/[^0-9]/', '', $cnpjRaw);

            if (!is_numeric($cnpjCleared))  throw new Exception('Custumer 004 - The CNPJ is not valid number');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the EMAIL field, throws a exception if the value is not valid e-mail
     * @param String $data Receive the EMAIL value
     * @return Void
     */
    static function email($data = "")
    {
        try {
            if ($data === "") throw new Exception('Custumer 004 - The EMAIL value is invalid, string or number is accepted');

            if (!filter_var($data, FILTER_VALIDATE_EMAIL))  throw new Exception('Custumer 005 - The EMAIL is not valid');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the EMAIL field, throws a exception if the value is not valid e-mail
     * @param String $data Receive the EMAIL value
     * @return Void
     */
    static function birthDate($data = "")
    {
        try {
            $reg = '~(0[1-9]|[12][0-9]|3[01])[-/](0[1-9]|1[012])[-/](19|20)\d\d~';

            if ($data === "" || $data <= 0) throw new Exception('Custumer 005 - The birth date value is invalid, string or number is accepted');

            if (!preg_match($reg, $data))  throw new Exception('Custumer 006 - The BIRTH DATE is not valid');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the TYPE field, throws a exception if the value is not valid type
     * @param String $data Receive the TYPE value
     * @return Void
     */
    static function contactType($data = "")
    {
        try {
            if ($data === "") throw new Exception('Custumer 006 - The type_contact value is invalid, string or number is accepted');

            if ($data !== 'H' && $data !== 'M' && $data !== 'W')  throw new Exception('Custumer 007 - The type_contact is not valid');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the NUMBER field, throws a exception if the value is not valid number phone
     * @param String $data Receive the NUMBER value
     * @return Void
     */
    static function contactNumber($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Custumer 007 - The birth date value is invalid, string or number is accepted');

            $numberPhone = preg_replace('/[^0-9]/', '', $data);

            if (strlen($numberPhone) < 8 || strlen($numberPhone) > 15)  throw new Exception('Custumer 008 - The NUMBER PHONE is not valid number, min 8 and max 15 characters');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the POSTAL CODE field, throws a exception if the value is not valid text
     * @param String $data Receive the POSTAL CODE value
     * @return Void
     */
    static function postalCode($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Custumer 009 - The postal code value is invalid, string or number is accepted');

            $postalCode = preg_replace('/[^0-9]/', '', $data);

            if (strlen($postalCode) > 8)  throw new Exception('Custumer 010 - The POSTAL CODE is not valid code, max 10 characters');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the STREET field, throws a exception if the value is not valid street value
     * @param String $data Receive the STREET value
     * @return Void
     */
    static function street($data = "")
    {
        try {
            if ($data === "") throw new Exception('Custumer 010 - The street value is invalid, string or number is accepted');

            $streetData = preg_replace('/[^a-z][^A-Z]/', '', $data);

            if (is_numeric($streetData))  throw new Exception('Custumer 011 - The STREET is not valid text, number is not allowed');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the STATE field, throws a exception if the value not have one or two character
     * @param String $data Receive the STATE value
     * @return Void
     */
    static function state($data = "")
    {
        try {
            if ($data === "") throw new Exception('Custumer 012 - The state value is invalid, string or number is accepted');

            if (strlen($data) > 2)  throw new Exception('Custumer 013 - The STATE is not valid number, min 1 and max 2 characters');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Validate the NUMBER field, throws a exception if the value not is a valid number
     * @param String $data Receive the NUMBER value
     * @return Void
     */
    static function number($data = "")
    {
        try {
            if ($data === "" || $data <= 0) throw new Exception('Custumer 013 - The number value is invalid, string or number is accepted');

            if (!is_numeric($data))  throw new Exception('Custumer 014 - The NUMBER is not valid number');
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
