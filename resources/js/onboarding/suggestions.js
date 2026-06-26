import { showToast } from '../shared/toast.js';

function toggleFollow(btn, userId) {
    if (btn.dataset.loading) return;

    const input = document.getElementById('follow_' + userId);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const wasFollowing = btn.classList.contains('following');

    const nowFollowing = !wasFollowing;
    const label = nowFollowing ? 'Siguiendo \u2713' : 'Seguir';
    btn.classList.toggle('following', nowFollowing);
    btn.textContent = label;
    if (input) {
        input.disabled = !nowFollowing;
        input.value = nowFollowing ? userId : '';
    }

    btn.dataset.loading = 'true';
    btn.disabled = true;

    fetch(`/users/${userId}/follow`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
        .then(res => {
            if (!res.ok) throw new Error('Error al seguir');
            return res.json();
        })
        .then(data => {
            const following = data.following;
            btn.classList.toggle('following', following);
            btn.textContent = following ? 'Siguiendo \u2713' : 'Seguir';
            if (input) {
                input.disabled = !following;
                input.value = following ? userId : '';
            }
        })
        .catch(err => {
            console.error('[Follow]', err);
            btn.classList.toggle('following', wasFollowing);
            btn.textContent = wasFollowing ? 'Siguiendo \u2713' : 'Seguir';
            if (input) {
                input.disabled = !wasFollowing;
                input.value = wasFollowing ? userId : '';
            }
            showToast('No se pudo seguir al usuario', 'error');
        })
        .finally(() => {
            delete btn.dataset.loading;
            btn.disabled = false;
        });
}

async function skipOnboarding(event) {
    event.preventDefault();
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
        || document.querySelector('input[name="_token"]')?.value;
    const form = document.getElementById('suggestionsForm');
    
    try {
        const formData = new FormData(form);
        await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
            credentials: 'same-origin'
        });
    } catch (e) {
        console.error('Error:', e);
    }
    
    window.location.href = event.currentTarget.href;
}

window.toggleFollow = toggleFollow;
window.skipOnboarding = skipOnboarding;
