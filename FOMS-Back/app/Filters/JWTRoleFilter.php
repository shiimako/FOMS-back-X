<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET_KEY');// atau hardcode dulu buat testing
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return Services::response()->setStatusCode(401)->setJSON(['message' => 'Token tidak ditemukan.']);
        }

        $token = explode(' ', $authHeader)[1];

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $userRole = $decoded->data->role ?? '';

            // Cek argument dari route
            if ($arguments && !in_array($userRole, $arguments)) {
                return Services::response()->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
            }

        } catch (\Exception $e) {
            return Services::response()->setStatusCode(401)->setJSON(['message' => 'Token tidak valid.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing
    }
}
