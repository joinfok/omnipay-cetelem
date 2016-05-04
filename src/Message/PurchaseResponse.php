<?php

namespace Omnipay\Cetelem\Message;

use Omnipay\Cetelem\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Cetelem Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $redirectUrl;

    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {
        parent::__construct($request, $data);
        $this->redirectUrl = $redirectUrl;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->data;
    }
    public function getError(){
        return $this->getMessage();
    }



    /**
     * Validates CTRL variable
     *
     * @return boolean
     *
     */
    public function checkCtrl($ctrl=NULL){
        $requestURL = substr($this->createRequestUriNotGiven(), 0, -38); //the last 38 characters are the CTRL param
        $hashInput = strlen($requestURL).$requestURL;
        //optional debug info, no need for live payment
        /*
        echo "\n<br />".my_print_r($_SERVER);
        echo "\n<br />".$requestURL;
        echo "\n<br />".$this->request->hashCtrl($hashInput);
        echo "\n<br />".$ctrl;
        die;
        */
        return ($ctrl == $this->request->hashCtrl($hashInput))?true:false;
    }

    /**
     * Creates request URI from HTTP SERVER VARS.
     * Handles http and https
     *
     * @return void
     *
     */
    protected function createRequestUriNotGiven()
    {
        $protocol = "http://";
        if (isset($_SERVER['HTTP_FRONT_END_HTTPS']) and $_SERVER['HTTP_FRONT_END_HTTPS'] == "On") {
            $protocol = "https://";
        }
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = "https://";
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $protocol = "https://";
        }
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}

