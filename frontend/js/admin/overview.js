window.addEventListener('DOMContentLoaded', async () => {
	const loading = document.getElementById('overviewLoading');
	const errorNode = document.getElementById('overviewError');
	const stats = document.getElementById('overviewStats');

	await initLayout();

	try {
		const [projectsResult, messagesResult] = await Promise.all([
			apiFetch('/admin/projects.php'),
			apiFetch('/admin/messages.php'),
		]);

		if (!projectsResult.ok || !messagesResult.ok) {
			throw new Error('Failed to load overview data.');
		}

		document.getElementById('totalProjects').textContent = String(projectsResult.data.length || 0);
		document.getElementById('totalMessages').textContent = String(messagesResult.data.length || 0);
		stats.classList.remove('hidden');
	} catch (error) {
		errorNode.textContent = error.message;
		errorNode.classList.remove('hidden');
	} finally {
		loading.classList.add('hidden');
	}
});
