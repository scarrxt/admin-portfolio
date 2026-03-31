<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/middleware/auth.php';

ensure_default_admin();

try {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
	if ($method !== 'GET') {
		send_error(405, 'Method not allowed');
	}

	$admin = require_auth();

	send_json([
		'id' => (string)($admin['id'] ?? '1'),
		'username' => trim((string)($admin['username'] ?? '')),
		'email' => trim((string)($admin['email'] ?? '')),
		'profile_image' => trim((string)($admin['profile_image'] ?? '')),
	]);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
