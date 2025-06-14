<?php
namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthHelpers {
    public static function getUserFromToken($request) {
        $key = getenv('JWT_SECRET_KEY');
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = explode(' ', $authHeader)[1];
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }
}

?>
