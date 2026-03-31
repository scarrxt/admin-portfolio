<?php

error_reporting(0);
ini_set('display_errors', '0');

date_default_timezone_set('UTC');

$rootDir = dirname(__DIR__);
$envFile = $rootDir . DIRECTORY_SEPARATOR . '.env';

if (!is_readable($envFile)) {
	throw new RuntimeException('.env file is missing or unreadable.');
}

$envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];

foreach ($envLines as $line) {
	$trimmed = trim($line);
	if ($trimmed === '' || str_starts_with($trimmed, '#')) {
		continue;
	}

	$parts = explode('=', $trimmed, 2);
	$key = trim($parts[0] ?? '');
	$value = trim($parts[1] ?? '');

	if ($key !== '') {
		$env[$key] = $value;
	}
}

$constants = [
	'JWT_SECRET' => $env['JWT_SECRET'] ?? '',
	'ADMIN_USERNAME' => $env['ADMIN_USERNAME'] ?? 'Admin',
	'ADMIN_EMAIL' => $env['ADMIN_EMAIL'] ?? 'admin@example.com',
	'ADMIN_PASSWORD' => $env['ADMIN_PASSWORD'] ?? 'Admin@123',
	'ADMIN_PROFILE_IMAGE' => $env['ADMIN_PROFILE_IMAGE'] ?? '',
	'FRONTEND_URL' => $env['FRONTEND_URL'] ?? 'http://localhost:3000',
	'DATA_DIR' => $rootDir . DIRECTORY_SEPARATOR . 'data',
];

foreach ($constants as $name => $value) {
	if (!defined($name)) {
		define($name, $value);
	}
}
