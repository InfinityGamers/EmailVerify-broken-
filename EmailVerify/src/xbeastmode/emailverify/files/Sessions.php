<?php
namespace xbeastmode\emailverify\files;
use pocketmine\Player;
class Sessions{

    /** @var Player[] */
    protected $sessions = [];

    /**
     * var dumps sessions
     */
    public function dumpSessions(){
        var_dump($this->sessions);
    }

    /**
     * @param bool|false $confirm
     */
    public function clearSessions($confirm = false){
        if(!$confirm) return;
        unset($this->sessions);
    }

    /**
     * @param Player $p
     */
    public function removeSession(Player $p){
        unset($this->sessions[$p->getName()]);

    }

    /**
     * @param Player $p
     * @return bool
     */
    public function sessionExists(Player $p){
        return isset($this->sessions[$p->getName()]);
    }

    /**
     * @param Player $p
     */
    public function addSession(Player $p){
        $this->sessions[$p->getName()] = $p;
    }
}