<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Myth\Auth\Config\Services as AuthServices;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = AuthServices::authentication();
        $authorize = AuthServices::authorization();

        // Cek login
        if (! $auth->check()) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please log in.',
                ])
                ->setStatusCode(401);
        }

        // Cek role
        if (! empty($arguments)) {
            foreach ($arguments as $group) {
                if ($authorize->inGroup($group, $auth->id())) {
                    return; // authorized, lanjut
                }
            }
        }

        // Kalau role tidak sesuai
        return service('response')
            ->setJSON([
                'status' => 'error',
                'message' => 'Access denied. You do not have permission.',
            ])
            ->setStatusCode(403);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // kosongin aja kalau gak perlu
    }
}
