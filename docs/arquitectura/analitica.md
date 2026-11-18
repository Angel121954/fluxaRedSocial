# Analítica del embudo

Fluxa registra eventos de producto en `analytics_events` para medir el camino desde la primera visita hasta la activación. No usa cookies de terceros ni servicios externos.

## Tabla

| Columna | Uso |
|---|---|
| `user_id` | Usuario relacionado. `null` en eventos anónimos como una visita pública. |
| `name` | Nombre del evento en `snake_case`. |
| `properties` | Datos de contexto en JSON. No guardar datos personales. |
| `source` | Primer toque: `utm_source`, `ref` o host del referrer. |
| `medium` | `utm_medium` o `referral`. |
| `campaign` | `utm_campaign`. |
| `referrer` | Host externo de procedencia. |
| `path` | Ruta donde ocurrió el evento. |
| `created_at` | Momento del evento. |

## Eventos actuales

| Evento | Cuándo se dispara | Usuario |
|---|---|---|
| `page_viewed` | Landing, registro, perfil público, proyecto público o sueldos vistos por un invitado. | `null` |
| `register_started` | Un invitado abre el formulario de registro. | `null` |
| `user_registered` | Se crea una cuenta con email o red social. | Sí |
| `email_verified` | Se verifica el correo (evento `Verified` de Laravel). | Sí |
| `onboarding_completed` | Se guardan las sugerencias del onboarding. | Sí |
| `follow_created` | Un usuario empieza a seguir a otro. | Sí |
| `project_published` | Se crea un proyecto. | Sí |
| `comment_created` | Se comenta un proyecto. | Sí |
| `project_shared` | Se comparte un proyecto. | Sí |
| `session_started` | Primera petición autenticada del día. | Sí |
| `weekly_summary_sent` | Se encola el resumen semanal. | Sí |
| `email_unsubscribed` | Se da de baja el resumen semanal. | Sí |

## Embudo

1. `page_viewed`
2. `user_registered`
3. `email_verified`
4. `onboarding_completed`
5. Activación: usuario registrado que publica un proyecto o comenta dentro de los 7 días.

La métrica principal es **builders activos semanales**: usuarios únicos con `project_published`, `comment_created`, `follow_created` o `project_shared` en los últimos 7 días.

## Atribución

`CaptureAcquisitionSource` guarda el primer toque en la sesión:

- `utm_source`
- `utm_medium`
- `utm_campaign`
- `ref`
- host del `referer` externo

Ese primer toque se copia a todos los eventos de la sesión. Así se puede saber qué canal trae registros y, más importante, qué canal trae usuarios activados.

## Cómo añadir un evento

```php
use App\Services\AnalyticsService;

AnalyticsService::track('nombre_del_evento', $user, [
    'contexto' => 'valor',
]);
```

Reglas:

- Nombre en `snake_case` y en pasado.
- No incluir emails, nombres, teléfonos ni contenido de mensajes.
- No bloquear la respuesta: `AnalyticsService` captura sus propios errores.
- Si el evento se dispara dentro de una transacción, la cola y los broadcasts esperan al commit (`after_commit`).

## Panel

`/admin/analytics` muestra:

- embudo y conversión por paso;
- actividad diaria o mensual;
- fuentes de adquisición;
- retención D1, D7 y D30;
- builders activos semanales.

El comando `analytics:prune` elimina eventos con más de un año de antigüedad y corre mensualmente.
