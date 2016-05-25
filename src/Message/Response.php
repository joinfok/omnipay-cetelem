<?php

namespace Omnipay\Cetelem\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Cetelem Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data =  (array)$data;
    }

    public function isPending()
    {
        return isset($this->data['customerKey']);
    }

    public function isSuccessful()
    {
        return FALSE;
    }

    public function getCustomerKey()
    {
        return isset($this->data['customerKey'])?$this->data['customerKey']:FALSE;
    }

    public function getMessage()
    {
        return null;
    }

    public function getError()
    {
        return isset($this->data['errorCode']) ? $this->data['errorCode'].$this->data['errorMessage'] : null;
    }

    public function isRedirect()
    {
        // ez az init kézfogás után jelzi, hogy átadható a felület
        return $this->getCustomerKey();
    }

    public function getInitData()
    {
        return $this->request->getData();
    }

    public function redirect()
    {
        $url = $this->request->getEndpoint().'?shopCode=' . $this->request->getShopCode().'&purchaseAmount=' . intval($this->request->getAmount()) . '&customerKey=' . $this->getCustomerKey();
        header('Location: ' . $url);
        exit();
    }


}
