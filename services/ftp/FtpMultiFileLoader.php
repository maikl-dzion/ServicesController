<?php


namespace App\Services\Ftp;


class FtpMultiFileLoader
{

    protected $ftp;
    protected $remotePath;
    protected $remoteDirChecked = array();
    protected $localFiles = array();

    public function __construct($ftp) {
        $this->ftp = $ftp;
        if(!$this->ftp->connect()) {
            print "Connection failed: " . $this->ftp->error;
            return false;
        }

        print "Connection successful - OK ";
    }

    public function remoteLoader($localPath, $remotePath) {
        $this->remotePath = $remotePath;
        $this->generateFiles($localPath, '');
        $this->ftpPutRun();

        print_r(array($this->localFiles, $this->remoteDirChecked));
    }

    protected function generateFiles($dirname, $remoteDir) {
        $files = $this->scan($dirname);
        foreach ($files as $key => $file) {
            if($file == '.' || $file == '..') continue;
            $pathFile = $dirname .'/'.$file;
            if(is_dir($pathFile)) {
                $this->generateFiles($pathFile, $file);
            } else {
                // echo $this->remotePath . '/' . $remoteDir;
                $dirCheck = $this->ftp->cd($this->remotePath . '/' . $remoteDir);
                $this->remoteDirChecked[] = array($dirCheck, $this->remotePath . '/' . $remoteDir);

                $item = array(
                    'local'  => $pathFile,
                    'remote' => $this->remotePath . '/' . $remoteDir . '/' .$file
                );
                $this->localFiles[] = $item;
            }
        }
    }

    protected function scan($dirname) {
        $files = scandir($dirname);
        return $files;
    }

    protected function ftpPutRun() {

        $this->createDirectories();

        foreach ($this->localFiles as $key => $item) {

            $localFile  = $item['local'];
            $remoteFile = $item['remote'];

            if($this->ftp->put($localFile, $remoteFile)) {
                print "Filed uploaded";
            } else {
                print "<br />Upload failed: {$remoteFile} " . $this->ftp->error;
            }
        }
    }

    protected function createDirectories() {

        if(empty($this->remoteDirChecked))
            return true;

        foreach ($this->remoteDirChecked as $key => $item) {
            $state = $item[0];
            if($state) continue;

            $newDir = $item[1];
            $this->ftp->mkdir($newDir);
        }
    }

}