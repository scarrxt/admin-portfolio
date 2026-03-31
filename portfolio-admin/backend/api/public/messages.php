<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';

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

	$name = trim((string)($body['name'] ?? ''));
	$email = trim((string)($body['email'] ?? ''));
	$message = trim((string)($body['message'] ?? ''));

	if ($name === '') {
		send_error(400, 'Field name is required.');
	}
	if ($email === '') {
		send_error(400, 'Field email is required.');
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		send_error(400, 'Field email must be a valid email address.');
	}
	if ($message === '') {
		send_error(400, 'Field message is required.');
	}

	$messages = read_json('messages');
	$messages[] = [
		'id' => uniqid('', true),
		'name' => $name,
		'email' => $email,
		'message' => $message,
		'created_at' => date('c'),
	];

	write_json('messages', $messages);

	send_json(['message' => 'Message received.'], 201);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
