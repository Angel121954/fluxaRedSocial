/**
 * Manejo del onboarding de sugerencias
 */

function toggleFollow(btn, userId) {
    const input = document.getElementById('follow_' + userId);
    const isFollowing = btn.classList.contains('following');

    if (isFollowing) {
        btn.classList.remove('following');
        btn.textContent = 'Seguir';
        input.disabled = true;
        input.value = '';
    } else {
        btn.classList.add('following');
        btn.textContent = 'Siguiendo ✓';
        input.disabled = false;
        input.value = userId;
    }
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
