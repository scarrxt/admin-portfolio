<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/middleware/auth.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';

ensure_default_admin();

try {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
	if ($method !== 'GET') {
		send_error(405, 'Method not allowed');
	}

	require_auth();
	$messages = read_json('messages');

	usort($messages, function (array $a, array $b): int {
		return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
	});

	$response = array_map(function (array $item): array {
		return [
			'id' => (string)($item['id'] ?? ''),
			'name' => trim((string)($item['name'] ?? '')),
			'email' => trim((string)($item['email'] ?? '')),
			'message' => trim((string)($item['message'] ?? '')),
			'created_at' => (string)($item['created_at'] ?? ''),
		];
	}, $messages);

	send_json($response);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
