<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: plugin/hai_user_phone.proto

namespace Akaxin\Proto\Plugin;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * 
 */
class HaiUserPhoneResponse extends \Google\Protobuf\Internal\Message
{
    /**
     *国际区号+86
     *
     * 
     */
    private $country_code = '';
    /**
     *手机号
     *
     * 
     */
    private $phone_id = '';

    public function __construct() {
        \GPBMetadata\Plugin\HaiUserPhone::initOnce();
        parent::__construct();
    }

    /**
     *国际区号+86
     *
     * 
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     *国际区号+86
     *
     * 
     * @param string $var
     * @return $this
     */
    public function setCountryCode($var)
    {
        GPBUtil::checkString($var, True);
        $this->country_code = $var;

        return $this;
    }

    /**
     *手机号
     *
     * 
     * @return string
     */
    public function getPhoneId()
    {
        return $this->phone_id;
    }

    /**
     *手机号
     *
     * 
     * @param string $var
     * @return $this
     */
    public function setPhoneId($var)
    {
        GPBUtil::checkString($var, True);
        $this->phone_id = $var;

        return $this;
    }

}

