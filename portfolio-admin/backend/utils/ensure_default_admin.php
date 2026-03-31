<?php

require_once dirname(__DIR__) . '/utils/data.php';

function ensure_default_admin(): void
{
	$admin = read_json('admin');

	$admin['id'] = (string)($admin['id'] ?? '1');
	if (empty(trim((string)($admin['username'] ?? '')))) {
		$admin['username'] = ADMIN_USERNAME;
	}
	if (empty(trim((string)($admin['email'] ?? '')))) {
		$admin['email'] = ADMIN_EMAIL;
	}
	if (!isset($admin['profile_image']) || $admin['profile_image'] === null) {
		$admin['profile_image'] = ADMIN_PROFILE_IMAGE;
	}

	if (empty(trim((string)($admin['password_hash'] ?? '')))) {
		$admin['password_hash'] = password_hash(ADMIN_PASSWORD, PASSWORD_BCRYPT);
	}

	write_json('admin', $admin);
}
