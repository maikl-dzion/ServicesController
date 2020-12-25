<?php

namespace App\Services\Jwt;

class JwtAuthController implements JwtAuthInterface {

    private $alg;

    public  function __construct($alg  = 'sha256') {
         $this->alg = $alg;
    }

    public function  encode(array $data = [], string $privateKey = '', string $alg  = 'sha256') :string {

         $secretKey  = $this->getSecretKey();
         $header     = $this->createHeader();
         $payload    = $this->createPayload($data);

         return $this->createToken($header, $payload, $secretKey, $alg);
    }

    public function decode(string $token, string $secretKey = '', string $alg  = 'sha256') : bool {

        if(!$token)
            return false;

        $data = explode('.', $token);

        if(empty($data[0]) ||
           empty($data[1]) ||
           empty($data[2]))  return false;

        $secretKey  = $this->getSecretKey();

        $header  = $data[0];
        $payload = $data[1];
        $userSign    = $data[2];

        $compareSign = $this->sign($header, $payload, $secretKey);

        return $userSign === $compareSign;
    }


    private function createPayload(array $data = array()) : array {

        $tokenId   = base64_encode(32);
        $issuedAt  = time();
        $notBefore = $issuedAt + 10; //добавляем 10 секунд
        $expire    = $notBefore + 60; // добавляем 60 секунд
        $serverName = $_SERVER['HTTP_HOST'];

        return [
            'jti'  => $tokenId,    // Json Token Id: уникальный идентификатор токена
            'iss'  => $serverName, // эммитент
            'iat'  => $issuedAt,   // время, когда был создан токен
            'nbf'  => $notBefore,  // не раньше
            'exp'  => $expire,     // время жизни
            'data' => array($data),  // данные, относящиеся к конкретному пользователю
        ];
    }

    private function sign(string $header, string $payload, string $secretKey, string $alg  = 'sha256') : string{
        $data = $header . '.' . $payload;
        $hash = hash_hmac($alg, $data, $secretKey);
        return rtrim(base64_encode($hash), '=');
    }

    private function createToken(array $header, array $payload, string $secretKey, string $alg) : string {
        $header64  = $this->base64Convert($header);
        $payload64  = $this->base64Convert($payload);
        $signature = $this->sign($header64, $payload64, $secretKey, $alg);
        return $header64 . '.'. $payload64 .'.'. $signature;
    }

    private function createHeader() : array {
        return ['alg' => 'HS256', 'typ' =>  'JWT'];
    }

    private function base64Convert(array $data) : string {
        return rtrim(base64_encode(json_encode($data)), '=');
    }

    private function  getSecretKey() : string {
        $secretKey = 'ghjfyHFDvfgdfhs67356vdfgdfdxHgGhKL7899';
        return  $secretKey;
    }
}
