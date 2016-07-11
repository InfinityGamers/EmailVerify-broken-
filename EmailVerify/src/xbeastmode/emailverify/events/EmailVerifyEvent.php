<?php
namespace xbeastmode\emailverify\events;
use pocketmine\event\plugin\PluginEvent;
use xbeastmode\emailverify\EmailVerify;
abstract class EmailVerifyEvent extends PluginEvent{
    public function __construct(EmailVerify $emailVerify){
        parent::__construct($emailVerify);
    }
}