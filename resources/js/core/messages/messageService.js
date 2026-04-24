export async function sendMessage(body, convId, recipient) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const url = convId
        ? `/messages/${convId}`
        : `/messages/user/${recipient}`;

    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Socket-ID': window.Echo?.socketId() ?? '',
        },
        body: JSON.stringify({ body }),
    });

    if (!res.ok) throw new Error('Error al enviar el mensaje');

    return await res.json();
}

export async function searchUsers(query) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const res = await fetch(`/users/search?q=${encodeURIComponent(query)}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    if (!res.ok) throw new Error('Error en la búsqueda');

    return await res.json();
}