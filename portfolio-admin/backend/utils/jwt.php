<?php

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jwt_encode(array $payload): string
{
	return JWT::encode($payload, JWT_SECRET, 'HS256');
}

function jwt_decode(string $token): object
{
	return JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
}
