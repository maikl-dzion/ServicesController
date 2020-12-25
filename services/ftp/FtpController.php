<?php

namespace App\Services\Ftp;

class FtpController {

    private $_host;
    private $_port = 21;
    private $_pwd;
    private $_stream;
    private $_timeout = 90;
    private $_user;

    public $error;
    public $passive = false;
    public $ssl     = false;
    public $system_type;

    public function  __construct($host = null, $user = null, $password = null, $port = 21, $timeout = 90) {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pwd  = $password;
        $this->_port = (int)$port;
        $this->_timeout = (int)$timeout;
    }

    /** Подключение и авторизация */
    public function connect() {

        if(!$this->connectToServer()) // Подключаемся к сервере
            return false;

        if(!$this->ftpAuth()) // Производим аутентификацию пользователя
            return false;

        return true;
    }


    /** Производим аутентификацию пользователя */
    private function ftpAuth() {

        if(ftp_login($this->_stream, $this->_user, $this->_pwd)) { // connection successful
            ftp_pasv($this->_stream, (bool)$this->passive); // set passive mode
            $this->system_type = ftp_systype($this->_stream); // set system type
            return true;
        }

        $this->error = "Failed to connect to {$this->_host} (login failed)";
        return false;
    }

    /** Подключаемся к сервере */
    private function connectToServer() {

        if(!$this->ssl) {
            $this->_stream = ftp_connect($this->_host, $this->_port, $this->_timeout);
            if(!$this->_stream) {
                $this->error = "Failed to connect to {$this->_host}";
                return false;
            }
        } elseif(function_exists("ftp_ssl_connect")) {
            $this->_stream = ftp_ssl_connect($this->_host, $this->_port, $this->_timeout);
            if(!$this->_stream) {
                $this->error = "Failed to connect to {$this->_host} (SSL connection)";
                return false;
            }
        } else {
            $this->error = "Failed to connect to {$this->_host} (Invalid connection type)";
            return false;
        }

        return true;
    }

    /** Получить текущую директорию */
    public function currentDirName() {
        return $this->pwd();
    }

    /** Получить список файлов/каталогов */
    public function ls($directory = null) {
        $list = [];
        $directory = ($directory) ?? $this->pwd();
        $list = ftp_nlist($this->_stream, $directory);
        if(!empty($list))
            return $list;
        $this->error = "Failed to get directory list";
        return false;
    }


    public function  __destruct() {
        $this->close();
    }

    /** Сменить директорию */
    public function cd($directory = null) {
        if(ftp_chdir($this->_stream, $directory)) return true;
        $this->error = "Failed to change directory to \"{$directory}\"";
        return false;
    }

    /** Set file permissions */
    public function chmod($permissions = 0, $remote_file = null) {

        if(ftp_chmod($this->_stream, $permissions, $remote_file)) {
            return true;
        } else {
            $this->error = "Failed to set file permissions for \"{$remote_file}\"";
            return false;
        }
    }

    /** Close FTP connection */
    public function close() {
        if($this->_stream) {
            ftp_close($this->_stream);
            $this->_stream = false;
        }
    }

    /** Delete file on FTP server */
    public function delete($remote_file = null) {
        if(ftp_delete($this->_stream, $remote_file)) return true;
        $this->error = "Failed to delete file \"{$remote_file}\"";
        return false;
    }

    /** Скачать файл с сервера */
    public function get($remoteFile = null, $localFile = null, $mode = FTP_ASCII) {
        if(ftp_get($this->_stream, $localFile, $remoteFile, $mode)) return true;
        $this->error = "Failed to download file \"{$remoteFile}\"";
        return false;
    }


    /** Create directory on FTP server */
    public function mkdir($directory = null) {
        if(ftp_mkdir($this->_stream, $directory)) return true;
        $this->error = "Failed to create directory \"{$directory}\"";
        return false;
    }

    /** Upload file to server */
    public function put($local_file = null, $remote_file = null, $mode = FTP_ASCII) {
        if(ftp_put($this->_stream, $remote_file, $local_file, $mode)) {
            return true;
        } else {
            $this->error = "Failed to upload file \"{$local_file}\"";
            return false;
        }
    }

    /** Get current directory */
    public function pwd() {
        return ftp_pwd($this->_stream);
    }

    /** Rename file on FTP server */
    public function rename($old_name = null, $new_name = null) {
        if(ftp_rename($this->_stream, $old_name, $new_name)) {
            return true;
        } else {
            $this->error = "Failed to rename file \"{$old_name}\"";
            return false;
        }
    }

    /** Remove directory on FTP server */
    public function rmdir($directory = null) {
        if(ftp_rmdir($this->_stream, $directory)) return true;
        $this->error = "Failed to remove directory \"{$directory}\"";
        return false;
    }
}
