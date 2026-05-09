# Fluxa — Principios UI/UX

## Optimistic UI
Actualizar la interfaz **antes** de recibir confirmación del servidor, revertiendo si falla.
Aplica en Fluxa a: likes, bookmarks, follows, marcar notificaciones como leídas.

Patrón:
1. Actualizar DOM inmediatamente al click
2. Lanzar fetch al servidor
3. Si falla → revertir DOM + mostrar toast de error

```js
// Ejemplo: like de proyecto
btn.addEventListener('click', () => {
    // 1. Actualizar UI inmediatamente
    const liked = btn.classList.toggle('liked');
    counter.textContent = parseInt(counter.textContent) + (liked ? 1 : -1);

    // 2. Confirmar con servidor
    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token } })
        .catch(() => {
            // 3. Revertir si falla
            btn.classList.toggle('liked');
            counter.textContent = parseInt(counter.textContent) + (liked ? -1 : 1);
            showToast('No se pudo registrar el like', 'error');
        });
});
```

---

## Ethical UX
Diseñar sin patrones manipuladores. Fluxa al ser una red para desarrolladores debe ser especialmente honesta.

Reglas concretas para Fluxa:
- No usar contadores de "X personas están viendo esto" inflados o falsos
- Las notificaciones push solo se piden cuando el usuario ya obtuvo valor de la plataforma (no al entrar por primera vez)
- No enviar notificaciones de reenganche falsas ("Alguien vio tu perfil" sin datos reales)
- Confirmaciones destructivas (eliminar cuenta, eliminar proyecto) deben ser explícitas — nunca un solo click
- No ocultar la opción de desactivar/eliminar cuenta detrás de flujos complicados
- Los límites del plan freemium deben mostrarse antes de que el usuario intente hacer algo, no después
- Dark mode y preferencias de notificación son de fácil acceso (ya implementado)
- No autoplay de contenido, no scroll infinito sin opción de pausar

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

Pantallas con Empty UI en Fluxa:

| Vista | Condición | Mensaje sugerido | CTA |
|-------|-----------|-----------------|-----|
| Feed | Sin follows | "Tu feed está vacío. Sigue desarrolladores para ver sus proyectos." | → Ir a Explore |
| Perfil / Proyectos | Sin proyectos propios | "Aún no has publicado ningún proyecto." | → Crear proyecto |
| Perfil / Proyectos (ajeno) | El usuario no tiene proyectos | "Este desarrollador aún no ha publicado proyectos." | — |
| Mensajes | Sin conversaciones | "No tienes conversaciones aún." | → Buscar usuario |
| Notificaciones | Sin notificaciones | "Todo al día. No tienes notificaciones nuevas." | — |
| Explore / búsqueda | Sin resultados | "No encontramos resultados para '…'." | → Limpiar filtros |
| Bookmarks | Sin guardados | "No has guardado ningún proyecto todavía." | → Ir a Explore |

Anatomía de un Empty UI en Fluxa:
```blade
<div class="empty-state">
    <!-- Icono SVG relacionado al contexto (Lucide style) -->
    <svg>...</svg>
    <!-- Título corto -->
    <p class="empty-state__title">Tu feed está vacío</p>
    <!-- Descripción opcional -->
    <p class="empty-state__desc">Sigue desarrolladores para ver sus proyectos aquí.</p>
    <!-- CTA solo si hay acción disponible -->
    <a href="{{ route('explore.index') }}" class="btn-accent">Explorar proyectos</a>
</div>
```

Tokens a usar: `--ink-300` para icono, `--ink-500` para texto, `--accent` para CTA.