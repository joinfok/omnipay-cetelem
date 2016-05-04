<?php
namespace Omnipay\Cetelem;

use Omnipay\Common\AbstractGateway;


class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Cetelem';
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

    public function getDefaultParameters()
    {
        $settings = parent::getDefaultParameters();
        $settings['merchantId'] = '';
        $settings['secretKey']  = '';
        $settings['currency']   = 'HUF';
        $settings['method']     = '';
        $settings['language']   = 'HU';
        $settings['automode']   = 1;    // Ha „1” értékkel van átadva, akkor a vásárló azonnal a kártyaadatok megadásához érkezik. Ha „0” értékkel kerül elküldésre, akkor a Cetelem fizetőoldala szerkeszthető formában megjeleníti a számlázási adatokat. Ezt élesítéskor 1-re kell állítani, hogy a vásárló ne szerkeszthesse az adatait a weboldalról történő átirányítás után. Alapvetően a fejlesztő részére ellenőrzésre használható, hogy átadásra kerülnek-e a számlázási paraméterek.
        return $settings;

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
        $successfulStatus = array(
            "PAYMENT_AUTHORIZED",   //IPN
            "COMPLETE",             //IDN
            "REFUND",               //IRN
            "PAYMENT_RECEIVED",     //WIRE
            "CASH",                 //CASH
        );
        $hash = isset($parameters['HASH'])?$parameters['HASH']:false;

        if ($hash == false) return false;
        if (!in_array(trim($parameters['ORDERSTATUS']), $successfulStatus)) {
            return false;
        }
        unset($parameters['HASH']);
        $chash = $this->createHashString($parameters);
        log_message('debug',__METHOD__.' HASHED:'.print_r($parameters,true).' HASH:'.$chash.' refHash:'.$hash);
        return $chash === $hash ? true : false;
    }

    /**
     * Creates INLINE string for corfirmation
     *
     * @return string $string <EPAYMENT> tag
     *
     */
    public function confirmReceived(array $parameters = array())
    {
        $serverDate = date("YmdHis");
        $hashArray = array(
            $parameters['IPN_PID'][0],
            $parameters['IPN_PNAME'][0],
            $parameters['IPN_DATE'],
            $serverDate
        );
        $hash = $this->createHashString($hashArray);
        $string = "<EPAYMENT>".$serverDate."|".$hash."</EPAYMENT>";
        return $string;
    }
    /**
     * HMAC HASH creation
     * RFC 2104
     * http://www.ietf.org/rfc/rfc2104.txt
     *
     * @param string $key  Secret key for encryption
     * @param string $data String to encode
     *
     * @return string HMAC hash
     *
     */
    private function hmac($key, $data)
    {
        $byte = 64; // byte length for md5
        if (strlen($key) > $byte) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $byte, chr(0x00));
        $ipad = str_pad('', $byte, chr(0x36));
        $opad = str_pad('', $byte, chr(0x5c));
        $kIpad = $key ^ $ipad;
        $kOpad = $key ^ $opad;
        return md5($kOpad . pack("H*", md5($kIpad . $data)));
    }

    /**
     * Create HASH code for an array (1-dimension only)
     *
     * @param array $hashData Array of ordered fields to be HASH-ed
     *
     * @return string Hash code
     *
     */
    private function createHashString($hashData)
    {
        $hashString = '';
        foreach ($hashData as $field) {
            if (is_array($field)) {
                foreach($this->flatArray($field) as $k=>$v){
                    if (is_array($v)){
                        log_message('error',__METHOD__.' a HASH tömb mélysége 2-nél nagyobb!');
                        return false;
                    }else{
                        $hashString .= strlen(StripSlashes($v)).$v;
                    }
                }
            }else{
                $hashString .= strlen(StripSlashes($field)).$field;
            }
        }

        return $this->hmac($this->getSecretKey(), $hashString);
    }

    /**
     * Creates a 1-dimension array from a 2-dimension one
     *
     * @param array $array Array to be processed
     * @param array $skip  Array of keys to be skipped when creating the new array
     *
     * @return array $return Flat array
     *
     */
    private function flatArray($array = array(), $skip = array())
    {
        $return = array();
        foreach ($array as $name => $item) {
            if (!in_array($name, $skip)) {
                if (is_array($item)) {
                    foreach ($item as $subItem) {
                        $return[] = $subItem;
                    }
                } elseif (!is_array($item)) {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }


}
