async function loadBio() {
	const bioContent = document.getElementById('bioContent');
	const bioError = document.getElementById('bioError');

	try {
		const result = await apiFetch('/public/bio.php');
		if (!result.ok) {
			throw new Error((result.data && result.data.message) || 'Failed to load bio.');
		}
		bioContent.textContent = result.data.content || 'No bio available.';
		bioContent.classList.remove('loading');
	} catch (error) {
		bioContent.textContent = '';
		bioError.textContent = error.message;
		bioError.classList.remove('hidden');
	}
}

function renderProjects(projects) {
	const grid = document.getElementById('projectsGrid');
	grid.innerHTML = '';

	if (!projects.length) {
		grid.innerHTML = '<p class="loading">No projects yet.</p>';
		return;
	}

	projects.forEach((project) => {
		const card = document.createElement('article');
		card.className = 'project-card card';
		card.innerHTML = `
			<img class="project-thumb" src="${project.image_url}" alt="${project.title}">
			<div class="project-body">
				<div class="project-name">${project.project_name}</div>
				<h3 class="project-title">${project.title}</h3>
				<p class="project-desc">${project.description}</p>
			</div>
		`;
		grid.appendChild(card);
	});
}

async function loadProjects() {
	const state = document.getElementById('projectsState');
	const errorNode = document.getElementById('projectsError');

	try {
		const result = await apiFetch('/public/projects.php');
		if (!result.ok) {
			throw new Error((result.data && result.data.message) || 'Failed to load projects.');
		}
		state.textContent = '';
		renderProjects(Array.isArray(result.data) ? result.data : []);
	} catch (error) {
		state.textContent = '';
		errorNode.textContent = error.message;
		errorNode.classList.remove('hidden');
	}
}

function initContactForm() {
	const form = document.getElementById('contactForm');
	const successNode = document.getElementById('contactSuccess');
	const errorNode = document.getElementById('contactError');

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		successNode.classList.add('hidden');
		errorNode.classList.add('hidden');

		const payload = {
			name: form.name.value.trim(),
			email: form.email.value.trim(),
			message: form.message.value.trim(),
		};

		const result = await apiFetch('/public/messages.php', {
			method: 'POST',
			body: JSON.stringify(payload),
		}).catch((error) => ({ ok: false, data: { message: error.message } }));

		if (result.ok && result.status === 201) {
			successNode.textContent = result.data.message || 'Message received.';
			successNode.classList.remove('hidden');
			form.reset();
			return;
		}

		errorNode.textContent = (result.data && result.data.message) || 'Failed to send message.';
		errorNode.classList.remove('hidden');
	});
}

window.addEventListener('DOMContentLoaded', () => {
	loadBio();
	loadProjects();
	initContactForm();
});
