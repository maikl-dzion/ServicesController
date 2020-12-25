# ServicesController

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Jwt\JwtAuthController;
use App\Services\Logger\Logger;

use App\Services\Logger\LoggerInterface;
use App\Services\Jwt\JwtAuthInterface;

#####
##### Пример использования Jwt авторизации

######
$data  = [
'id' => '234',
'username' => 'Maikl',
'email' => 'dzr@mail.ru'
];
######

###### $jwt = new JwtAuthController();
###### $token  = $jwt->encode($data);  // Создаем токен
###### $verify = $jwt->decode($token); // Проверям токен

lg(['token'  => $token, 'verify' => $verify,]);

#####

##### Пример использования класса Logger

$path    = __DIR__ . '/log';
###### $logger  = new Logger($path);

######
$title = 'Error Logger';
$data  = [
'id'       => '234',
'username' => 'Maikl',
'email'    => 'dzr@mail.ru',
];
######

###### $result = $logger->log($data, $title);
###### $log = $logger->read();

lg($log);