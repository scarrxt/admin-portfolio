function previewMessage(value, max = 80) {
	if (!value) return '';
	return value.length > max ? `${value.slice(0, max)}...` : value;
}

window.addEventListener('DOMContentLoaded', async () => {
	const loading = document.getElementById('inboxLoading');
	const errorNode = document.getElementById('inboxError');
	const body = document.getElementById('inboxTableBody');

	await initLayout();

	const result = await apiFetch('/admin/messages.php');
	loading.classList.add('hidden');

	if (!result.ok) {
		errorNode.textContent = (result.data && result.data.message) || 'Failed to load messages.';
		errorNode.classList.remove('hidden');
		return;
	}

	if (!result.data.length) {
		body.innerHTML = '<tr><td colspan="4" class="muted">No messages yet.</td></tr>';
		return;
	}

	body.innerHTML = result.data
		.map((item) => `
			<tr>
				<td>${item.name}</td>
				<td>${item.email}</td>
				<td>${previewMessage(item.message)}</td>
				<td>${item.created_at}</td>
			</tr>
		`)
		.join('');
});
