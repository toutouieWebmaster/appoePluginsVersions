<?php

class Paypal
{
    const PAYPAL_URL = 'https://api-3t.paypal.com/nvp?';

    private $request;
    private $response;
    private $errorRequest;
    private $errorPaypal;
    private $listParam;
    private $lastMethode;

    private $successClient = false;
    private $successPaiement = false;

    private $clientData;

    private $token;
    private $payerId;

    private $amt;

    public function __construct($amt)
    {
        $this->amt = $amt;
    }

    public function getAuthApi()
    {
        return array(
            'USER' => 'paypal_api1.aoe-communication.com',
            'PWD' => 'EMRTG4JKF56HLJRB',
            'SIGNATURE' => 'ACUe-E7Hjxmeel8FjYAtjnx-yjHAAD3FkTSFXbONGinhaunm98gGPm0A',
            'VERSION' => '204.0'
        );
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getPayerId()
    {
        return $this->payerId;
    }

    /**
     * @param mixed $payerId
     */
    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;
    }

    /**
     * @return mixed
     */
    public function getAmt()
    {
        return $this->amt;
    }

    /**
     * @param mixed $amt
     */
    public function setAmt($amt)
    {
        $this->amt = $amt;
    }

    /**
     * @return mixed
     */
    public function getClientData()
    {
        return $this->clientData;
    }

    /**
     * @param mixed $clientData
     */
    public function setClientData($clientData)
    {
        $this->clientData = $clientData;
    }

    /**
     * @return bool
     */
    public function isSuccessClient()
    {
        return $this->successClient;
    }

    /**
     * @param bool $successClient
     */
    public function setSuccessClient($successClient)
    {
        $this->successClient = $successClient;
    }

    /**
     * @return bool
     */
    public function isSuccessPaiement()
    {
        return $this->successPaiement;
    }

    /**
     * @param bool $successPaiement
     */
    public function setSuccessPaiement($successPaiement)
    {
        $this->successPaiement = $successPaiement;
    }

    public function prepareFirstRequest(Client $Client)
    {
        $this->request = array(
            'METHOD' => 'SetExpressCheckout',
            'CANCELURL' => webUrl('cancelledOrder/'),
            'RETURNURL' => webUrl('returnOrder/'),
            'PAYMENTREQUEST_0_AMT' => $this->amt,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE',
            'BRANDNAME' => 'hodeshtov.com',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
            'LOCALECODE' => 'FR',
            'EMAIL' => $Client->getEmail(),
            'PAYMENTREQUEST_0_SHIPTONAME' => $Client->getPrenom() . ' ' . $Client->getNom(),
            'PAYMENTREQUEST_0_SHIPTOSTREET' => $Client->getAdresse(),
            'PAYMENTREQUEST_0_SHIPTOCITY' => $Client->getVille(),
            'PAYMENTREQUEST_0_SHIPTOZIP' => $Client->getCodePostal(),
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $Client->getPays(),
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => $Client->getTel(),
            'NOSHIPPING' => 1,
            'ADDROVERRIDE' => 1,
            'SOLUTIONTYPE' => 'Sole',
            'LANDINGPAGE' => 'Billing',
        );


        $this->lastMethode = __METHOD__;
    }

    public function prepareSecondeRequest()
    {
        $this->request = array(
            'METHOD' => 'DoExpressCheckoutPayment',
            'TOKEN' => $this->token,
            'PAYMENTREQUEST_0_AMT' => $this->amt,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
            'PayerID' => $this->payerId,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'sale'
        );
        $this->lastMethode = __METHOD__;
    }

    public function prepareThirdRequest()
    {
        $this->request = array(
            'METHOD' => 'GetExpressCheckoutDetails',
            'TOKEN' => $this->token
        );

        $this->lastMethode = __METHOD__;
    }

    public function executeRequest()
    {
        //prepare query
        $query = self::PAYPAL_URL
            . http_build_query($this->getAuthApi(), '', '&')
            . '&'
            . http_build_query($this->request, '', '&');

        //send query
        $ch = curl_init($query);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, "TLSv1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $this->response = curl_exec($ch);
        $this->errorRequest = curl_error($ch);

        curl_close($ch);

        if (!$this->response) {

            Flash::setMsg('Impossible de se connecter à Paypal.');
            header('location:/');
            exit();

        } else {


            $this->getParam();

            if ($this->listParam['ACK'] == 'Success') {

                $method = explode('::', $this->lastMethode);

                //execute first request
                if ($method[1] == 'prepareFirstRequest') {

                    $this->token = $this->listParam['TOKEN'];

                    //header("location:https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=" . $this->token);
                    header("location:https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=" . $this->token);
                    //header("location:https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=" . $this->token);

                    exit();

                    //execute seconde request
                } elseif ($method[1] == 'prepareSecondeRequest') {

                    $this->successPaiement = true;

                    //execute third request
                } elseif ($method[1] == 'prepareThirdRequest') {

                    $this->successClient = true;
                }

            } else {

                $this->errorPaypal = 'Erreur de communication avec le serveur PayPal. ' . $this->listParam['L_SHORTMESSAGE0'] . '. ' . $this->listParam['L_LONGMESSAGE0'];

                Flash::setMsg($this->errorPaypal);
                header('location:/');
                exit();
            }

        }
    }

    public function getParam()
    {

        $liste_param_paypal = [];

        $liste_parametres = explode("&", $this->response);

        foreach ($liste_parametres as $param_paypal) {

            list($nom, $valeur) = explode("=", $param_paypal);

            $liste_param_paypal[$nom] = urldecode($valeur);

        }

        $this->listParam = $liste_param_paypal;

    }


}