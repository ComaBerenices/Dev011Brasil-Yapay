<?php

namespace Dev011Brasil;

use Dev011Brasil\App\Models\Transaction\Transaction;
use Exception;

class Yapay
{
    protected $token = null;
    protected $finger_print = null;
    protected $url_environment = null;
    public $production = false;

    public function __construct($token, $finger_print, $production)
    {
        $this->token = $token;
        $this->finger_print = $finger_print;
        $this->production = $production;

        $this->setURLEnvironment();
    }

    public function __set($name, $value)
    {
        if (!$this->$name) throw new Exception("The property {$name} don't exists");
        $this->$name = $value;
    }

    public function __get($name)
    {
        if (!$this->$name) throw new Exception("The property {$name} don't exists");

        return $this->$name;
    }

    private function setURLEnvironment()
    {
        try {
            if ($this->production) {
                $this->url_environment = 'https://api.intermediador.yapay.com.br/api/v3/';
            } else {
                $this->url_environment = 'https://api.intermediador.sandbox.yapay.com.br/api/v3/';
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function transaction($body = [])
    {
        try {
            if (!$this->finger_print) throw  new Exception('403 - This request is not Authorized, your fingir print is missing');
            if (!$this->token) throw  new Exception('403 - This request is not Authorized, your token is missing');

            $transaction = new Transaction($this->token, $this->finger_print, $this->url_environment);

            return $transaction->setTransaction($body);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
