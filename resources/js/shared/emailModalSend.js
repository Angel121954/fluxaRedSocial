// Reenviar correo: hace submit del form oculto
function resend(e) {
    e.preventDefault();
    const link = document.getElementById("resend-link");
    link.textContent = "Enviando...";
    link.style.pointerEvents = "none";

    // Crea un form dinámico con el email guardado y lo submite
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "{{ route('password.email') }}";

    const token = document.createElement("input");
    token.type = "hidden";
    token.name = "_token";
    token.value = "{{ csrf_token() }}";

    const emailInput = document.createElement("input");
    emailInput.type = "hidden";
    emailInput.name = "email";
    emailInput.value = "{{ old('email') }}";

    form.appendChild(token);
    form.appendChild(emailInput);
    document.body.appendChild(form);
    form.submit();
}
