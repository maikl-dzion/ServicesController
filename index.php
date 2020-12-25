<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Jwt\JwtAuthController;
use App\Services\Logger\Logger;
use App\Services\Ftp\FtpService;
use App\Services\Ftp\FtpController;

use App\Services\Logger\LoggerInterface;
use App\Services\Jwt\JwtAuthInterface;

////////////////////////////////////////
/// -----------------------------------
// Пример использования Jwt авторизации
$data  = [
    'id' => '234',
    'username' => 'Maikl',
    'email' => 'dzr@mail.ru'
];

$jwt = new JwtAuthController();
$token  = $jwt->encode($data);  // Создаем токен
$verify = $jwt->decode($token); // Проверям токен

// lg(['token'  => $token, 'verify' => $verify,]);
////////////////////////////////////////


////////////////////////////////////////
/// ----------------------------------
// Пример использования класса Logger
$path    = __DIR__ . '/log';
$logger  = new Logger($path);

$title = 'Error Logger';

$data  = [
    'id'       => '234',
    'username' => 'Maikl',
    'email'    => 'dzr@mail.ru',
];

$result = $logger->log($data, $title);
$log = $logger->read();
// lg($log);
////////////////////////////////////////



////////////////////////////////////////
///  Ftp Controller использование

$host = 'bolderp5.beget.tech';
$user = 'bolderp5_ftp';
$password = '1985list';


$ftp = new FtpService($host, $user, $password);
lg([
    'list' => $ftp->getDirList(),
    // 'file_load' => $ftp->getFile('/test.html', 'f.html'),
    'ftp' => $ftp,
]);

// set SFTP object, use host, username and password
//$ftp = new FtpController($host, $user, $password);
//$localDir  = __DIR__ . '/dir';
//$remoteDir = '/iac-dashboard/ftp-test';
//$ftpClient = new \App\Services\Ftp\FtpMultiFileLoader($ftp);
//$ftpClient->remoteLoader($localDir, $remoteDir);


///////////////////////////////////////



////////////////////////////////////////
/// -----------------------------------
// Используем сервисные контроллеры в классе User

$user = new UserController($logger, $jwt);

lg($user);


class UserController
{
    private $jwt;
    private $logger;

    public function __construct(LoggerInterface $logger, JwtAuthInterface $jwt)
    {
        $this->jwt = $jwt;
        $this->logger = $logger;
    }
}


function lg($data) {
    echo "\n";
    echo "<pre>" . print_r($data, true) . "</pre>";
    echo "\n";
}

