<?php
namespace xbeastmode\emailverify\tasks;
use pocketmine\scheduler\PluginTask;
use xbeastmode\emailverify\EmailVerify;
class ExpiredTokenTask extends PluginTask{

    /** @var EmailVerify */
    private $emailVerify;

    public function __construct(EmailVerify $emailVerify){
        parent::__construct($emailVerify);
        $this->emailVerify = $emailVerify;
    }
    /**
     *
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun($currentTick){
        $this->emailVerify->savedTokens()->deleteExpiredFiles();
    }
}