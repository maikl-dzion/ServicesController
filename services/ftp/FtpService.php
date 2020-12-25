<?php


namespace App\Services\Ftp;


class FtpService
{

    private $ftp;
    private $user;
    private $host;
    private $pass;

    private $currentDir = '/';
    private $ds = DIRECTORY_SEPARATOR;

    public $status;
    public $error;

    function __construct($host, $user, $password, $conn = true){

        $this->host = $host;
        $this->user = $user;
        $this->pass = $password;
        $this->status = 'Ready';

        if($conn)
            $this->connect();
    }


    public function connect() {

        if (!isset($this->ftp)){

            $this->ftp = ftp_connect($this->host, 21, 3) or die ("Cannot connect to host");
            if(!$this->ftp) {
                $this->error = 'Не удалось подключиться к серверу';
                return false;
            }

            ftp_login($this->ftp, $this->user, $this->pass) or die("Cannot login, wrong username or password");
            ftp_pasv($this->ftp, true);
            $this->status = 'Connected';
            return true;
        }

        return true;
    }

    public function getDirList($dir ='/'){
        $list = ftp_nlist($this->ftp, $dir);
        $this->setCurrentDir($dir);
        return $list;
    }

    public function setCurrentDir($dir) {
        $this->currentDir = $dir;
    }

    public function getFile($remoteFile, $localFile) {
        if(ftp_get($this->ftp, $localFile, $remoteFile,  FTP_BINARY)) {
            $this->status = 'Download complete';
            return true;
        }
        $this->error = 'Не удалось скачать файл';
        return false;
    }

    public function putFile($localFile, $remoteFile){
        if(ftp_put($this->ftp, $remoteFile, $localFile, FTP_BINARY)) {
            $this->status = 'Upload complete';
            return true;
        }
        $this->error = 'Не удалось загрузить файл на сервер';
        return false;
    }

    public function deleteFile($remoteFile){

        if (ftp_delete($this->ftp, $remoteFile)) {
            $this->status = "$remoteFile deleted successfully";
            return true;
        }
        $this->error = 'Не удалось загрузить файл на сервер';
        return false;
    }

    public function createDir($dir) {
        if (ftp_mkdir($this->ftp, $dir)) {
            $this->status = "Successfully created $dir";
            return true;
        }
        $this->error = "Не удалось создать директорию - {$dir}";
        return false;
    }

    public function deleteDir($dir){
        if (ftp_rmdir($this->ftp, $dir)) {
            $this->status = "Successfully deleted $dir";
            return true;
        }

        $this->error = "Не удалось удалить папку - {$dir}";
        return false;
    }


    private function close(){
        ftp_close($this->ftp);
    }

    public function __destruct(){
        if(isset($this->ftp))
            $this->close();
    }

}