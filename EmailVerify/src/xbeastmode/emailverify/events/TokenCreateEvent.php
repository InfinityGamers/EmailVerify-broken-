<?php
namespace xbeastmode\emailverify\events;
use pocketmine\event\plugin\PluginEvent;
use xbeastmode\emailverify\EmailVerify;
class TokenCreateEvent extends PluginEvent{
    public static $handlerList = null;

    /** @var string */
    private $token;

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

    /**
     * @param string $token
     */
    public function setToken($token){
        $this->token = $token;
    }
}