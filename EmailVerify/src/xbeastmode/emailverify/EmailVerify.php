<?php
namespace xbeastmode\emailverify;
use pocketmine\plugin\PluginBase;
use xbeastmode\emailverify\commands\EmailCommand;
use xbeastmode\emailverify\commands\VerifyTokenCommand;
use xbeastmode\emailverify\files\SavedEmails;
use xbeastmode\emailverify\files\SavedTokens;
use xbeastmode\emailverify\files\Sessions;
use xbeastmode\emailverify\tasks\ExpiredTokenTask;
class EmailVerify extends PluginBase{

    /** @var SavedTokens */
    public $savedTokens;

    /** @var SavedEmails */
    public $savedEmails;

    /** @var Sessions */
    public $sessions;

    public function onEnable(){
        if(!file_exists($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        if(!file_exists($this->getDataFolder()."files/")){
            mkdir($this->getDataFolder() . "files/");
        }
        $this->savedTokens = new SavedTokens($this);
        $this->sessions = new Sessions();
        $this->savedEmails = new SavedEmails($this);
        $this->saveDefaultConfig();
        $this->savedTokens->initFolder();
        $this->savedEmails->initFolder();
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ExpiredTokenTask($this), 20*60*10);
        $this->getServer()->getCommandMap()->register("verify-token", new VerifyTokenCommand($this));
        $this->getServer()->getCommandMap()->register("email-set", new EmailCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EVListener($this), $this);
    }

    /**
     * @return SavedTokens
     */
    public function savedTokens() : SavedTokens{
        return $this->savedTokens;
    }

    /**
     * @return SavedEmails
     */
    public function savedEmails() : SavedEmails{
        return $this->savedEmails;
    }

    /**
     * @return Sessions
     */
    public function sessions() : Sessions{
        return $this->sessions;
    }
}
?>