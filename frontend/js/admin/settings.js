function setSectionMessage(nodeId, isError, text) {
	const node = document.getElementById(nodeId);
	node.className = isError ? 'error-msg' : 'success-msg';
	node.textContent = text;
}

async function loadSettingsData() {
	const [profileResult, bioResult] = await Promise.all([
		apiFetch('/admin/me.php'),
		apiFetch('/admin/bio.php'),
	]);

	if (profileResult.ok) {
		const form = document.getElementById('profileForm');
		form.username.value = profileResult.data.username || '';
		form.email.value = profileResult.data.email || '';
		form.profile_image.value = profileResult.data.profile_image || '';
	}

	if (bioResult.ok) {
		const form = document.getElementById('bioForm');
		form.content.value = bioResult.data.content || '';
	}
}

window.addEventListener('DOMContentLoaded', async () => {
	await initLayout();
	await loadSettingsData();

	const profileForm = document.getElementById('profileForm');
	const bioForm = document.getElementById('bioForm');
	const passwordForm = document.getElementById('passwordForm');
	const resetForm = document.getElementById('resetForm');

	profileForm.addEventListener('submit', async (event) => {
		event.preventDefault();
		const payload = {
			username: profileForm.username.value.trim(),
			email: profileForm.email.value.trim(),
			profile_image: profileForm.profile_image.value.trim(),
		};

		const result = await apiFetch('/admin/profile.php', {
			method: 'PUT',
			body: JSON.stringify(payload),
		});

		if (!result.ok) {
			setSectionMessage('profileMsg', true, (result.data && result.data.message) || 'Profile update failed.');
			return;
		}

		setSectionMessage('profileMsg', false, 'Profile updated successfully.');
	});

	bioForm.addEventListener('submit', async (event) => {
		event.preventDefault();
		const payload = { content: bioForm.content.value.trim() };

		const result = await apiFetch('/admin/bio.php', {
			method: 'PUT',
			body: JSON.stringify(payload),
		});

		if (!result.ok) {
			setSectionMessage('bioMsg', true, (result.data && result.data.message) || 'Bio update failed.');
			return;
		}

		setSectionMessage('bioMsg', false, 'Bio updated successfully.');
	});

	passwordForm.addEventListener('submit', async (event) => {
		event.preventDefault();
		const payload = {
			current_password: passwordForm.current_password.value.trim(),
			new_password: passwordForm.new_password.value.trim(),
			confirm_password: passwordForm.confirm_password.value.trim(),
		};

		const result = await apiFetch('/admin/password.php', {
			method: 'PUT',
			body: JSON.stringify(payload),
		});

		if (!result.ok) {
			setSectionMessage('passwordMsg', true, (result.data && result.data.message) || 'Password change failed.');
			return;
		}

		passwordForm.reset();
		setSectionMessage('passwordMsg', false, 'Password changed successfully.');
	});

	resetForm.addEventListener('submit', async (event) => {
		event.preventDefault();
		const payload = {
			new_password: resetForm.new_password.value.trim(),
			confirm_password: resetForm.confirm_password.value.trim(),
		};

		const result = await apiFetch('/admin/password_reset.php', {
			method: 'PUT',
			body: JSON.stringify(payload),
		});

		if (!result.ok) {
			setSectionMessage('resetMsg', true, (result.data && result.data.message) || 'Password reset failed.');
			return;
		}

		resetForm.reset();
		setSectionMessage('resetMsg', false, 'Password reset successfully.');
	});
});
