# Fluxa — Principios UI/UX

## Optimistic UI
Actualizar la interfaz **antes** de recibir confirmación del servidor, revertiendo si falla.
Aplica en Fluxa a: likes, bookmarks, follows, marcar notificaciones como leídas.

Patrón:
1. Actualizar DOM inmediatamente al click
2. Lanzar fetch al servidor
3. Si falla → revertir DOM + mostrar toast de error

```js
btn.addEventListener('click', () => {
    const liked = btn.classList.toggle('liked');
    counter.textContent = parseInt(counter.textContent) + (liked ? 1 : -1);

    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token } })
        .catch(() => {
            btn.classList.toggle('liked');
            counter.textContent = parseInt(counter.textContent) + (liked ? -1 : 1);
            showToast('No se pudo completar la acción', 'error');
        });
});
```

---

## Loading UI
Toda interacción que requiera servidor debe mostrar feedback visual inmediato. Nunca dejar al usuario sin indicación de que algo está pasando.

### Estados de carga
| Componente | Tipo de carga | Implementación |
|-----------|--------------|----------------|
| Botones | Spinner inline + botón deshabilitado | `btn.disabled = true`, texto → texto + spinner SVG |
| Listas (proyectos, mensajes) | Skeleton screens | `div.skeleton` con clase CSS `@apply animate-pulse rounded bg-surface` |
| Perfiles | Skeleton header + tabs | 3-4 bloques `skeleton` que imitan la estructura final |
| Imágenes | Lazy load + placeholder | `<img loading="lazy">`, fondo `--bg-subtle` hasta que carga |
| Modal / dropdown remoto | Spinner centrado | Bouncy dots o spinner SVG dentro del contenedor |

### Skeleton pattern
```blade
{{-- Mientras carga, mostrar estructura idéntica al contenido real --}}
<div class="space-y-4">
    <div class="skeleton h-8 w-3/4 rounded"></div>
    <div class="skeleton h-4 w-full rounded"></div>
    <div class="skeleton h-4 w-5/6 rounded"></div>
    <div class="skeleton h-32 w-full rounded-lg"></div>
</div>
```

### Transiciones entre estados
- `opacity` + `transition` para fade entre carga y contenido
- No superponer skeleton + contenido — reemplazar limpiamente
- Si la carga toma <300ms, no mostrar skeleton (flash perceptual)
- Usar `setTimeout(300ms)` antes de mostrar skeleton para evitar flicker en respuestas rápidas

---

## Error UI
Los errores deben comunicarse con claridad, sin tecnicismos, y ofrecer una salida.

| Tipo | Mensaje | Acción |
|------|---------|--------|
| Red (timeout, offline) | "Parece que no tienes conexión. Revisa tu internet." | Botón "Reintentar" |
| Servidor (500) | "Algo salió mal. Estamos trabajando en ello." | Botón "Reintentar" |
| Validación (422) | Mensaje específico del campo (del FormRequest) | Resaltar campo + mensaje abajo del input |
| No autorizado (403) | "No tienes permiso para hacer esto." | — |
| No encontrado (404) | "Esto ya no existe o nunca existió." | Link al feed |
| Límite de plan | "Has alcanzado el límite de {X} proyectos en tu plan actual." | Link a planes |

### Toast de error
```js
function showToast(message, type = 'error') {
    // type: 'error' | 'success' | 'warning' | 'info'
    // Duración: error 6s, success 3s, warning 5s, info 3s
    // El toast se posiciona bottom-right, z-50
    // Animación: slide-in desde derecha, fade-out al cerrar
}
```

### Offline detection
- Escuchar `window.addEventListener('offline', handler)`
- Mostrar banner persistente: "Sin conexión. Los cambios se guardarán cuando vuelvas."
- Escuchar `online` para ocultar banner y recargar datos si es necesario

### Confirmaciones destructivas
Toda acción destructiva (eliminar proyecto, eliminar cuenta, eliminar comentario) requiere:
1. Modal de confirmación con el nombre del recurso
2. Botón de confirmación en rojo (`bg-red-600`)
3. Botón de cancelación
4. No confirmar con un solo click fuera del modal

```blade
<x-modal name="confirm-delete" title="Eliminar proyecto">
    <p class="text-ink-600">¿Estás seguro de eliminar <strong>"{{ $project->title }}"</strong>? Esta acción no se puede deshacer.</p>
    <div class="flex justify-end gap-3 mt-6">
        <button @click="show = false" class="btn-secondary">Cancelar</button>
        <button class="btn-danger" data-confirm-delete="{{ $project->id }}">Eliminar</button>
    </div>
</x-modal>
```

---

## Reactive UI
La interfaz reacciona a eventos del servidor en tiempo real sin recargar la página. En Fluxa esto va via **Laravel Reverb + Echo**.

Canales activos y qué actualizan:

| Canal | Evento | Qué actualiza en UI |
|-------|--------|---------------------|
| `private-conversation.{id}` | `NewMessage` | Añade mensaje al chat |
| `private-conversation.{id}` | `UserTyping` | Muestra "escribiendo..." |
| `private-user.{id}` | `NotificationCreated` | Badge + lista de notificaciones |
| `private-user.{id}` | `NotificationRead` | Quita badge |
| `private-user.{id}` | `FollowToggled` | Actualiza contador de seguidores |
| `private-user.{id}` | `ConversationCreated` | Abre nueva conversación |
| `private-user.{id}` | `PrivacyUpdated` | Refleja cambios de privacidad |

Reglas al implementar Reactive UI en Fluxa:
- Siempre escuchar en el canal correcto (privado con auth, no público)
- Usar `X-Socket-ID` header en fetch para evitar que el emisor reciba su propio evento (`.toOthers()`)
- El handler JS solo manipula DOM — nunca hace lógica de negocio
- Si Echo no está disponible (red lenta), la UI debe funcionar igualmente via polling o recarga manual

---

## Empty UI
Cuando una sección no tiene contenido, mostrar un estado vacío que explique qué hay ahí y cómo llenarlo. Nunca una página en blanco.

| Vista | Condición | Mensaje sugerido | CTA |
|-------|-----------|-----------------|-----|
| Feed | Sin follows | "Tu feed está vacío. Sigue desarrolladores para ver sus proyectos." | → Ir a Explore |
| Perfil / Proyectos | Sin proyectos propios | "Aún no has publicado ningún proyecto." | → Crear proyecto |
| Perfil / Proyectos (ajeno) | El usuario no tiene proyectos | "Este desarrollador aún no ha publicado proyectos." | — |
| Mensajes | Sin conversaciones | "No tienes conversaciones aún." | → Buscar usuario |
| Notificaciones | Sin notificaciones | "Todo al día. No tienes notificaciones nuevas." | — |
| Explore / búsqueda | Sin resultados | "No encontramos resultados para '…'." | → Limpiar filtros |
| Bookmarks | Sin guardados | "No has guardado ningún proyecto todavía." | → Ir a Explore |
| Endorsements | Sin endorsements | "Este proyecto aún no tiene reconocimientos." | — |
| Comentarios | Sin comentarios | "No hay comentarios todavía. Sé el primero en comentar." | → Enfocar input |

### Anatomía de un Empty UI
```blade
<div class="empty-state">
    <svg class="empty-state__icon">...</svg>
    <p class="empty-state__title">Tu feed está vacío</p>
    <p class="empty-state__desc">Sigue desarrolladores para ver sus proyectos aquí.</p>
    <a href="{{ route('explore.index') }}" class="btn-accent">Explorar proyectos</a>
</div>
```

Tokens: `--ink-300` para icono, `--ink-500` para texto, `--accent` para CTA.

---

## Forms UX
Los formularios son el medio principal de interacción. Deben sentirse rápidos, predecibles y tolerantes.

### Validación
- Validación inline en `blur` del campo (no en cada keystroke)
- El botón submit se deshabilita `disabled` mientras se envía
- Los errores del servidor (422) se mapean al campo correspondiente automáticamente
- No borrar campos rellenados al mostrar error de validación
- Los límites de caracteres se muestran como contador: `0/500`

### Submit
```js
form.addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = spinnerSVG + ' Guardando...';

    fetch(form.action, { method: 'POST', body: new FormData(form) })
        .then(handleResponse)
        .catch(handleError)
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
});
```

### Confirmación en formularios no-destructivos
- Formularios de configuración no requieren confirmación — mostrar toast "Guardado"
- Formularios con efectos visibles (avatar, portada) muestran preview antes de enviar

---

## Accesibilidad (a11y)

### Roles y ARIA
- Modales: `role="dialog"`, `aria-modal="true"`, primer foco en elemento interactivo, trampa de foco, cerrar con Escape
- Dropdowns: `role="menu"`, `aria-expanded`, navegación con flechas arriba/abajo
- Tabs: `role="tablist"`, `role="tab"`, `aria-selected`, `role="tabpanel"`
- Toasts: `role="alert"`, `aria-live="polite"`
- Notificaciones badge: `aria-label="3 notificaciones sin leer"`

### Navegación por teclado
- Toda acción clickeable debe ser alcanzable con Tab
- Modales: cerrar con Escape, Tab cíclico (focus trap)
- Dropdowns: Escape cierra, flechas navegan, Enter selecciona
- Skip to content: enlace invisible al inicio del layout

### Contraste y color
- Relación de contraste mínima: 4.5:1 para texto normal, 3:1 para texto grande
- No usar solo color para transmitir estado (ej. error: icono + texto + color)
- Dark mode hereda los mismos tokens de contraste

### Imágenes
- `<img alt="Descripción de la imagen">` — obligatorio en fotos de perfil, proyectos
- Avatar decorativo: `alt=""` (imagen puramente decorativa)
- Los SVG decorativos llevan `aria-hidden="true"`

---

## Responsive Design

### Breakpoints
| Tailwind | Min-width | Target |
|----------|-----------|--------|
| `sm` | 640px | Tablets pequeñas |
| `md` | 768px | Tablets |
| `lg` | 1024px | Desktop |
| `xl` | 1280px | Desktop grande |

### Mobile first
Todas las vistas se diseñan primero para mobile. Los breakpoints añaden layout progresivamente.

### Touch targets
- Mínimo 44x44px para elementos interactivos (buttons, links, icon buttons)
- Separación mínima de 8px entre elementos tappables para evitar errores de dedo

### Patrones responsive
- Sidebar navigation → bottom tab bar en mobile
- Tablas de datos → cards verticales en mobile
- Grid de proyectos: 1 col mobile, 2 col tablet, 3-4 col desktop
- Modales → fullscreen en mobile, centered dialog en desktop

---

## Animaciones y transiciones

### Principios
- **Rendimiento**: animar solo `transform` y `opacity` (compositor-friendly)
- **Duración**: 200-300ms (entrada), 150-200ms (salida)
- **Easing**: `ease-out` para entradas, `ease-in-out` para estados
- No animar `width`, `height`, `top`, `left`, `margin`, `padding` — causan layout thrash

### Cuándo animar
| Elemento | Animación | Notas |
|----------|-----------|-------|
| Modal/overlay | Fade in 200ms | Escala sutil (scale 0.95 → 1) |
| Toast | Slide in desde derecha 250ms | Slide out hacia derecha |
| Dropdown/menu | Fade + translateY(4px) 150ms | Sin scale (se ve raro en posiciones extremas) |
| Like/bookmark toggle | Scale bounce 300ms | `transform: scale(1 → 1.15 → 1)` |
| Notificación badge | Fade in + slide 200ms | — |
| Skeleton → contenido | Crossfade 200ms | Opacity no abrupta |
| Page transitions | No hay (Blade server-rendered) | Usar `@keyframes` en elementos individuales |

### Reduced motion
```css
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## Modales

### Convenciones
- Overlay oscuro (bg-black/50) cierra al hacer click fuera
- Escape cierra el modal (event listener en `keydown`)
- Focus trap: Tab cíclico dentro del modal, no sale hasta cerrar
- Primer foco: en el primer input o botón de acción
- Scroll: `overflow-y: auto` si el contenido excede viewport, pero `body` no scrolea detrás
- No anidar modales — si se necesita, reemplazar el contenido del actual

### Plantilla
```blade
<div x-data="{ show: false }" x-show="show" class="modal-overlay">
    <div class="modal-panel" @click.outside="show = false" @keydown.escape.window="show = false">
        <div class="modal-header">
            <h2>{{ $title }}</h2>
            <button @click="show = false" aria-label="Cerrar">
                <svg class="icon-close">...</svg>
            </button>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>
```

---

## Dropdowns y menús

### Convenciones
- Posicionamiento: abajo del trigger, alineado a la izquierda (o derecha si hay desborde)
- Cerrar al hacer click fuera, Escape, o click en una opción
- Flechas arriba/abajo navegan entre opciones (con highlight visual)
- Enter selecciona la opción resaltada
- No abrir automáticamente en hover (solo en click) — evitar menús que desaparecen al mover el mouse
- `aria-expanded` refleja estado del menú

---

## Notificaciones

### Jerarquía de feedback visual
1. **Toast**: acciones sin redirección (like, bookmark, follow, guardar settings) — 3-6s
2. **Badge**: notificaciones no leídas (topbar) — persistente hasta leer
3. **Banner**: estado global (offline, error crítico) — persistente hasta resolver
4. **Inline**: errores de validación — al lado del campo, hasta corregir

### Toast positioning
```css
.toast-container {
    @apply fixed bottom-4 right-4 z-50 flex flex-col gap-2;
}
```

---

## Estados de elementos interactivos

Todo elemento interactivo (button, link, input, textarea, select) debe tener:

| Estado | Implementación |
|--------|---------------|
| `:hover` | `opacity-90` o cambio sutil de fondo (150ms) |
| `:focus-visible` | `ring-2 ring-accent` (solo teclado, no click) |
| `:active` | `scale-[0.97]` en botones |
| `:disabled` | `opacity-50 cursor-not-allowed` |
| `loading` | Spinner inline + `disabled` |

Inputs y textareas:
- `border` color `--border` por defecto, `--accent` en focus, `--error` en error
- Placeholder: `--ink-400`
- Label: `--ink-700`, bold, arriba del input

---

## Colores y tipografía

### Paleta de tokens (de variables.css)
```css
--accent: #12b3b6;        /* Teal — CTA, links, active states */
--surface: #ffffff;       /* Fondos principales */
--bg: #f5f5f5;           /* Fondos secundarios */
--bg-subtle: #eaeaea;    /* Fondos terciarios / hover */
--ink-900: #171717;      /* Texto principal */
--ink-700: #404040;      /* Texto secundario */
--ink-500: #737373;      /* Texto terciario / metadata */
--ink-300: #a3a3a3;      /* Placeholder / disabled */
--ink-50:  #fafafa;      /* Texto sobre fondo oscuro */
--border: #e5e5e5;       /* Bordes suaves */
--border-strong: #d4d4d4; /* Bordes más marcados */
```

### Dark mode
Modo oscuro duplica los tokens de superficie: `--surface` → tonos grises oscuros, `--ink-*` → invertidos. El acento se mantiene.

### Tipografía
- Fuente principal: `font-sans` (Figtree de Tailwind)
- Fuente monospace: `font-mono` (JetBrains Mono) para código
- Headings: `font-semibold`, `tracking-tight`
- Body: `font-normal`, `leading-relaxed`
- Metadata (fechas, tags): `text-sm text-ink-500`
- Links inline: subrayado solo en hover, color `--accent`

---

## Spacing system
Usar escala de Tailwind consistente. Preferir espaciado basado en la naturaleza del contenido:

| Contexto | Gap/Stack |
|----------|-----------|
| Entre secciones de página | `space-y-8` |
| Entre cards en grid | `gap-4` o `gap-6` |
| Entre items de una lista | `space-y-3` |
| Dentro de una card (título → contenido) | `space-y-2` |
| Elementos inline (icono + texto) | `gap-2` |
| Padding de card | `p-4` (mobile) / `p-6` (desktop) |
| Padding de página | `px-4 py-6` (mobile) / `px-8 py-8` (desktop) |
