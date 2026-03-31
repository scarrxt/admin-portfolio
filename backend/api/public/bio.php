<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';

ensure_default_admin();

try {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

	if ($method !== 'GET') {
		send_error(405, 'Method not allowed');
	}

	$bio = read_json('bio');
	send_json([
		'content' => trim((string)($bio['content'] ?? '')),
		'updated_at' => (string)($bio['updated_at'] ?? ''),
	]);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
