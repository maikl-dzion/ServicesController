<?php

namespace App\Services\Jwt;

interface  JwtAuthInterface {
    public function  encode(array $data = [], string $privateKey = '', string $alg  = 'sha256') : string; // create token
    public function  decode(string $token, string $secretKey = '', string $alg  = 'sha256') : bool; // verify token
}

