(function () {
    const link = document.getElementById('resend-link');
    if (!link) return;

    link.addEventListener('click', function (e) {
        e.preventDefault();

        const route = link.getAttribute('data-route');
        const csrf = link.getAttribute('data-csrf');
        const emailInput = document.getElementById('email');
        const email = emailInput ? emailInput.value : '';

        link.textContent = 'Enviando...';
        link.style.pointerEvents = 'none';

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = route;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrf;

        const emailHidden = document.createElement('input');
        emailHidden.type = 'hidden';
        emailHidden.name = 'email';
        emailHidden.value = email;

        form.appendChild(tokenInput);
        form.appendChild(emailHidden);
        document.body.appendChild(form);
        form.submit();
    });
})();
