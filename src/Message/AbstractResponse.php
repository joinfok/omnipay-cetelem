<?php
/**
 * Abstract Response
 */

namespace Omnipay\Cetelem\Message;

use Omnipay\Common\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Abstract Response
 *
 * This abstract class implements ResponseInterface and defines a basic
 * set of functions that all Omnipay Requests are intended to include.
 *
 * Objects of this class or a subclass are usually created in the Request
 * object (subclass of AbstractRequest) as the return parameters from the
 * send() function.
 *
 * Example -- validating and sending a request:
 *
 * <code>
 *   $myResponse = $myRequest->send();
 *   // now do something with the $myResponse object, test for success, etc.
 * </code>
 *
 * @see ResponseInterface
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse implements ResponseInterface
{

    /**
     * The embodied request object.
     *
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * The data contained in the response.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * Get the initiating request object.
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function isRedirect()
    {
        return false;
    }

    public function isTransparentRedirect()
    {
        return false;
    }

    public function isCancelled()
    {
        return false;
    }

    /**
     * Get the response data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getMessage()
    {
        return null;
    }

    public function getCode()
    {
        return null;
    }

    public function getTransactionReference()
    {
        return null;
    }

    /**
     * Automatically perform any required redirect
     *
     * This method is meant to be a helper for simple scenarios. If you want to customize the
     * redirection page, just call the getRedirectUrl() and getRedirectData() methods directly.
     *
     * @codeCoverageIgnore
     */
    public function redirect()
    {
        $this->getRedirectResponse()->send();
        exit;
    }

    public function getRedirectResponse()
    {
        if (!$this instanceof RedirectResponseInterface || !$this->isRedirect()) {
            throw new RuntimeException('This response does not support redirection.');
        }

        if ('GET' === $this->getRedirectMethod()) {
            return HttpRedirectResponse::create($this->getRedirectUrl());
        } elseif ('POST' === $this->getRedirectMethod()) {
            $hiddenFields = '';
            foreach ($this->getRedirectData() as $key => $value) {
                if (!is_array($value)){
                $hiddenFields .= sprintf(
                    '<input type="hidden" name="%1$s" value="%2$s" />',
                    htmlentities($key, ENT_QUOTES, 'UTF-8', false),
                    htmlentities($value, ENT_QUOTES, 'UTF-8', false)
                )."\n";
                }else{ // tömb, elvileg csak a termékeknél az ORDERS_alapján
                    foreach($value as $k=>$v){
                        $hiddenFields .= sprintf(
                                '<input type="hidden" name="%1$s" value="%2$s" />',
                                htmlentities($key.'[]', ENT_QUOTES, 'UTF-8', false),
                                htmlentities($v, ENT_QUOTES, 'UTF-8', false)
                            )."\n";
                    }
                }
            }

            $output = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Redirecting to Cetelem</title>
    </head>
    <body onload="document.forms[0].submit();">
        <form action="%1$s" method="post">
            <p>Redirecting to payment page...</p>
            <p>
                %2$s
                <input type="submit" value="Cetelem">
            </p>
        </form>
    </body>
</html>';
            $output = sprintf(
                $output,
                htmlentities($this->getRedirectUrl(), ENT_QUOTES, 'UTF-8', false),
                $hiddenFields
            );

            return HttpResponse::create($output);
        }

        throw new RuntimeException('Invalid redirect method "'.$this->getRedirectMethod().'".');
    }
}
