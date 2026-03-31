function guardRoute() {
	if (!getToken()) {
		window.location.href = '/admin/login.html';
	}
}

async function handleLogin(email, password) {
	const result = await apiFetch('/admin/login.php', {
		method: 'POST',
		body: JSON.stringify({ email, password }),
	});

	if (!result.ok) {
		return result.data && result.data.message ? result.data.message : 'Login failed.';
	}

	setToken(result.data.token);
	window.location.href = '/admin/index.html';
	return null;
}

function handleLogout() {
	clearToken();
	window.location.href = '/admin/login.html';
}
