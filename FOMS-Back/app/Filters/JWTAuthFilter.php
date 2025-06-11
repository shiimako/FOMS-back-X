<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = "FOMS-Back";// atau hardcode dulu buat testing

        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return \Config\Services::response()
                ->setJSON(['message' => 'Token tidak ditemukan.'])
                ->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // Simpan user info ke request kalau kamu mau
            $request->userData = (array) $decoded->data;
        } catch (\Exception $e) {
            return \Config\Services::response()
                ->setJSON(['message' => 'Token tidak valid atau kadaluarsa.'])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak perlu dipakai
    }
}
