async function initLayout() {
	guardRoute();

	const usernameNodes = document.querySelectorAll('[data-admin-username]');
	const imageNodes = document.querySelectorAll('[data-admin-image]');
	const layoutError = document.getElementById('layoutError');

	const result = await apiFetch('/admin/me.php');
	if (!result.ok) {
		if (layoutError) {
			layoutError.textContent = (result.data && result.data.message) || 'Failed to load admin profile.';
			layoutError.classList.remove('hidden');
		}
		return null;
	}

	const admin = result.data;
	usernameNodes.forEach((node) => {
		node.textContent = admin.username || 'Admin';
	});

	const fallbackImage = 'https://via.placeholder.com/80?text=Admin';
	imageNodes.forEach((node) => {
		node.src = admin.profile_image || fallbackImage;
		node.alt = admin.username || 'Admin';
	});

	const path = window.location.pathname;
	document.querySelectorAll('[data-nav-link]').forEach((link) => {
		const href = link.getAttribute('href') || '';
		if (path.endsWith(href)) {
			link.classList.add('active');
		}
	});

	const logoutButton = document.getElementById('logoutBtn');
	if (logoutButton) {
		logoutButton.addEventListener('click', handleLogout);
	}

	return admin;
}
