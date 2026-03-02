document.addEventListener("DOMContentLoaded", () => {
    // Desactivar
    document
        .getElementById("formDeactivate")
        .addEventListener("submit", async (e) => {
            e.preventDefault();
            const { isConfirmed } = await Swal.fire({
                title: "¿Desactivar cuenta?",
                text: "Tu perfil dejará de ser visible. Podrás reactivarla iniciando sesión.",
                icon: "warning",
                confirmButtonText: "Sí, desactivar",
                cancelButtonText: "Cancelar",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
            });
            if (isConfirmed) e.target.submit();
        });

    // Eliminar — doble confirmación
    document
        .getElementById("formDestroy")
        .addEventListener("submit", async (e) => {
            e.preventDefault();
            const btn = e.target;
            const username = btn.dataset.username;

            const first = await Swal.fire({
                title: "¿Eliminar cuenta?",
                text: "Se borrarán todos tus datos, proyectos y publicaciones.",
                icon: "warning",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
            });

            if (!first.isConfirmed) return;

            // Segunda confirmación escribiendo el username
            const { value } = await Swal.fire({
                title: "Confirmación final",
                text: `Escribe tu usuario para confirmar: ${username}`,
                input: "text",
                inputPlaceholder: "Tu usuario",
                confirmButtonText: "Eliminar definitivamente",
                cancelButtonText: "Cancelar",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                inputValidator: (value) => {
                    if (value !== username) {
                        return "El usuario no coincide";
                    }
                },
            });

            if (value) e.target.submit();
        });
});
