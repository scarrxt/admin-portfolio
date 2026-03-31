<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';
require_once dirname(__DIR__, 2) . '/utils/jwt.php';

ensure_default_admin();

try {
	$method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
	if ($method !== 'POST') {
		send_error(405, 'Method not allowed');
	}

	$body = json_decode(file_get_contents('php://input'), true);
	if (!is_array($body)) {
		send_error(400, 'Invalid request body.');
	}

	$email = trim((string)($body['email'] ?? ''));
	$password = trim((string)($body['password'] ?? ''));

	if ($email === '') {
		send_error(400, 'Field email is required.');
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		send_error(400, 'Field email must be a valid email address.');
	}
	if ($password === '') {
		send_error(400, 'Field password is required.');
	}

	$admin = read_json('admin');
	$now = time();

	$rate = $admin['login_rate_limit'] ?? [
		'attempts' => 0,
		'first_attempt_at' => 0,
		'lock_until' => 0,
	];

	$attempts = (int)($rate['attempts'] ?? 0);
	$firstAttempt = (int)($rate['first_attempt_at'] ?? 0);
	$lockUntil = (int)($rate['lock_until'] ?? 0);

	if ($lockUntil > $now) {
		send_error(429, 'Too many login attempts. Try again later.');
	}

	if ($firstAttempt > 0 && ($now - $firstAttempt) > 300) {
		$attempts = 0;
		$firstAttempt = 0;
	}

	$validEmail = hash_equals((string)($admin['email'] ?? ''), $email);
	$validPassword = password_verify($password, (string)($admin['password_hash'] ?? ''));

	if (!$validEmail || !$validPassword) {
		if ($firstAttempt === 0) {
			$firstAttempt = $now;
		}
		$attempts++;

		if ($attempts >= 5) {
			$admin['login_rate_limit'] = [
				'attempts' => 0,
				'first_attempt_at' => 0,
				'lock_until' => $now + 300,
			];
		} else {
			$admin['login_rate_limit'] = [
				'attempts' => $attempts,
				'first_attempt_at' => $firstAttempt,
				'lock_until' => 0,
			];
		}

		write_json('admin', $admin);
		send_error(401, 'Invalid credentials.');
	}

	$admin['login_rate_limit'] = [
		'attempts' => 0,
		'first_attempt_at' => 0,
		'lock_until' => 0,
	];
	write_json('admin', $admin);

	$token = jwt_encode([
		'sub' => (string)($admin['id'] ?? '1'),
		'email' => (string)($admin['email'] ?? ''),
		'iat' => $now,
		'exp' => $now + 86400,
	]);

	send_json([
		'token' => $token,
		'admin' => [
			'id' => (string)($admin['id'] ?? '1'),
			'username' => trim((string)($admin['username'] ?? '')),
			'email' => trim((string)($admin['email'] ?? '')),
			'profile_image' => trim((string)($admin['profile_image'] ?? '')),
		],
	]);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
