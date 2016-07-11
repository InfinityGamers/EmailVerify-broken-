<?php
namespace xbeastmode\emailverify\files;
use pocketmine\Player;
use xbeastmode\emailverify\EmailVerify;
use xbeastmode\emailverify\events\TokenCreateEvent;
use xbeastmode\emailverify\events\TokenExpireEvent;
class SavedTokens{

    const FOLDER_PATH = "/files/saved_tokens/";

    /** @var EmailVerify */
    private $emailVerify;

    public function __construct(EmailVerify $emailVerify){
        $this->emailVerify = $emailVerify;
    }

    /**
     * @return bool
     */
    public function folderExists(){
        return file_exists($this->emailVerify->getDataFolder().self::FOLDER_PATH);
    }

    /**
     * Creates token folder
     */
    public function makeFolder(){
        mkdir($this->emailVerify->getDataFolder().self::FOLDER_PATH);
    }

    /**
     * initiates folder
     */
    public function initFolder(){
        if(!$this->folderExists()){
            $this->makeFolder();
        }
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function tokenExists(Player $p){
        return file_exists($this->emailVerify->getDataFolder().self::FOLDER_PATH.strtolower($p->getName()).".token");
    }

    /**
     * @param Player $p
     * @param $token
     * @param $email
     */
    public function createToken(Player $p, $token, $email = ""){
        if (!$this->tokenExists($p)) {
            $tce = new TokenCreateEvent($this->emailVerify, $token);
            $this->emailVerify->getServer()->getPluginManager()->callEvent($tce);
            $contents = ["token" => $token, "email" => $email, "valid" => false];
            file_put_contents($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token", gzencode(serialize($contents)));
        }
    }

    /**
     * @param Player $p
     */
    public function trashToken(Player $p){
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            unlink($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token");
        }
    }

    /**
     * @param Player $p
     * @param $token
     * @return bool
     */
    public function validateToken(Player $p, $token){
        $isValid = false;
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            $dat = $this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token";
            $dat = unserialize(gzdecode(file_get_contents($dat)));
            if($dat["token"] === $token and $token === $dat["token"]) $isValid = true;
            $dat["valid"] = true;
            file_put_contents($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token", gzencode(serialize($dat)));
        }
        return $isValid;
    }

    /**
     * @param Player $p
     * @param string $email
     */
    public function setTokenEmail(Player $p, $email){
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            $dat = $this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token";
            $dat = unserialize(gzdecode(file_get_contents($dat)));
            $dat["email"] = $email;
            file_put_contents($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token", gzencode(serialize($dat)));
        }
    }

    /**
     * @param Player $p
     * @return null
     */
    public function getTokenEmail(Player $p){
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            $dat = $this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token";
            $dat = unserialize(gzdecode(file_get_contents($dat)));
            return $dat["email"];
        }
        return null;
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function hasAnEmail(Player $p){
        $hasEmail = (bool) null;
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            $dat = $this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token";
            $dat = unserialize(gzdecode(file_get_contents($dat)));
            $hasEmail = $dat["email"] !== "" ? true : false;
        }
        return $hasEmail;
    }

    /**
     * @param Player $p
     * @return bool|null
     */
    public function isTokenValid(Player $p){
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            $dat = $this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token";
            $dat = unserialize(gzdecode(file_get_contents($dat)));
            return $dat["valid"];
        }
        return null;
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function hasAToken(Player $p){
        return file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token");
    }

    /**
     * @param Player $p
     * @return string|null
     */
    public function getPlayerToken(Player $p){
        if(file_exists($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")) {
            $dat = unserialize(gzdecode(file_get_contents($this->emailVerify->getDataFolder() . self::FOLDER_PATH . strtolower($p->getName()) . ".token")));
            return $dat["token"];
        }
        return null;
    }

    /**
     * Deletes all expired files
     */
    public function deleteExpiredFiles(){
        $dir = $this->emailVerify->getDataFolder().self::FOLDER_PATH;
        foreach (glob($dir."*.token") as $file) {
            $uF = unserialize(gzdecode(file_get_contents($file)));
            $tee = new TokenExpireEvent($this->emailVerify, $uF["token"]);
            $this->emailVerify->getServer()->getPluginManager()->callEvent($tee);
            if (!$uF["valid"] and filemtime($file) < time() - 60*60*24*7) {
                unlink($file);
            }
        }
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function sendTokenMail(Player $p){
        $server = str_replace(" ", "_", $this->emailVerify->getConfig()->get("server_name"));
        $subject = "Your_token_for_server_".$server;
        $body = "-----------------------\nPlayer_name:_{$p->getName()}\nToken:_{$this->getPlayerToken($p)}\n-----------------------";
        $mail = file_get_contents("http://evmcpe.pe.hu/ev_send.php?to={$this->getTokenEmail($p)}&subject=$subject&body=$body");
        if($mail === "success"){
            return true;
        }
        return false;
    }
}