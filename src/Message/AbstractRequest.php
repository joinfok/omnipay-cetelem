<?php

namespace Omnipay\Cetelem\Message;

/**
 * Cetelem Abstract Request
 *
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = '1.0.0';

    protected $liveEndpoint = 'https://secure.cetelem.hu/order/lu.php';
    protected $testEndpoint = 'https://secure.cetelem.hu/order/lu.php';
    protected $iosEndpoint  = 'https://secure.cetelem.hu/ios.php';
    protected $luEndpoint   = 'https://secure.cetelem.hu/lu.php';
    protected $aluEndpoint  = 'https://secure.cetelem.hu/alu.php';
    protected $idnEndpoint  = 'https://secure.cetelem.hu/idn.php';
    protected $irnEndpoint  = 'https://secure.cetelem.hu/irn.php';
    protected $ocEndpoint   = 'https://secure.cetelem.hu/tokens';

    public function getApiVersion(){
        return 'Netfort-Cetelem-Ver'.(self::API_VERSION);
    }
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }
    public function getMethod()
    {
        return $this->getParameter('method');
    }

    public function setMethod($value)
    {
        return $this->setParameter('method', $value);
    }

    public function getOrderDate()
    {
        return $this->getParameter('orderDate');
    }

    public function setOrderDate($value)
    {
        return $this->setParameter('orderDate', $value);
    }
    public function getBackRef()
    {
        return $this->getParameter('backRef');
    }

    public function setBackRef($value)
    {
        return $this->setParameter('backRef', $value);
    }
    public function getTimeoutUrl()
    {
        return $this->getParameter('timeoutUrl');
    }

    public function setTimeoutUrl($value)
    {
        return $this->setParameter('timeoutUrl', $value);
    }
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }
    public function getAutomode()
    {
        return $this->getParameter('automode');
    }

    public function setAutomode($value)
    {
        return $this->setParameter('automode', $value);
    }

    public function setDiscount($value)
    {
        return $this->setParameter('discount', $value);
    }
    public function getDiscount()
    {
        return $this->getParameter('discount');
    }


    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data, $this->getEndpoint());
    }

    public function checkCtrl(){}
}
