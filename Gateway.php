<?php
namespace Omnipay\Cetelem;

use Omnipay\Common\AbstractGateway;


class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Cetelem';
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
    public function getCustomerKey()
    {
        return $this->getParameter('customerKey');
    }
    public function getBaremId()
    {
        return $this->getParameter('baremId');
    }

    public function setBaremId($value)
    {
        return $this->setParameter('baremId', $value);
    }
    public function getDefaultParameters()
    {
        $settings = parent::getDefaultParameters();
        $settings['society']    = 100;
        $settings['shopCode']   = '';
        $settings['customerKey']= '';
        $settings['baremId']      = '';
        return $settings;

    }
    public function getEndpoint($wsdl = FALSE)
    {
        return $this->getTestMode() ? 'https://ecomdemo.cetelem.hu' : 'https://ecomd.cetelem.hu';
    }
    /**
     * @param array $parameters
     * @return \Omnipay\Cetelem\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cetelem\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Cetelem\Message\PurchaseResponse
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cetelem\Message\PurchaseRequest', $parameters);
    }

    public function validateIPN(array $parameters = array()){        
        log_message('debug',__METHOD__.' ');
        return 1 ? true : false;
    }    
}
