function truncateText(value, max = 90) {
	if (!value) return '';
	return value.length > max ? `${value.slice(0, max)}...` : value;
}

function getProjectPayload(form) {
	return {
		project_name: form.project_name.value.trim(),
		title: form.title.value.trim(),
		description: form.description.value.trim(),
		image_url: form.image_url.value.trim(),
	};
}

function showProjectMessage(type, message) {
	const errorNode = document.getElementById('projectFormError');
	const successNode = document.getElementById('projectFormSuccess');
	errorNode.classList.add('hidden');
	successNode.classList.add('hidden');

	if (type === 'error') {
		errorNode.textContent = message;
		errorNode.classList.remove('hidden');
		return;
	}

	successNode.textContent = message;
	successNode.classList.remove('hidden');
}

function populateForm(project) {
	const form = document.getElementById('projectForm');
	document.getElementById('projectId').value = project.id;
	form.project_name.value = project.project_name;
	form.title.value = project.title;
	form.description.value = project.description;
	form.image_url.value = project.image_url;
	document.getElementById('projectSubmit').textContent = 'Update Project';
	form.classList.remove('hidden');
}

function resetForm() {
	const form = document.getElementById('projectForm');
	form.reset();
	document.getElementById('projectId').value = '';
	document.getElementById('projectSubmit').textContent = 'Save Project';
}

async function loadProjects() {
	const loading = document.getElementById('projectsLoading');
	const errorNode = document.getElementById('projectsError');
	const body = document.getElementById('projectsTableBody');

	loading.classList.remove('hidden');
	errorNode.classList.add('hidden');

	const result = await apiFetch('/admin/projects.php');
	loading.classList.add('hidden');

	if (!result.ok) {
		errorNode.textContent = (result.data && result.data.message) || 'Failed to load projects.';
		errorNode.classList.remove('hidden');
		return;
	}

	body.innerHTML = '';
	if (!result.data.length) {
		body.innerHTML = '<tr><td colspan="5" class="muted">No projects available.</td></tr>';
		return;
	}

	result.data.forEach((project) => {
		const tr = document.createElement('tr');
		tr.innerHTML = `
			<td><img class="project-thumb-small" src="${project.image_url}" alt="${project.title}"></td>
			<td>${project.project_name}</td>
			<td>${project.title}</td>
			<td>${truncateText(project.description)}</td>
			<td>
				<div class="inline-actions">
					<button type="button" class="ghost" data-edit-id="${project.id}">Edit</button>
					<button type="button" class="danger" data-delete-id="${project.id}">Delete</button>
				</div>
			</td>
		`;
		body.appendChild(tr);
	});

	body.querySelectorAll('[data-edit-id]').forEach((button) => {
		button.addEventListener('click', () => {
			const project = result.data.find((item) => item.id === button.dataset.editId);
			if (project) {
				populateForm(project);
			}
		});
	});

	body.querySelectorAll('[data-delete-id]').forEach((button) => {
		button.addEventListener('click', async () => {
			if (!window.confirm('Delete this project?')) {
				return;
			}
			const deleteResult = await apiFetch(`/admin/projects.php?id=${encodeURIComponent(button.dataset.deleteId)}`, {
				method: 'DELETE',
			});
			if (!deleteResult.ok) {
				showProjectMessage('error', (deleteResult.data && deleteResult.data.message) || 'Failed to delete project.');
				return;
			}
			showProjectMessage('success', 'Project deleted.');
			await loadProjects();
		});
	});
}

window.addEventListener('DOMContentLoaded', async () => {
	await initLayout();
	const form = document.getElementById('projectForm');
	const toggle = document.getElementById('toggleProjectForm');
	const cancel = document.getElementById('projectCancel');

	toggle.addEventListener('click', () => {
		form.classList.toggle('hidden');
	});

	cancel.addEventListener('click', () => {
		resetForm();
		form.classList.add('hidden');
	});

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		const id = document.getElementById('projectId').value;
		const payload = getProjectPayload(form);

		const endpoint = id ? `/admin/projects.php?id=${encodeURIComponent(id)}` : '/admin/projects.php';
		const method = id ? 'PUT' : 'POST';

		const result = await apiFetch(endpoint, {
			method,
			body: JSON.stringify(payload),
		});

		if (!result.ok) {
			showProjectMessage('error', (result.data && result.data.message) || 'Failed to save project.');
			return;
		}

		showProjectMessage('success', id ? 'Project updated.' : 'Project added.');
		resetForm();
		form.classList.add('hidden');
		await loadProjects();
	});

	await loadProjects();
});
