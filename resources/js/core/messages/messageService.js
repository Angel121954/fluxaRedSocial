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
        credentials: 'same-origin',
        body: JSON.stringify({ body }),
    });

    if (!res.ok) {
        const errorData = await res.json();
        if (res.status === 403 && errorData.recipient_accepts_messages === false) {
            throw new Error('USER_NOT_ACCEPTING_MESSAGES');
        }
        throw new Error('Error al enviar el mensaje');
    }

    return await res.json();
}

export async function searchUsers(query) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const res = await fetch(`/users/search?q=${encodeURIComponent(query)}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
    });

    if (!res.ok) throw new Error('Error en la búsqueda');

    return await res.json();
}