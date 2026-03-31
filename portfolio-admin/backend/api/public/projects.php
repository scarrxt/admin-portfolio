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

	$projects = read_json('projects');
	usort($projects, function (array $a, array $b): int {
		return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
	});

	$response = array_map(function (array $item): array {
		return [
			'id' => (string)($item['id'] ?? ''),
			'project_name' => trim((string)($item['project_name'] ?? '')),
			'title' => trim((string)($item['title'] ?? '')),
			'description' => trim((string)($item['description'] ?? '')),
			'image_url' => trim((string)($item['image_url'] ?? '')),
			'created_at' => (string)($item['created_at'] ?? ''),
		];
	}, $projects);

	send_json($response);
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
