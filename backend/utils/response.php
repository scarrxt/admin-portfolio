<?php

require_once dirname(__DIR__) . '/config/config.php';

function send_json($data, int $status = 200): void
{
	http_response_code($status);
	header('Content-Type: application/json');
	echo json_encode($data, JSON_UNESCAPED_SLASHES);
	exit;
}

function send_error(int $status, string $message): void
{
	send_json(['message' => $message], $status);
}

function set_cors_headers(): void
{
	header('Access-Control-Allow-Origin: ' . FRONTEND_URL);
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: Content-Type, Authorization');

	if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
		http_response_code(200);
		exit;
	}
}
