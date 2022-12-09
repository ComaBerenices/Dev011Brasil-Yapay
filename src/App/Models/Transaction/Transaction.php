<?php

namespace Dev011Brasil\App\Models\Transaction;

use Dev011Brasil\App\Utils\Validations\Custumer;
use Dev011Brasil\App\Utils\Validations\Transaction as TransactionValidation;
use Dev011Brasil\App\Utils\Validations\Payment;

use Exception;
use \Unirest\Request\Body;
use \Unirest\Request\Request;
use \Unirest\Response;

class Transaction
{

    protected $token = null;
    protected $finger_print = null;
    protected $url_environment = null;
    protected $httClient = null;

    public function __construct($token, $finger_print, $url_environment)
    {
        $this->token = $token;
        $this->finger_print = $finger_print;
        $this->url_environment = $url_environment . 'transactions/payment';
        $this->httClient = new \Unirest\HttpClient();
    }

    /**
     * Send the body data to Yapay services
     * @param Array $transaction The payment data
     * @return Response The response from Yapay services
     */
    public function setTransaction($transaction = [])
    {
        try {
            $fakePaymentData = [
                "token_account" => "SEU_TOKEN_AQUI",
                "customer" => [
                    "contacts" => [
                        [
                            "type_contact" => "H",
                            "number_contact" => "1133221122"
                        ],
                        [
                            "type_contact" => "M",
                            "number_contact" => "11999999999"
                        ]
                    ],
                    "addresses" => [
                        [
                            "type_address" => "B",
                            "postal_code" => "17000-000",
                            "street" => "Av Esmeralda",
                            "number" => "1001",
                            "completion" => "A",
                            "neighborhood" => "Jd Esmeralda",
                            "city" => "Marilia",
                            "state" => "SP"
                        ]
                    ],
                    "name" => "Stephen Strange",
                    "birth_date" => "21/05/1941",
                    "cpf" => "50235335142",
                    "email" => "stephen.strange@avengers.com"
                ],
                "transaction_product" => [
                    [
                        "description" => "Camiseta Tony Stark",
                        "quantity" => "1",
                        "price_unit" => "130.00",
                        "code" => "1",
                        "sku_code" => "0001",
                        "extra" => "Informação Extra"
                    ]
                ],
                "transaction" => [
                    "available_payment_methods" => "2,3,4,5,6,7,14,15,16,18,19,21,22,23",
                    "customer_ip" => "127.0.0.1",
                    "shipping_type" => "Sedex",
                    "shipping_price" => "12",
                    "price_discount" => "",
                    "url_notification" => "http://www.loja.com.br/notificacao",
                    "free" => "Campo Livre"
                ],
                "transaction_trace" => [
                    "estimated_date" => "02/04/2022"
                ],
                "payment" => [
                    "payment_method_id" => "3",
                    "card_name" => "STEPHEN STRANGE",
                    "card_number" => "4111111111111111",
                    "card_expdate_month" => "01",
                    "card_expdate_year" => "2021",
                    "card_cvv" => "644",
                    "split" => "1"
                ]
            ];

            $this->setRules($fakePaymentData);

            $transaction['finger_print'] = $this->finger_print;
            $transaction['token'] = $this->token;

            // $body = Body::json($transaction);
            $body = Body::json($fakePaymentData);

            $request = new Request($this->url_environment, RequestMethod::POST, [], $body);

            if (!$this->httClient) throw new Exception('Something is wrong, Try Again!');

            $response = $this->httClient->execute($request);

            if (!$response) throw new Exception("Something don't work correctly");

            $bodyResponse = $response->getBody();
            $bodyStatusCodeResponse = $response->getStatusCode();

            return ["code" => $bodyStatusCodeResponse, "data" => $bodyResponse];
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function setRules($data = [])
    {
        try {
            if (array_key_exists('customer', $data)) {
                $this->setRulesCustumer($data['customer']);
            } else {
                throw new Exception('400 - The Custumer data given is full invalid');
            }

            if (array_key_exists('transaction', $data)) {
                $this->setRulesTransaction($data['transaction']);
            } else {
                throw new Exception('400 - The Transaction data given is full invalid');
            }

            if (array_key_exists('payment', $data)) {
                $this->setRulesPayment($data['payment']);
            } else {
                throw new Exception('400 - The Payment data given is full invalid');
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function setRulesCustumer($data = [])
    {
        try {
            if (count($data) <= 0) throw new Exception('400 - Your customer data is empty');

            foreach ($data as $field => $value) {
                switch ($field) {
                    case 'cpf':
                        Custumer::cpf($value);
                        break;
                    case 'cpj':
                        Custumer::cnpj($value);
                        break;
                    case 'email':
                        Custumer::email($value);
                        break;
                    case 'birth_date':
                        Custumer::birthDate($value);
                        break;
                    default:
                }
            }

            if (array_key_exists('contacts', $data) && (is_array($data['contacts']) && count($data['contacts']) > 0)) {
                foreach ($data['contacts'] as $field => $value) {
                    switch ($field) {
                        case 'type_contact':
                            Custumer::contactType($value);
                            break;
                        case 'number_contact':
                            Custumer::contactNumber($value);
                            break;
                        default:
                    }
                }
            }

            if (array_key_exists('addresses', $data) && (is_array($data['addresses']) && count($data['addresses']) > 0)) {
                foreach ($data['addresses'] as $field => $value) {
                    switch ($field) {
                        case 'postal_code':
                            Custumer::postalCode($value);
                            break;
                        case 'street':
                            Custumer::street($value);
                            break;
                        case 'state':
                            Custumer::state($value);
                            break;
                        case 'number':
                            Custumer::number($value);
                            break;
                        default:
                    }
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function setRulesPayment($data = [])
    {
        try {
            if (count($data) <= 0) throw new Exception('400 - Your payment data is empty');

            foreach ($data as $field => $value) {
                switch ($field) {
                    case 'card_cvv':
                        Payment::cardCvv($value);
                        break;
                    case 'card_expdate_month':
                        Payment::cardExpdateMonth($value);
                        break;
                    case 'card_expdate_year':
                        Payment::cardExpdateYear($value);
                        break;
                    case 'card_name':
                        Payment::cardName($value);
                        break;
                    case 'card_number':
                        Payment::cardNumber($value);
                        break;
                    case 'payment_method_id':
                        Payment::paymentMethod($value);
                        break;
                    default:
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function setRulesTransaction($data = [])
    {
        try {
            if (count($data) <= 0) throw new Exception('400 - Your payment data is empty');

            TransactionValidation::finger($this->finger_print);

            foreach ($data as $field => $value) {
                switch ($field) {
                    case 'finger_print':
                        break;
                    default:
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
