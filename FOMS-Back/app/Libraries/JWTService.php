<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private $key = 'FOMS-Back'; // nama project

    public function generateToken($data)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // expired 1 hari
            'data' => $data
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function decodeToken($token)
    {
        return JWT::decode($token, new Key($this->key, 'HS256'));
    }
}
