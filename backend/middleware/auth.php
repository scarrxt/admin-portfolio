<?php

require_once dirname(__DIR__) . '/utils/response.php';
require_once dirname(__DIR__) . '/utils/jwt.php';
require_once dirname(__DIR__) . '/utils/data.php';

function require_auth(): array
{
	$header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

	if ($header === '' && function_exists('apache_request_headers')) {
		$headers = apache_request_headers();
		$header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
	}

	if (!preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
		send_error(401, 'Unauthorized');
	}

	$token = trim($matches[1] ?? '');
	if ($token === '') {
		send_error(401, 'Unauthorized');
	}

	try {
		jwt_decode($token);
		return read_json('admin');
	} catch (Throwable $e) {
		send_error(401, 'Unauthorized');
	}
}
