<?php
namespace xbeastmode\emailverify\files;
use xbeastmode\emailverify\EmailVerify;
class SavedEmails{

    const FOLDER_PATH = "/files/registered_emails/";

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
     * @param string $email
     */
    public function setEmail($email){
        file_put_contents($this->emailVerify->getDataFolder().self::FOLDER_PATH.strtolower($email), "");
    }

    /**
     * @param string $email
     */
    public function trashEmail($email){
        unlink($this->emailVerify->getDataFolder().self::FOLDER_PATH.strtolower($email));
    }

    /**
     * @param string $email
     * @return bool
     */
    public function emailExists($email){
        return file_exists($this->emailVerify->getDataFolder().self::FOLDER_PATH.strtolower($email));
    }
}