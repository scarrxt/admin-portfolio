<?php

require_once dirname(__DIR__) . '/config/config.php';

function read_json(string $filename): array
{
	$path = DATA_DIR . DIRECTORY_SEPARATOR . $filename . '.json';

	if (!is_readable($path)) {
		throw new RuntimeException('Data file is unreadable: ' . $filename);
	}

	$raw = file_get_contents($path);
	if ($raw === false) {
		throw new RuntimeException('Failed reading data file: ' . $filename);
	}

	$decoded = json_decode($raw, true);
	if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
		throw new RuntimeException('Invalid JSON in data file: ' . $filename);
	}

	return $decoded;
}

function write_json(string $filename, $data): void
{
	$path = DATA_DIR . DIRECTORY_SEPARATOR . $filename . '.json';
	$encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

	if ($encoded === false) {
		throw new RuntimeException('Failed to encode data: ' . $filename);
	}

	if (file_put_contents($path, $encoded . PHP_EOL, LOCK_EX) === false) {
		throw new RuntimeException('Failed writing data file: ' . $filename);
	}
}
