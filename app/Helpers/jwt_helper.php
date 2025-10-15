<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('jwt_generate')) {
    function jwt_generate(array $data, int $expMinutes = 60): string
    {
        $issuedAt = time();
        $expiration = $issuedAt + ($expMinutes * 60); // Expiration time

        $payload = array_merge($data, [
            // 'iat' => $issuedAt,   // requested at
            'exp' => $expiration, // expires at
        ]);

        $secretKey = getenv('JWT_SECRET_KEY') ?: "2384329";

        return JWT::encode($payload, $secretKey, 'HS256');
    }
}

if (!function_exists('jwt_decode')) {
    function jwt_decode(string $jwt): ?array
    {
        try {
            $secretKey = getenv('JWT_SECRET_KEY') ?: "2384329";
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            log_message('error', '[JWT DECODE ERROR] ' . $e->getMessage());
            return null;
        }
    }
}
