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

function skipOnboarding() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
        || document.querySelector('input[name="_token"]')?.value;
    
    fetch(window._routes?.onboardingSave || '/onboarding/save-suggestions', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    });
}
