<?php
namespace xbeastmode\emailverify\commands;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xbeastmode\emailverify\EmailVerify;
use xbeastmode\emailverify\events\PlayerValidateTokenEvent;
class VerifyTokenCommand extends Command implements PluginIdentifiableCommand{

    /** @var EmailVerify */
    private $emailVerify;

    public function __construct(EmailVerify $emailVerify){
        parent::__construct("verify-token", "", "Error. Please use: /verify-token <token>", ["vt"]);
        $this->emailVerify = $emailVerify;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(!$sender instanceof Player){ $sender->sendMessage("Please run command in-game"); return; }
        if(isset($args[0])) {
            if(!$this->emailVerify->savedTokens()->hasAToken($sender)){
                $sender->sendMessage(TextFormat::YELLOW . "Sorry, but you do not have a token. Contact and admin for help.");
                return;
            }
            if ($this->emailVerify->savedTokens()->validateToken($sender, $args[0])) {
                $pvte = new PlayerValidateTokenEvent($this->emailVerify, $sender, $args[0]);
                $this->emailVerify->getServer()->getPluginManager()->callEvent($pvte);
                if ($pvte->isCancelled()) return;
                $this->emailVerify->sessions()->removeSession($sender);
                $sender->sendMessage(TextFormat::GREEN . "Your token is now valid, thank you for verifying!");
            } else {
                $sender->sendMessage(TextFormat::RED . "That token is not valid, please try again.");
            }
        }else{
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        }
    }

    /**
     * @return EmailVerify
     */
    public function getPlugin() : EmailVerify{
        return $this->emailVerify;
    }
}