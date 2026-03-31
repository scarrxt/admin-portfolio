<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/middleware/auth.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';

ensure_default_admin();

try {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'PUT';
	if ($method !== 'PUT') {
		send_error(405, 'Method not allowed');
	}

	require_auth();

	$body = json_decode(file_get_contents('php://input'), true);
	if (!is_array($body)) {
		send_error(400, 'Invalid request body.');
	}

	$username = trim((string)($body['username'] ?? ''));
	$email = trim((string)($body['email'] ?? ''));
	$profileImage = trim((string)($body['profile_image'] ?? ''));

	if ($username === '') {
		send_error(400, 'Field username is required.');
	}
	if ($email === '') {
		send_error(400, 'Field email is required.');
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		send_error(400, 'Field email must be a valid email address.');
	}

	$admin = read_json('admin');
	$admin['username'] = $username;
	$admin['email'] = $email;
	$admin['profile_image'] = $profileImage;
	write_json('admin', $admin);

	send_json([
		'id' => (string)($admin['id'] ?? '1'),
		'username' => $username,
		'email' => $email,
		'profile_image' => $profileImage,
	]);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
