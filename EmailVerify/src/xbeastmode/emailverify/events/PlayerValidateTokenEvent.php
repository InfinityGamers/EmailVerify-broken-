<?php
namespace xbeastmode\emailverify\events;
use pocketmine\event\Cancellable;
use pocketmine\Player;
use xbeastmode\emailverify\EmailVerify;
class PlayerValidateTokenEvent extends EmailVerifyEvent implements Cancellable{
    public static $handlerList = null;

    /** @var Player */
    private $player;

    /** @var string */
    private $token;

    public function __construct(EmailVerify $emailVerify, Player $p, $token){
        parent::__construct($emailVerify);
        $this->player = $p;
        $this->token = $token;
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player{
        return $this->player;
    }

    /**
     * @return string
     */
    public function getToken(){
        return $this->token;
    }
}