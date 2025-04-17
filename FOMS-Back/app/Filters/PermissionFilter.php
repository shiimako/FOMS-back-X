<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Myth\Auth\Config\Services as AuthServices;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = AuthServices::authentication();
        $authorize = AuthServices::authorization();

        // Cek apakah user login
        if (! $auth->check()) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please log in.',
                ])
                ->setStatusCode(401);
        }

        if (empty($arguments)) {
            return;
        }

        // Cek setiap permission yang diminta
        foreach ($arguments as $permission) {
            if (! $authorize->hasPermission($permission, $auth->id())) {
                return service('response')
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Forbidden. You do not have the required permission.',
                    ])
                    ->setStatusCode(403);
            }
        }

        // Authorized, lanjut
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong karena nggak diperlukan untuk sekarang
    }
}
