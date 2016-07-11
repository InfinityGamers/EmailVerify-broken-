<?php
namespace xbeastmode\emailverify\commands;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xbeastmode\emailverify\EmailVerify;
class EmailCommand extends Command implements PluginIdentifiableCommand{

    /** @var EmailVerify */
    private $emailVerify;

    /** @var Player[] */
    private $confirm = [];

    public function __construct(EmailVerify $emailVerify){
        parent::__construct("email-set", "", "Error. Please use: /email-set <email>", ["e-set"]);
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
        if(isset($args[0])){
            if(strtolower($args[0]) === "resend" and !isset($this->confirm[$sender->getName()])){
                if($this->emailVerify->savedTokens()->hasAnEmail($sender)){
                    if($this->emailVerify->savedTokens()->sendTokenMail($sender)) {
                        $sender->sendMessage(TextFormat::GREEN . "Email has been resent!");
                    }else{
                        $sender->sendMessage(TextFormat::RED . "Email could not be sent.");
                    }
                }else{
                    $sender->sendMessage(TextFormat::RED."You do not have an email set. To set an email, please use: /email-set <email>");
                }
                return;
            }
            if(filter_var($args[0], FILTER_VALIDATE_EMAIL) and !isset($this->confirm[$sender->getName()])){
                if(!$this->emailVerify->savedEmails()->emailExists($args[0])){
                    $this->confirm[$sender->getName()] = $args[0];
                    $sender->sendMessage(TextFormat::YELLOW."Would you like to set {$args[0]} as a permanent email? Please run: ".TextFormat::GREEN."/email-set <yes|no>");
                }else{
                    $sender->sendMessage(TextFormat::RED."Oops! Someone already registered that email, try another one!");
                }
            }elseif(!filter_var($args[0], FILTER_VALIDATE_EMAIL) and !isset($this->confirm[$sender->getName()])){
                $sender->sendMessage(TextFormat::RED."Invalid email format, try again.");
            }
            if(isset($this->confirm[$sender->getName()]) and (strtolower($args[0]) === "yes")){
                $this->emailVerify->savedEmails()->setEmail($this->confirm[$sender->getName()]);
                $this->emailVerify->savedTokens()->setTokenEmail($sender, $this->confirm[$sender->getName()]);
                if($this->emailVerify->savedTokens()->sendTokenMail($sender)) {
                    $sender->sendMessage(TextFormat::AQUA . "Successfully added email! Please check your email for the token.");
                }else{
                    $sender->sendMessage(TextFormat::AQUA . "Successfully added email, but could not send token.");
                }
                $this->emailVerify->savedTokens()->sendTokenMail($sender);
                unset($this->confirm[$sender->getName()]);
            }elseif(isset($this->confirm[$sender->getName()]) and (strtolower($args[0]) === "no")){
                $sender->sendMessage(TextFormat::GREEN."Cancelled your request.");
                unset($this->confirm[$sender->getName()]);
            }
        }else{
            $sender->sendMessage(TextFormat::RED.$this->getUsage());
        }
    }

    /**
     * @return EmailVerify
     */
    public function getPlugin(){
        return $this->emailVerify;
    }
}