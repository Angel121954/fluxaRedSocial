import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#12b3b6",
    },
});

// ── Prevenir retroceso tras cerrar sesión (Inertia) ──────────
// Cuando el usuario presiona "Atrás", verificamos si la página
// cacheada de Inertia corresponde a una ruta que requiere auth.
// Si el servidor devuelve un redirect al login (status 409 o redirect),
// forzamos la recarga completa para que Laravel aplique el middleware auth.
window.addEventListener("popstate", () => {
    // Fuerza una recarga completa de la página actual al navegar con el historial.
    // El servidor verá que no hay sesión y redirigirá al login.
    window.location.reload();
});
