<?php

namespace Omnipay\Cetelem\Message;

/**
 * Cetelem Abstract Request
 *
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = '1.0.0';

    protected $liveEndpoint = 'https://ecom.cetelem.hu';
    protected $testEndpoint = 'https://ecomdemo.cetelem.hu';
    protected $soap         = '/ecommerce/EcommerceService?wsdl';
    protected $validation   = '/cetelem_aruhitel/hitelbiralat';
    

    public function getApiVersion(){
        return 'Netfort-Cetelem-Ver'.(self::API_VERSION);
    }
    public function getSociety()
    {
        return $this->getParameter('society');
    }

    public function setSociety($value)
    {
        return $this->setParameter('society', $value);
    }
    public function getShopCode()
    {
        return $this->getParameter('shopCode');
    }

    public function setShopCode($value)
    {
        return $this->setParameter('shopCode', $value);
    }
    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }
    public function setTimeout($value)
    {
        return $this->setParameter('timeout', $value);
    }
    public function getTimeout($value)
    {
        return $this->getParameter('timeout', $value);
    }
    public function setTimeoutUrl($value)
    {
        return $this->setParameter('timeoutUrl', $value);
    }
    public function getTimeoutUrl($value)
    {
        return $this->getParameter('timeoutUrl', $value);
    }
    public function setRedirectUrl($value)
    {
        return $this->setParameter('redirectUrl', $value);
    }
    public function getRedirectUrl($value)
    {
        return $this->getParameter('redirectUrl', $value);
    }
    public function setMainUrl($value)
    {
        return $this->setParameter('mainUrl', $value);
    }
    public function getMaintUrl($value)
    {
        return $this->getParameter('mainUrl', $value);
    }
    public function setDeniedUrl($value)
    {
        return $this->setParameter('deniedUrl', $value);
    }
    public function getDeniedUrl($value)
    {
        return $this->getParameter('deniedUrl', $value);
    }
    public function setAcceptedUrl($value)
    {
        return $this->setParameter('acceptedUrl', $value);
    }
    public function getAcceptedUrl($value)
    {
        return $this->getParameter('acceptedUrl', $value);
    }
    public function setWaitingUrl($value)
    {
        return $this->setParameter('waitingUrl', $value);
    }
    public function getWaitingUrl($value)
    {
        return $this->getParameter('waitingUrl', $value);
    }
    public function getBaremId()
    {
        return $this->getParameter('baremId');
    }

    public function setBaremId($value)
    {
        return $this->setParameter('baremId', $value);
    }
    public function getArticleId()
    {
        return $this->getParameter('articleId');
    }

    public function setArticleId($value)
    {
        return $this->setParameter('articleId', $value);
    }
    public function getCustomerKey()
    {
        return $this->getParameter('customerKey');
    }

    public function getEndpoint($wsdl = FALSE)
    {
        return ($this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint).$this->getApiPoint($wsdl);
    }

    public function getApiPoint($wsdl = FALSE)
    {
        return $wsdl ? $this->soap : $this->validation;
    }
    public function getData(){
        return $this->data;
    }
    public function sendData($data)
    {
        //return $this->response = new PurchaseResponse($this, $data, $this->getEndpoint());
    }
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
