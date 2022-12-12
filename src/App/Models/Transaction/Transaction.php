<?php

namespace Dev011Brasil\App\Models\Transaction;

use CoreInterfaces\Core\Request\RequestMethod;
use Dev011Brasil\App\Models\Contracts\Parser;
use Dev011Brasil\App\Utils\Validations\Custumer;
use Dev011Brasil\App\Utils\Validations\Transaction as TransactionValidation;
use Dev011Brasil\App\Utils\Validations\Payment;

use Exception;
use \Unirest\Request\Body;
use \Unirest\Request\Request;
use \Unirest\Response;

class Transaction implements Parser
{
    protected $token = null;
    protected $finger_print = null;
    protected $url_environment = null;
    protected $httpClient = null;

    public function __construct($token, $finger_print, $url_environment)
    {
        $this->token = $token;
        $this->finger_print = $finger_print;
        $this->url_environment = $url_environment;
        $this->httpClient = new \Unirest\HttpClient();
    }

    /**
     * Send the body data to Yapay services
     * @param Array $transaction The payment data
     * @return Response The response from Yapay services
     */
    public function pay($transaction = [])
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
                    "card_expdate_month" => "12",
                    "card_expdate_year" => "2022",
                    "card_cvv" => "644",
                    "split" => "1"
                ]
            ];

            $this->setRules($fakePaymentData);

            $this->url_environment .= 'transactions/payment';
            $transaction['finger_print'] = $this->finger_print;
            $transaction['token'] = $this->token;

            // $body = Body::json($transaction);
            $body = Body::json($fakePaymentData);

            $request = new Request($this->url_environment, RequestMethod::POST, [], $body);

            if (!$this->httpClient) throw new Exception('Something is wrong, Try Again!');

            $response = $this->httpClient->execute($request);

            if (!$response) throw new Exception("Something don't work correctly");

            $bodyResponse = $response->getBody();
            $bodyStatusCodeResponse = $response->getStatusCode();
            $messageErrorResponse = null;
            $codeResponse = "";
            $bodyResponseParsed = [];

            if (property_exists($bodyResponse, 'error_response')) {
                $codeResponse = $bodyResponse->error_response->general_errors[0]->code ?? "-1";
            } else if (
                property_exists($bodyResponse, 'data_response')
                &&
                property_exists($bodyResponse->data_response, 'transaction')
                &&
                property_exists($bodyResponse->transaction, 'payment')
            ) {
                $codeResponse = $bodyResponse->data_response->transaction->payment->payment_response_code ?? "-1";
            }

            $messageErrorResponse = $this->getCodeMessage($codeResponse);

            if (!$messageErrorResponse) $bodyResponseParsed = $this->getBodyResponse($bodyResponse);

            return [
                "code" => $bodyStatusCodeResponse,
                "message" => $messageErrorResponse ?? 'Solicitação de transação efetuada',
                "data" => $bodyResponseParsed
            ];
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Refund the transaction by transaction_id
     * @see https://intermediador.dev.yapay.com.br/#/api-cancelar-transacao
     * @param String $transactionID  The transaction ID
     * @return Response The response from Yapay services
     */
    public function refundByID($transactionID = null)
    {
        try {

            if (!$transactionID) throw new Exception();

            $this->url_environment .= 'transactions/cancel';

            $transaction['finger_print'] = $this->finger_print;
            $transaction['token'] = $this->token;

            $body = Body::json([
                "access_token" => $this->token,
                "transaction_id" => 79717
            ]);

            $request = new Request($this->url_environment, RequestMethod::PATCH, [], $body);

            if (!$this->httpClient) throw new Exception('Something is wrong, Try Again!');

            $response = $this->httpClient->execute($request);

            if (!$response) throw new Exception("Something don't work correctly,the request or response is invalid");

            $bodyResponse = $response->getBody();
            $bodyStatusCodeResponse = $response->getStatusCode();
            $messageErrorResponse = null;
            $codeResponse = "";
            $bodyResponseParsed = [];

            if (property_exists($bodyResponse, 'error_response')) {
                $codeResponse = $bodyResponse->error_response->general_errors[0]->code ?? "-1";
            } else if (
                property_exists($bodyResponse, 'data_response')
                &&
                property_exists($bodyResponse->data_response, 'transaction')
                &&
                property_exists($bodyResponse->transaction, 'payment')
            ) {
                $codeResponse = $bodyResponse->data_response->transaction->payment->payment_response_code ?? "-1";
            }

            $messageErrorResponse = $this->getCodeMessage($codeResponse);

            if (!$messageErrorResponse) $bodyResponseParsed = $this->getBodyResponse($bodyResponse);

            return [
                "code" => $bodyStatusCodeResponse,
                "message" => $messageErrorResponse ?? 'Solicitação de transação efetuada',
                "data" => $bodyResponseParsed
            ];
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
                foreach ($data['contacts'] as $contacts) {
                    foreach ($contacts as $field => $value) {
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
            }

            if (array_key_exists('addresses', $data) && (is_array($data['addresses']) && count($data['addresses']) > 0)) {
                foreach ($data['addresses'] as $address) {
                    foreach ($address as $field => $value) {
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

    public function getCodeMessage($codeError = -1)
    {
        try {
            $message = null;
            $code = -1;

            switch ($codeError) {
                case '00':
                    $code = '00';
                    $message = 'APROVADA. TRANSACAO EXECUTADA COM SUCESSO';
                    break;
                case '01':
                    $code = '01';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-01]';
                    break;
                case '12':
                    $code = '12';
                    $message = 'PARCELAMENTO INVALIDO - NAO TENTE NOVAMENTE [ECOM-12]';
                    break;
                case '64':
                    $code = '64';
                    $message = 'VALOR DA TRANSACAO NAO PERMITIDO - NAO TENTE NOVAMENTE [ECOM-64]';
                    break;
                case '76':
                    $code = '76';
                    $message = 'CONTA DESTINO INVALIDA - NAO TENTE NOVAMENTE [ECOM-76]';
                    break;
                case '912':
                    $code = '912';
                    $message = 'FALHA DE COMUNICACAO - TENTE MAIS TARDE [ECOM-912]';
                    break;
                case '911':
                    $code = '911';
                    $message = 'FALHA DE COMUNICACAO - TENTE MAIS TARDE [ECOM-911]';
                    break;
                case '04':
                    $code = '04';
                    $message = 'REFAZER A TRANSACAO [ECOM-04]';
                    break;
                case '06':
                    $code = '06';
                    $message = 'LOJISTA CONTATE O ADQUIRENTE [ECOM-06]';
                    break;
                case 'R1':
                    $code = 'R1';
                    $message = 'SUSPENSAO DE PAGAMENTO RECORRENTE PARA SERVICO - NAO TENTE NOVAMENTE [ECOM-R1]';
                    break;
                case '100':
                    $code = '100';
                    $message = 'CONTATE A CENTRAL DO SEU CARTAO [ECOM-100]';
                    break;
                case '101':
                    $code = '101';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-101]';
                    break;
                case '106':
                    $code = '106';
                    $message = 'EXCEDIDAS TENTATIVAS DE SENHA. CONTATE A CENTRAL DO SEU CARTAO [ECOM-106]';
                    break;
                case '109':
                    $code = '109';
                    $message = 'TRANSACAO NAO PERMITIDA - NAO TENTE NOVAMENTE [ECOM-109]';
                    break;
                case '110':
                    $code = '110';
                    $message = 'VALOR DA TRANSACAO NAO PERMITIDO - NAO TENTE NOVAMENTE [ECOM-110]';
                    break;
                case '115':
                    $code = '115';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-115]';
                    break;
                case '116':
                    $code = '116';
                    $message = 'NAO AUTORIZADA [ECOM-116]';
                    break;
                case '117':
                    $code = '117';
                    $message = 'SENHA INVALIDA [ECOM-117]';
                    break;
                case '122':
                    $code = '122';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-122]';
                    break;
                case '03':
                    $code = '03';
                    $message = 'TRANSACAO NAO PERMITIDA - NAO TENTE NOVAMENTE [ECOM-03]';
                    break;
                case '43':
                    $code = '43';
                    $message = 'TRANSACAO NAO PERMITIDA - NAO TENTE NOVAMENTE [ECOM-43]';
                    break;
                case '05':
                    $code = '05';
                    $message = 'CONTATE A CENTRAL DO SEU CARTAO [ECOM-05]';
                    break;
                case '05':
                    $code = '05';
                    $message = 'CONTATE A CENTRAL DO SEU CARTAO [ECOM-05]';
                    break;
                case '07':
                    $code = '07';
                    $message = 'TRANSACAO NAO PERMITIDA PARA O CARTAO - NAO TENTE NOVAMENTE [ECOM-07]';
                    break;
                case '12':
                    $code = '12';
                    $message = 'ERRO NO CARTAO – NAO TENTE NOVAMENTE [ECOM-12]';
                    break;
                case '13':
                    $code = '13';
                    $message = 'VALOR DA TRANSACAO NAO PERMITIDO - NAO TENTE NOVAMENTE [ECOM-13]';
                    break;
                case '14':
                    $code = '14';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-14]';
                    break;
                case '19':
                    $code = '19';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-14]';
                    break;
                case '23':
                    $code = '23';
                    $message = 'PARCELAMENTO INVALIDO - NAO TENTE NOVAMENTE [ECOM-23]';
                    break;
                case '30':
                    $code = '30';
                    $message = 'ERRO NO CARTAO – NAO TENTE NOVAMENTE [ECOM-30]';
                    break;
                case '38':
                    $code = '38';
                    $message = 'EXCEDIDAS TENTATIVAS DE SENHA. CONTATE A CENTRAL DO SEU CARTAO [ECOM-38]';
                    break;
                case '39':
                    $code = '39';
                    $message = 'UTILIZE FUNCAO DEBITO [ECOM-39]';
                    break;
                case '41':
                    $code = '41';
                    $message = 'TRANSACAO NAO PERMITIDA - NAO TENTE NOVAMENTE [ECOM-41]';
                    break;
                case '43':
                    $code = '43';
                    $message = 'TRANSACAO NAO PERMITIDA - NAO TENTE NOVAMENTE [ECOM-43]';
                    break;
                case '53':
                    $code = '53';
                    $message = 'UTILIZE FUNCAO CREDITO [ECOM-53]';
                    break;
                case '56':
                    $code = '56';
                    $message = 'VERIFIQUE OS DADOS DO CARTAO [ECOM-56]';
                    break;
                case '51':
                    $code = '51';
                    $message = 'NAO AUTORIZADA [ECOM-51]';
                    break;
                case '52':
                    $code = '52';
                    $message = 'UTILIZE FUNCAO CREDITO [ECOM-52]';
                    break;
                case '61':
                    $code = '61';
                    $message = 'VALOR EXCEDIDO. CONTATE A CENTRAL DO SEU CARTAO [ECOM-61]';
                    break;
                case '77':
                    $code = '77';
                    $message = 'CONTA ORIGEM INVALIDA - NAO TENTE NOVAMENTE [ECOM-77]';
                    break;
                case '77':
                    $code = '77';
                    $message = 'CONTA ORIGEM INVALIDA - NAO TENTE NOVAMENTE [ECOM-77]';
                    break;
                case '200':
                    $code = '200';
                    $message = 'TRANSACAO NAO PERMITIDA PARA O CARTAO - NAO TENTE NOVAMENTE [ECOM-200]';
                    break;
                case '003039':
                    $code = '003039';
                    $message = 'Vendedor inválido ou não encontrado';
                    break;
                case '003005':
                    $code = '003005';
                    $message = 'Transação inválida ou inexistente';
                    break;
                default:
                    $message = null;
            }

            $messageMounted = $message ? "$code - $message" : $message;

            return $messageMounted;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getBodyResponse($bodyResponse = null)
    {
        try {
            if (count($bodyResponse) <= 0) throw new Exception("Transaction 174 - The response body is invalid");

            $paymentMethodID = $bodyResponse->data_response->transaction->payment->payment_response_code ?? -1;

            $responseParsed = [
                "price_payment" => 0.0,
                "price_original" => 0.0,
                "status_id" => 0,
                "status_name" => "",
                "order_number" => -1,
                "transaction_id" => -1
            ];

            if ($paymentMethodID <= 0) throw new Exception('Transaction 175 - Payment method not found');

            switch ($paymentMethodID) {
                case 27:
                    $responseParsed['url_payment'] = $bodyResponse->data_response->transaction->payment->url_payment ?? '';
                    $responseParsed['qrcode_path']  = $bodyResponse->data_response->transaction->payment->qrcode_path ?? '';
                    $responseParsed['qrcode_original_path']  = $bodyResponse->data_response->transaction->payment->qrcode_original_path ?? '';
                    $responseParsed['payment']  = 'PIX';

                    break;
                case 6:
                    $responseParsed['url_payment'] = $bodyResponse->data_response->transaction->payment->url_payment ?? '';
                    $responseParsed['payment']  = 'Boleto';
                    break;
                default:
                    //
            }

            $responseParsed['price_payment'] = $bodyResponse->data_response->transaction->payment->price_payment ?? 0.0;
            $responseParsed['price_original'] = $bodyResponse->data_response->transaction->payment->price_original ?? 0.0;

            $responseParsed['status_id'] = $bodyResponse->data_response->transaction->payment->status_id ?? 0.0;
            $responseParsed['status_name'] = $bodyResponse->data_response->transaction->payment->status_name ?? 0.0;
            $responseParsed['order_number'] = $bodyResponse->data_response->transaction->payment->order_number ?? 0.0;
            $responseParsed['transaction_id'] = $bodyResponse->data_response->transaction->payment->transaction_id ?? 0.0;

            return $responseParsed;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
