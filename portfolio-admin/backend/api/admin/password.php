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

	$currentPassword = trim((string)($body['current_password'] ?? ''));
	$newPassword = trim((string)($body['new_password'] ?? ''));
	$confirmPassword = trim((string)($body['confirm_password'] ?? ''));

	if ($currentPassword === '') {
		send_error(400, 'Field current_password is required.');
	}
	if ($newPassword === '') {
		send_error(400, 'Field new_password is required.');
	}
	if ($confirmPassword === '') {
		send_error(400, 'Field confirm_password is required.');
	}
	if ($newPassword !== $confirmPassword) {
		send_error(400, 'New password and confirm password must match.');
	}

	$admin = read_json('admin');
	if (!password_verify($currentPassword, (string)($admin['password_hash'] ?? ''))) {
		send_error(400, 'Current password is incorrect.');
	}

	$admin['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
	write_json('admin', $admin);

	send_json(['message' => 'Password changed successfully.']);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
