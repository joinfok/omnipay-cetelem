<?php

namespace Omnipay\Cetelem\Message;

use Omnipay\Cetelem\Message\AbstractRequest;


/**
 * Cetelem Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    private $hashCode;
    private $hashString;

    public function getParameters()
    {
        return $this->parameters->all();
    }

    public function getData()
    {

        $data = array();
        // A feldolgozási sorrenden ne változtass!!

        $data['MERCHANT'] = substr($this->getMerchantId(), 0, 7); //
        $data['ORDER_REF'] = substr($this->getParameter('transactionId'), 0, 20); //
        $data['ORDER_DATE'] = substr($this->getParameter('orderDate'), 0, 19); //

        $items = $this->getItems();
        /*
         *
         * A Cetelem sorrendiség alapú feldolgozása szerint kell így összeálítani az items blokkot!!!!!
          A HASH és a FORM küldéskor figyelni kell a feldolgozásra!!!!
          HASH FIELDS
-----------------------------------------------------------------------------------
Array
(
    [0] => MERCHANT
    [1] => ORDER_REF
    [2] => ORDER_DATE
    [3] => ORDER_PNAME
    [4] => ORDER_PCODE
    [5] => ORDER_PINFO
    [6] => ORDER_PRICE
    [7] => ORDER_QTY
    [8] => ORDER_VAT
    [9] => ORDER_SHIPPING
    [10] => PRICES_CURRENCY
    [11] => DISCOUNT
    [12] => PAY_METHOD
)

HASH DATA
-----------------------------------------------------------------------------------
Array
(
    [0] => P152401
    [1] => 1270011433425358
    [2] => 2015-06-04 15:42:38
    [3] => Lorem 1
    [4] => Duis 2
    [5] => sku0001
    [6] => sku0002
    [7] => Lorem ipsum dolor sit amet
    [8] => Duis aute (ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP)
    [9] => 30
    [10] => 51
    [11] => 2
    [12] => 3
    [13] => 0
    [14] => 0
    [15] => 20
    [16] => HUF
    [17] => 30
    [18] => CCVISAMC
)
         */
        foreach ($items as $k => $item) {
            $data['ORDER_PNAME'][$k] = $this->encodeToUtf8(substr($item->getName(), 0, 155));
            $data['ORDER_PCODE'][$k] = preg_replace('/[^a-zA-Z0-9]/', '', substr($item->getCode(), 0, 20));
            $data['ORDER_PINFO'][$k] = $this->encodeToUtf8(substr($item->getInfo(), 0, 155));
            $data['ORDER_PRICE'][$k] = str_ireplace(',', '.', substr($item->getPrice(), 0, 20));
            $data['ORDER_QTY'][$k] = substr($item->getQuantity(), 0, 20);
            $data['ORDER_VAT'][$k] = substr($item->getVat(), 0, 2);
        }

        $data['ORDER_SHIPPING'] = 0; // külön tételként jön a kosárba
        $data['PRICES_CURRENCY'] = substr($this->getCurrency(), 0, 3); // ISO kód
        $data['DISCOUNT'] = substr($this->getParameter('discount'), 0, 20); //
        $data['PAY_METHOD'] = substr($this->getMethod(), 0, 32); //

        // Az ORDER_HASH-t itt kell generálni, mert csak a fenti mezők és csak ebben a sorrendben kellenek bele!
        $data['ORDER_HASH'] = $this->createHashString($data);

        $data['BACK_REF'] = substr($this->getBackRef() . '?token=' . $this->getParameter('transactionReference'), 0, 155); //
        $data['TIMEOUT_URL'] = substr($this->getTimeoutUrl() . '?token=' . $this->getParameter('transactionReference'), 0, 155); //
        $data['LANGUAGE'] = substr($this->getParameter('language'), 0, 2); //
        $data['AOTOMODE'] = $this->getParameter('automode'); //
        $data['ORDER_TIMEOUT'] = 300; //
        $card = $this->getCard();
        if ($card) {

            $data['BILL_LNAME'] = $this->encodeToUtf8(substr($card->getBillingLastName(), 0, 155)); //
            $data['BILL_FNAME'] = $this->encodeToUtf8(substr($card->getBillingFirstName(), 0, 155)); //
            $data['BILL_EMAIL'] = $this->encodeToUtf8(substr($card->getEmail(), 0, 155)); //[ $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; ]
            $data['BILL_PHONE'] = $this->encodeToUtf8(substr($card->getBillingPhone(), 0, 155)); //
            //opcios
            if (strlen($card->getBillingCompany())) {
                $data['BILL_COMPANY'] = $this->encodeToUtf8(substr($card->getBillingCompany(), 0, 155)); //
                $data['BILL_FISCALCODE'] = $this->encodeToUtf8(substr($card->getNumber(), 0, 155)); //Cég adószám
            }
            $data['BILL_COUNTRYCODE'] = substr($card->getBillingCountry(), 0, 2); //
            $data['BILL_STATE'] = $this->encodeToUtf8(substr($card->getBillingState(), 0, 155)); //
            $data['BILL_CYTI'] = $this->encodeToUtf8(substr($card->getBillingCity(), 0, 155)); //
            $data['BILL_ADDRESS'] = $this->encodeToUtf8(substr($card->getBillingAddress1(), 0, 155));
            $data['BILL_ADDRESS2'] = $this->encodeToUtf8(substr($card->getBillingAddress2(), 0, 155));
            $data['BILL_ZIPCODE'] = substr($card->getBillingPostcode(), 0, 20);

            $data['DELIVERY_FNAME'] = $this->encodeToUtf8(substr($card->getShippingFirstName(), 0, 155));
            $data['DELIVERY_LNAME'] = $this->encodeToUtf8(substr($card->getShippingLastName(), 0, 155));
            $data['DELIVERY_PHONE'] = substr($card->getShippingPhone(), 0, 155);
            $data['DELIVERY_ADDRESS'] = $this->encodeToUtf8(substr($card->getShippingAddress1(), 0, 155));
            $data['DELIVERY_ADDRESS2'] = $this->encodeToUtf8(substr($card->getShippingAddress2(), 0, 155));
            $data['DELIVERY_ZIPCODE'] = substr($card->getShippingPostcode(), 0, 20);
            $data['DELIVERY_CITY'] = $this->encodeToUtf8(substr($card->getShippingCity(), 0, 155));
            $data['DELIVERY_STATE'] = $this->encodeToUtf8(substr($card->getShippingState(), 0, 155));
            $data['DELIVERY_COUNTRYCODE'] = substr($card->getShippingCountry(), 0, 2);
        }
        $data['APPINFO'] = $this->getApiVersion();

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data, $this->getEndpoint());
    }

    public function hashCtrl($ctrl)
    {
        return $this->hmac($this->getSecretKey(), $ctrl);
    }

    /**
     * HMAC HASH creation
     * RFC 2104
     * http://www.ietf.org/rfc/rfc2104.txt
     *
     * @param string $key Secret key for encryption
     * @param string $data String to encode
     *
     * @return string HMAC hash
     *
     */
    protected function hmac($key, $data)
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
    protected function createHashString($hashData)
    {
        $hashString = '';
        foreach ($hashData as $field) {
            if (is_array($field)) {
                foreach ($this->flatArray($field) as $k => $v) {
                    if (is_array($v)) {
                        log_message('error', __METHOD__ . ' a HASH tömb mélysége 2-nél nagyobb!');
                        return false;
                    } else {
                        $hashString .= strlen(StripSlashes($v)) . $v;
                    }
                }
            } else {
                $hashString .= strlen(StripSlashes($field)) . $field;
            }
        }
        $this->hashString = $hashString;
        $this->hashCode = $this->hmac($this->getSecretKey(), $this->hashString);
        return $this->hashCode;
    }

    /**
     * Creates a 1-dimension array from a 2-dimension one
     *
     * @param array $array Array to be processed
     * @param array $skip Array of keys to be skipped when creating the new array
     *
     * @return array $return Flat array
     *
     */
    public function flatArray($array = array(), $skip = array())
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