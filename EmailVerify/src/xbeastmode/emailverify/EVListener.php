<?php
namespace xbeastmode\emailverify;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xbeastmode\emailverify\utils\TokenUtils;
class EVListener implements Listener{

    /** @var EmailVerify */
    private $emailVerify;

    public function __construct(EmailVerify $emailVerify){
        $this->emailVerify = $emailVerify;
    }
    /**
     * @param PlayerJoinEvent $e
     * @priority MONITOR
     */
    public function onJoin(PlayerJoinEvent $e){
        if(!$this->emailVerify->savedTokens()->tokenExists($e->getPlayer())){
            $this->emailVerify->savedTokens()->createToken($e->getPlayer(), TokenUtils::generateToken());
        }
        if(!$this->emailVerify->sessions()->sessionExists($e->getPlayer())
            and !$this->emailVerify->savedTokens()->isTokenValid($e->getPlayer())){
            $this->emailVerify->sessions()->addSession($e->getPlayer());
            if($this->emailVerify->savedTokens()->hasAnEmail($e->getPlayer())){
                $e->getPlayer()->sendMessage(TextFormat::GREEN."Please run \"/verify-token <token>\" to verify your token.\n".TextFormat::GREEN."If you don't know your token, check your email.\n".TextFormat::GREEN."If you did not get an email please run: /email-set resend");
            }else{
                $e->getPlayer()->sendMessage(TextFormat::GREEN."Please run \"/email-set <email>\" to get your verification token.");
            }
        }
    }

    /**
     * @param PlayerQuitEvent $e
     * @priority LOWEST
     */
    public function onQuit(PlayerQuitEvent $e){
        if($this->emailVerify->sessions()->sessionExists($e->getPlayer())){
            $this->emailVerify->sessions()->removeSession($e->getPlayer());
        }
    }

    /**
     * @param EntityTeleportEvent $e
     * @priority LOWEST
     */
    public function onTP(EntityTeleportEvent $e){
        $player = $e->getEntity();
        if($player instanceof Player){
            if($this->emailVerify->sessions()->sessionExists($player)){
                $e->setCancelled();
            }
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $e
     * @priority MONITOR
     */
    public function onCommandPP(PlayerCommandPreprocessEvent $e){
        if($this->emailVerify->sessions()->sessionExists($e->getPlayer())){
            if(explode(" ", $e->getMessage())[0] === "verify-token" or explode(" ", $e->getMessage())[0] === "vt"
                or explode(" ", $e->getMessage())[0] === "email-set" or explode(" ", $e->getMessage())[0] === "e-set") {
                $e->setCancelled(false);
            }
        }
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority LOWEST
     */
    public function onMove(PlayerMoveEvent $e){
        if($this->emailVerify->sessions()->sessionExists($e->getPlayer())){
            $e->setCancelled();
        }
    }
}