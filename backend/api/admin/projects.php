<?php

require_once dirname(__DIR__, 2) . '/utils/response.php';
set_cors_headers();

require_once dirname(__DIR__, 2) . '/utils/ensure_default_admin.php';
require_once dirname(__DIR__, 2) . '/middleware/auth.php';
require_once dirname(__DIR__, 2) . '/utils/data.php';

ensure_default_admin();

function sanitize_project(array $project): array
{
	return [
		'id' => (string)($project['id'] ?? ''),
		'project_name' => trim((string)($project['project_name'] ?? '')),
		'title' => trim((string)($project['title'] ?? '')),
		'description' => trim((string)($project['description'] ?? '')),
		'image_url' => trim((string)($project['image_url'] ?? '')),
		'created_at' => (string)($project['created_at'] ?? ''),
	];
}

try {
	require_auth();
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

	if ($method === 'GET') {
		$projects = read_json('projects');
		usort($projects, function (array $a, array $b): int {
			return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
		});
		send_json(array_map('sanitize_project', $projects));
	}

	if ($method === 'POST') {
		$body = json_decode(file_get_contents('php://input'), true);
		if (!is_array($body)) {
			send_error(400, 'Invalid request body.');
		}

		$projectName = trim((string)($body['project_name'] ?? ''));
		$title = trim((string)($body['title'] ?? ''));
		$description = trim((string)($body['description'] ?? ''));
		$imageUrl = trim((string)($body['image_url'] ?? ''));

		if ($projectName === '') {
			send_error(400, 'Field project_name is required.');
		}
		if ($title === '') {
			send_error(400, 'Field title is required.');
		}
		if ($description === '') {
			send_error(400, 'Field description is required.');
		}
		if ($imageUrl === '') {
			send_error(400, 'Field image_url is required.');
		}

		$projects = read_json('projects');
		$newProject = [
			'id' => uniqid('', true),
			'project_name' => $projectName,
			'title' => $title,
			'description' => $description,
			'image_url' => $imageUrl,
			'created_at' => date('c'),
		];

		$projects[] = $newProject;
		write_json('projects', $projects);
		send_json(sanitize_project($newProject), 201);
	}

	if ($method === 'PUT') {
		$id = trim((string)($_GET['id'] ?? ''));
		if ($id === '') {
			send_error(400, 'Field id is required.');
		}

		$body = json_decode(file_get_contents('php://input'), true);
		if (!is_array($body)) {
			send_error(400, 'Invalid request body.');
		}

		$projectName = trim((string)($body['project_name'] ?? ''));
		$title = trim((string)($body['title'] ?? ''));
		$description = trim((string)($body['description'] ?? ''));
		$imageUrl = trim((string)($body['image_url'] ?? ''));

		if ($projectName === '') {
			send_error(400, 'Field project_name is required.');
		}
		if ($title === '') {
			send_error(400, 'Field title is required.');
		}
		if ($description === '') {
			send_error(400, 'Field description is required.');
		}
		if ($imageUrl === '') {
			send_error(400, 'Field image_url is required.');
		}

		$projects = read_json('projects');
		$found = false;
		$updatedProject = [];

		foreach ($projects as &$project) {
			if ((string)($project['id'] ?? '') === $id) {
				$project['project_name'] = $projectName;
				$project['title'] = $title;
				$project['description'] = $description;
				$project['image_url'] = $imageUrl;
				$updatedProject = $project;
				$found = true;
				break;
			}
		}
		unset($project);

		if (!$found) {
			send_error(404, 'Project not found.');
		}

		write_json('projects', $projects);
		send_json(sanitize_project($updatedProject));
	}

	if ($method === 'DELETE') {
		$id = trim((string)($_GET['id'] ?? ''));
		if ($id === '') {
			send_error(400, 'Field id is required.');
		}

		$projects = read_json('projects');
		$found = false;
		$remaining = [];

		foreach ($projects as $project) {
			if ((string)($project['id'] ?? '') === $id) {
				$found = true;
				continue;
			}
			$remaining[] = $project;
		}

		if (!$found) {
			send_error(404, 'Project not found.');
		}

		write_json('projects', $remaining);
		send_json(['message' => 'Project deleted.']);
	}

	send_error(405, 'Method not allowed');
} catch (Throwable $e) {
	send_error(500, 'Server error');
}
