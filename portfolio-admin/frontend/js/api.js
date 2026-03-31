const API_BASE = 'http://localhost:5000/api';

function getToken() {
	return localStorage.getItem('auth_token');
}

function setToken(token) {
	localStorage.setItem('auth_token', token);
}

function clearToken() {
	localStorage.removeItem('auth_token');
}

async function apiFetch(endpoint, options = {}) {
	const headers = {
		'Content-Type': 'application/json',
		...(options.headers || {}),
	};

	const token = getToken();
	if (token) {
		headers.Authorization = `Bearer ${token}`;
	}

	try {
		const response = await fetch(`${API_BASE}${endpoint}`, {
			...options,
			headers,
		});

		if (response.status === 401) {
			clearToken();
			window.location.href = '/admin/login.html';
			throw new Error('Unauthorized');
		}

		const text = await response.text();
		const data = text ? JSON.parse(text) : null;
		return { ok: response.ok, status: response.status, data };
	} catch (error) {
		if (error.message === 'Unauthorized') {
			throw error;
		}
		throw new Error('Unable to connect to the server. Please try again.');
	}
}
