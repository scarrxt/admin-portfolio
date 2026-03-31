<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/middleware/auth.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';

ensure_default_admin();

try {
	require_auth();
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

	if ($method === 'GET') {
		$bio = read_json('bio');
		send_json([
			'content' => trim((string)($bio['content'] ?? '')),
			'updated_at' => (string)($bio['updated_at'] ?? ''),
		]);
	}

	if ($method === 'PUT') {
		$body = json_decode(file_get_contents('php://input'), true);
		if (!is_array($body)) {
			send_error(400, 'Invalid request body.');
		}

		$content = trim((string)($body['content'] ?? ''));
		if ($content === '') {
			send_error(400, 'Field content is required.');
		}

		$bio = [
			'content' => $content,
			'updated_at' => date('c'),
		];
		write_json('bio', $bio);
		send_json($bio);
	}

	send_error(405, 'Method not allowed');
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
