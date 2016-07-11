<?php
namespace xbeastmode\emailverify\events;
use xbeastmode\emailverify\EmailVerify;
class TokenExpireEvent extends EmailVerifyEvent{
    public static $handlerList = null;

    public function __construct(EmailVerify $emailVerify, $token){
        parent::__construct($emailVerify);
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(){
        return $this->token;
    }
}