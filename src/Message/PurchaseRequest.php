<?php

namespace Omnipay\Cetelem\Message;

use Omnipay\Cetelem\Message\AbstractRequest;


/**
 * Cetelem Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public function getParameters()
    {
        return $this->parameters->all();
    }

    public function getData()
    {
        $data = array();
        // A feldolgozási sorrenden ne változtass!!

        $data['society']    = (int)$this->getSociety();
        $data['shopCode']   = (int)$this->getShopCode(); //

        //$items = $this->getItems();
        $card  = $this->getCard();

           if ($card) {
            $json_data['barem']     = (int)$this->getBaremId();
            $json_data['articleId'] = (int)$this->getParameter('articleId');
            $json_data['lastName']  = $this->encodeToUtf8(substr($card->getBillingLastName(), 0, 30)); //
            $json_data['firstName'] = $this->encodeToUtf8(substr($card->getBillingFirstName(), 0, 30)); //
            $json_data['email']     = $this->encodeToUtf8(substr(str_ireplace('cetelem@cetelem.hu','',$card->getEmail()), 0, 255)); //[ $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; ]
            $json_data['phone']     = $this->encodeToUtf8(substr(preg_replace('/\D/','',$card->getBillingPhone()), 0, 32));
            $json_data['mobile']    = $this->encodeToUtf8(substr(preg_replace('/\D/','',$card->getBillingPhone()), 0, 32));
            $json_data['city']      = $this->encodeToUtf8(substr($card->getBillingCity(), 0, 32));
            $json_data['address']   = $this->encodeToUtf8(substr($card->getBillingAddress1().$card->getBillingAddress2(), 0, 32));
            $json_data['additional']= $this->encodeToUtf8(substr($card->getBillingAddress1().$card->getBillingAddress2(), 32, 25));
            $json_data['pcode']     = substr(preg_replace('/\D/','',$card->getBillingPostcode()), 0, 4);

            $json_data['cbZold']    = $this->encodeToUtf8(substr($this->getParameter('acceptedUrl').'&token='.$this->getParameter('transactionReference'), 0, 255));
            $json_data['cbNarancs'] = $this->encodeToUtf8(substr($this->getParameter('waitingUrl').'&token='.$this->getParameter('transactionReference'), 0, 255));
            $json_data['cbPiros']   = $this->encodeToUtf8(substr($this->getParameter('deniedUrl').'&token='.$this->getParameter('transactionReference'), 0, 255));
            $json_data['cbUj']      = $this->encodeToUtf8(substr($this->getParameter('redirectUrl').'?token='.$this->getParameter('transactionReference'), 0, 255));
            $json_data['cbMainpage']= $this->encodeToUtf8(substr($this->getParameter('mainUrl').'?token='.$this->getParameter('transactionReference'), 0, 255));
            $json_data['cbTimeout'] = $this->encodeToUtf8(substr($this->getParameter('timeoutUrl').'&token='.$this->getParameter('transactionReference'), 0, 255));
        }
        $json_data['APPINFO'] = $this->getApiVersion();
        // üres mezők nem lehetnek a tömbben
        foreach ($json_data as $k=>&$v){
            if (empty($v)) unset($v);
        }
        $data['jsonData']   = $json_data;
        return $data;
    }

    public function sendData($data)
    {
        $response = NULL;
        if ($this->getTestMode()){
            ini_set('soap.wsdl_cache', '0');
        }else{
            ini_set('soap.wsdl_cache_enable', '1');
            ini_set('soap.wsdl_cache','1');
        }
        try {
            ini_set('soap.wsdl_cache', '0');
            $client = new \SoapClient($this->getEndpoint(TRUE));
            $response = json_decode($client->initProcess($data['society'],$data['shopCode'],json_encode($data['jsonData'])));
        }catch (\Exception $e){
            log_message('error',__METHOD__.': Soap connection failed:'.$e->getMessage());
        }
        return $this->response = $this->createResponse($response);
    }
    
    private function encodeToUtf8($string)
    {
        $str = preg_replace('/[^a-z0-9\s\-_=?!.,;:&#$;*+@<>\[\]<>()\\\'"\/?íéáóöőüűúä]/i', '?', $string);
        if (mb_detect_encoding($str, "UTF-8", true)) {
            return $str;

        } else {
            return mb_convert_encoding($string, "UTF-8");
        }
    }
}