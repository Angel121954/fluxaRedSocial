# Fluxa — Reglas del Proyecto

## Descripción del proyecto
Red social para desarrolladores latinoamericanos que combina conceptos de LinkedIn, GitHub y Dev.to.
Proyecto final de grado SENA (programa ADSO). Dominio en evaluación: getfluxa.com / fluxahq.com.

## Filosofía del proyecto

### YAGNI — You Ain't Gonna Need It
No construir para el futuro incierto. Cada línea de código debe resolver un problema **actual**, no uno imaginario. Si una abstracción solo sirve "por si acaso", no se hace.

### KISS — Keep It Simple, Stupid
La solución más simple que funciona es la mejor. No agregar capas de indirección, interfaces, herencia, patrones de diseño o dependencias sin que exista una necesidad real y presente.

### Overengineering está prohibido
- No crear interfaces o contratos hasta que existan **2+ implementaciones concretas** reales
- No crear Services genéricos tipo `BaseService` o `CrudService` — cada Service tiene una responsabilidad única y concreta
- No usar patrones de diseño como decorators, strategies, observers (PHP) a menos que el código actual lo exija, no "por si acaso"
- No abstraer lógica que solo se usa en un lugar
- No añadir repositorios — Eloquent **es** la capa de datos, no necesitas otra
- No crear DTOs a menos que un método reciba 4+ parámetros del mismo tipo
- No usar Traits para "compartir código bonito" — prefiere composición o duplicación controlada (rule of three)
- La duplicación de código es aceptable hasta 3 veces; después de la 3ra ocurrencia, refactoriza

### Principios generales
- **Código explícito > código clever/ingenioso** — Un bloque `if` sencillo vale más que una expresión ternaria anidada de una línea
- **Menos dependencias, mejor** — Cada dependencia nueva es un riesgo de seguridad, mantenimiento y breaking changes. Preguntar siempre ¿podemos hacerlo con lo que ya tenemos?
- **Las reglas están para romperse, pero con permiso** — Si necesitas desviarte de estas convenciones, discútelo antes
- **Cada archivo debe tener una responsabilidad única** — Si no puedes explicar qué hace en una frase, está haciendo demasiado
- **Las vistas Blade no tienen lógica** — Sin condicionales complejos, sin consultas, sin procesamiento de datos. Solo `@if` simples y bucles `@foreach`

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Framework | Laravel 11 + Laravel Sail (Docker + WSL2) |
| PHP mínimo | 8.2 |
| Frontend | Blade + Tailwind CSS 3 + Vanilla JS |
| Reactividad | Livewire 4 |
| WebSockets | Laravel Reverb 1.x (puerto 8080, Supervisor en Docker) |
| Echo cliente | laravel-echo + pusher-js |
| Build | Vite 4 + laravel-vite-plugin |
| Base de datos | MySQL (via Sail) |
| Medios | Cloudinary (cloudinary-labs/cloudinary-laravel 3) |
| Colas | Laravel Queues |
| PDF | Spatie Browsershot 5 + Puppeteer |
| Push notifications | laravel-notification-channels/webpush (VAPID) |
| Auth | Laravel Fortify 1 + Laravel Socialite 5 |
| Códigos QR | endroid/qr-code 5 |
| Rutas JS | ziggy-js 2 |
| Observabilidad | Laravel Telescope 5 (dev) |
| Testing | PestPHP 3 |
| Linting PHP | Laravel Pint |
| Fuente principal | Figtree (Tailwind sans) |
| Fuente monospace | JetBrains Mono (--mono) |

### Paquetes (Composer)

| Paquete | Propósito |
|---------|-----------|
| Laravel Framework | Framework base |
| Laravel Fortify | Autenticación (login, registro, 2FA) |
| Laravel Socialite | OAuth social (GitHub, Google, etc.) |
| Laravel Reverb | WebSockets en tiempo real |
| Laravel Sanctum | API tokens |
| Laravel Telescope | Debug y observabilidad (dev) |
| Laravel Sail | Entorno Docker (dev) |
| Laravel Pint | Linting PHP (dev) |
| Livewire | Componentes reactivos del lado del servidor |
| Spatie Browsershot | Generación de PDF con Puppeteer |
| Cloudinary Laravel | Subida y transformación de imágenes |
| Endroid QR Code | Generación de códigos QR |
| Pest PHP | Testing (dev) |
| Guzzle | Cliente HTTP |
| Faker PHP | Generación de datos falsos (dev) |
| PHPUnit | Testing base (dev) |
| Laravel Tinker | REPL interactivo (dev) |

### Paquetes JS (NPM)

| Paquete | Propósito |
|---------|-----------|
| Vite | Build tool y dev server |
| Tailwind CSS | Framework CSS utilitario |
| Axios | Cliente HTTP para el frontend |
| Laravel Echo | Cliente WebSocket (Reverb/Pusher) |
| Pusher JS | Protocolo WebSocket |
| Chart.js | Gráficos interactivos (admin) |
| Ziggy | Generación de rutas Laravel en JS |
| Puppeteer | Headless Chrome (para PDF con Browsershot) |
| Lodash | Utilidades JS |

### Librerías CDN

| Librería | Propósito |
|----------|-----------|
| SweetAlert2 | Alertas y modales personalizados |
| Devicon | Íconos de tecnologías de programación |

### APIs externas

| API | Propósito |
|-----|-----------|
| Cloudinary | Almacenamiento, transformación y entrega de imágenes |
| GIPHY | Búsqueda e inserción de GIFs en mensajería |

## Convenciones de código

### PHP / Laravel
- `declare(strict_types=1)` en **todos** los archivos PHP nuevos
- Type hints y return types en **todos** los métodos, sin excepción
- `readonly` properties donde el valor no cambia después de construcción
- Lógica de negocio en `app/Services/` o `app/Actions/`, **nunca** en controladores
- Observers para efectos secundarios en modelos (ej. sincronización Cloudinary)
- Comandos Artisan para tareas programadas (ej. `cloudinary:cleanup`)
- Validación exclusivamente via Form Requests (`app/Http/Requests/`)
- Policies para autorización (`app/Policies/`)
- Sin lógica en vistas Blade
- Límites de plan definidos en `config/plans.php`
- Imágenes para PDF: siempre base64, nunca URLs externas (Browsershot no las resuelve)
- Named arguments en llamadas a métodos con 3+ parámetros opcionales
- Rutas invocables (`__invoke`) para controladores con una sola acción
- Composición sobre herencia — sin herencia profunda de controladores o services
- Modelos: definir `$fillable` o `$guarded`, `$casts`, `$with` para eager loading por defecto, `$primaryKey` si no es `id`
- Migraciones: `Schema::dropIfExists()` en `down()`, nombres descriptivos como `add_avatar_column_to_users_table`
- Rutas: usar route model binding, evitar recibir `$id` — recibir el modelo directamente
- No usar `dd()`, `dump()`, `ray()` en commits — usar `Log::error()` o lanzar excepción
- `config/` para configuración, nunca `env()` fuera de archivos de config
- Preferir `firstOrCreate()` / `firstOrFail()` sobre `where()->first()` cuando aplique
- Usar `?->` (nullsafe operator) y `??` (null coalescing) para evitar null checks verbosos

### JavaScript
- **Sin Alpine.js** — vanilla JS únicamente (salvo autorización explícita)
- Sin directivas Blade (`@`) dentro de `<script>`; pasar datos del servidor via atributos `data-*`
- Sin JS inline en vistas Blade
- IIFEs para evitar conflictos de scope global entre módulos
- Todos los archivos JS están registrados individualmente en `vite.config.js` → `input[]`
- No usar `import` dinámico entre módulos de feature; cada archivo es independiente
- Un archivo JS = una responsabilidad (ej. `toast.js` solo maneja toasts)
- `fetch` con `async/await`, no `.then()` — excepción: event listeners de un solo uso
- Event delegation para elementos dinámicos (escuchar en padre con `e.target.closest()` )
- No manipular `.style` directamente — usar `classList.add/remove/toggle` y CSS
- Si algo se puede resolver con CSS (`:hover`, `:focus-within`, `transition`), no usar JS
- Preferir `textContent` sobre `innerHTML` para prevenir XSS
- Usar `data-*-id` para identificar elementos del DOM (ej. `data-project-id="5"`)
- Constantes en UPPER_SNAKE_CASE, funciones en camelCase, eventos en kebab-case

### CSS / Diseño
- Design tokens en `resources/css/variables.css` — **debe cargarse antes que cualquier otro CSS**
- Color acento: `--accent: #12b3b6` (teal)
- Superficies: `--surface`, `--bg`, `--bg-subtle`
- Escala de texto: `--ink-900` … `--ink-50`
- Bordes: `--border`, `--border-strong`
- Radios: `--r-xs` `--r-sm` `--r-md` `--r-lg` `--r-xl`
- Sombras: `--shadow-xs` `--shadow-sm` `--shadow-md`
- Dark mode: `darkMode: 'class'` (Tailwind) + clase `.dark` en `<html>` + `localStorage`
- Cada sección tiene su propio archivo CSS en `resources/css/[sección]/[vista].css`
- No hardcodear colores hex — usar siempre los tokens de `variables.css` o clases Tailwind basadas en tokens
- Animaciones: preferir `transform` y `opacity` (compositor-friendly) sobre `width`, `height`, `top`, `left`
- Transiciones: máximo 200-300ms, con `ease-out` o `ease-in-out`

### Testing (PestPHP)
- `describe()` para agrupar tests relacionados, `it()` para cada caso individual
- Tests nombrados en español: `it('crea un proyecto correctamente cuando el usuario está autenticado')`
- AAA: Arrange (organizar), Act (actuar), Assert (afirmar) — separar con líneas en blanco
- Factory states para variantes: `User::factory()->unverified()->create()`
- Feature tests para HTTP (controladores, rutas, autorización)
- Unit tests para Services, Actions, Jobs
- No testear el framework — testear tu lógica de negocio
- Cobertura mínima sugerida: 70% en Services y Actions
- Usar `fake()` para facades (Storage, Queue, Notification, Mail)
- Usar `Http::fake()` para llamadas externas (Cloudinary API, etc.)
- No usar `assertDatabaseHas()` sin limpiar datos después

### Base de datos / Migraciones
- Nombres de tablas en snake_case plural: `work_experiences`, `project_media`
- Columnas: `created_by` (user_id), `deleted_at` (soft deletes), `timestamps()`
- Índices compuestos para queries frecuentes: `$table->index(['user_id', 'created_at'])`
- Foreign keys: `$table->foreignId('user_id')->constrained()->cascadeOnDelete()`
- Una migración por tabla (no mezclar schemas no relacionados)
- No modificar migraciones existentes que ya corrieron en producción — crear nueva migración
- `$casts` en modelos para atributos booleanos, fechas, arrays, etc.

### Rendimiento
- **N+1: prohibido** — siempre con `with()` o `load()` para relaciones
- Eager loading por defecto en `$with` del modelo para relaciones siempre necesarias
- `chunk()` o `lazy()` para procesar batches grandes sin agotar memoria
- Cachear queries pesadas (Redis o file): `Cache::remember('trending_projects', 3600, fn() => ...)`
- NO hacer queries dentro de loops — siempre eager load o collection methods
- Usar `select()` explícito cuando solo necesitas columnas específicas
- eager loading condicional: `->with('comments', fn($q) => $q->select('id', 'body'))`

### Manejo de errores
- Excepciones personalizadas para dominios específicos (`app/Exceptions/`)
- `try/catch` en Services, no en Controllers — el Controller delega y deja fluir la excepción
- Controllers: confiar en FormRequest + Policy para validar/autorizar; si llegan al método, fluyen
- Loggear con contexto: `Log::error('No se pudo subir avatar a Cloudinary', ['user_id' => $user->id])`
- Mostrar errores al usuario en español, con mensajes legibles no técnicos
- En JS: `try/catch` en cada `fetch`, mostrar toast de error con mensaje amigable

### Seguridad
- **Nunca** exponer IDs internos en URLs si el modelo tiene `$slug` (`projects/{slug}` en vez de `projects/{id}`)
- `XSS`: escapar output con `{{ }}` en Blade, evitar `{!! !!}`, no usar `innerHTML`
- CSRF: `@csrf` en todos los formularios, `X-CSRF-TOKEN` en headers de fetch
- SQL Injection: Eloquent lo maneja — no concatenar valores en queries raw
- Autorización: siempre via Policy, nunca `if (auth()->id() === $model->user_id)` en el controller
- Archivos subidos: validar tipo MIME, tamaño máximo, scan de virus (si aplica)
- No almacenar secrets en `.env` que no tenga `.env.example` correspondiente
- Guest users: middleware `RestrictGuest` en rutas protegidas, `CleanExpiredGuests` programado

## Arquitectura

### Estructura de directorios
app/
├── Actions/Fortify/           # Acciones de auth (CreateNewUser, etc.)
├── Console/                   # Artisan commands
├── Events/                    # Eventos broadcast (NewMessage, FollowToggled, etc.)
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Api/               # SearchController, LocationController
│   │   ├── Auth/              # Login, Register, Social, 2FA
│   │   ├── Explore/
│   │   ├── Feed/
│   │   ├── Follows/
│   │   ├── Messages/
│   │   ├── Notifications/
│   │   ├── Onboarding/
│   │   ├── Pages/             # About, Contact, Terms, PrivacyPolicy
│   │   ├── Profile/           # Profile, Account, CV, WorkExperience, Education, etc.
│   │   ├── Projects/          # ProjectController, CommentController, CommentLikeController
│   │   ├── Suggestions/
│   │   └── Technology/
│   ├── Middleware/
│   │   ├── CheckAdmin.php
│   │   ├── CheckOnboarding.php
│   │   ├── CheckUserActive.php
│   │   ├── CleanExpiredGuests.php
│   │   ├── PreventBackHistory.php
│   │   └── RestrictGuest.php
│   └── Requests/              # Form Requests por feature
├── Jobs/                      # UploadAvatarJob (queue)
├── Models/                    # User, Profile, Project, Message, Conversation, etc.
├── Notifications/             # Push notifications (VAPID)
├── Policies/                  # Project, Message, Profile, Education, Comment, WorkExperience
├── Providers/
├── Services/
│   ├── CloudinaryService.php
│   ├── CVService.php
│   ├── LocationService.php
│   ├── MessageService.php
│   ├── ProfileService.php
│   └── ProjectService.php
└── View/Components/           # AppLayout, GuestLayout, Modal
resources/
├── css/
│   ├── variables.css          # ← tokens globales, siempre primero
│   ├── app.css
│   ├── auth/
│   ├── core/                  # explore.css, messages.css
│   ├── notifications.css
│   ├── onboarding/
│   ├── profile/
│   ├── projects/
│   ├── public/
│   ├── settings/
│   ├── shared/
│   └── admin/
├── js/
│   ├── app.js                 # Bootstrap Echo + Axios global
│   ├── bootstrap.js
│   ├── core/
│   │   ├── globals.js
│   │   ├── explore/           # index, like, loadMore, projectMenu, skillEndorsement, tabs, topics
│   │   ├── messages/          # index, messageRenderer, messageService, messageUtils,
│   │   │                      # realtimeHandler, sender, typingHandler, ui
│   │   └── projects/          # newProject, projectMedia
│   ├── notifications/         # index, badges, realtime
│   ├── onboarding/            # index, suggestions, technologies
│   ├── profile/               # index, avatar, cv, account, tabs, workExperience,
│   │                          # education, filters, dropdown, profileOptions, etc.
│   ├── projects/              # commentForm, commentRenderer, modalComment
│   ├── settings/              # locationSelects
│   ├── shared/                # index, toast, topbar, security, passwordVisibility,
│   │                          # securePassword, scrollLock, emailModalSend
│   └── admin/                 # index
└── views/
├── layouts/
├── components/            # x-topbar, x-sidebar, x-footer, x-modal
├── auth/
├── onboarding/
├── profile/ + partials/
├── explore/
├── feed/
├── messages/
├── notifications/
├── projects/
├── settings/
├── cv/
├── public/                # about, contact, terms, privacy-policy
├── suggestions/
└── admin/
routes/
├── web.php                    # Rutas principales (agrupadas por middleware)
├── api.php                    # /api/search, /api/locations/*
├── auth.php                   # Rutas Fortify (login, register, 2FA...)
└── channels.php               # Canales Reverb

### Modelos principales

| Modelo | Relaciones clave |
|--------|-----------------|
| User | hasOne Profile, hasMany Projects, Messages, Conversations, Notifications |
| Profile | belongsTo User, hasMany WorkExperiences, Educations |
| Project | belongsTo User, hasMany ProjectMedia, Comments, ProjectLikes, ProjectBookmarks |
| Conversation | belongsToMany Users, hasMany Messages |
| Message | belongsTo Conversation, User |
| Comment | belongsTo Project, User; hasMany CommentLikes |
| Technology | `category` (`language`, `framework`, `library`, `database`, `tool`, `platform`). belongsToMany Projects, Users (onboarding) |
| SkillEndorsement | belongsTo User (endorser), Project |

### Grupos de middleware (web.php)
1. **Público:** social auth, guest login
2. **`auth` + `prevent-back-history`:** onboarding, follow toggle, CV download
3. **`auth` + `prevent-back-history` + `onboarding`:** todo lo protegido (feed, explore, mensajes…)
4. **`verified` + `restrict.guest`:** perfil, settings, proyectos, mensajes, notificaciones
5. **`admin`:** panel de sugerencias

### Eventos broadcast (Reverb)
| Evento | Canal |
|--------|-------|
| `NewMessage` | private-conversation.{id} |
| `UserTyping` | private-conversation.{id} |
| `ConversationCreated` | private-user.{id} |
| `NotificationCreated` | private-user.{id} |
| `NotificationRead` | private-user.{id} |
| `FollowToggled` | private-user.{id} |
| `PrivacyUpdated` | private-user.{id} |

## Decisiones importantes

- **Sin Alpine.js** en ninguna vista a menos que se apruebe explícitamente
- **Cloudinary** es el único almacenamiento de medios; sin almacenamiento local de imágenes de usuario
- **UploadAvatarJob** maneja el upload a Cloudinary de forma asíncrona via Queue
- **Reverb** corre en puerto 8080 bajo Supervisor en Docker; separado del proceso PHP
- **Dark mode** via clase `.dark` en `<html>` + `localStorage`, toggled por componente Livewire
- **Browsershot/PDF:** imágenes siempre en base64 (las URLs externas fallan en contexto headless)
- **Broadcasting:** event naming con prefijo punto en Echo; header `X-Socket-ID` para `.toOthers()`
- **JS y datos del servidor:** exclusivamente via `data-*` en el HTML, nunca `@` dentro de `<script>`
- **Ziggy** disponible como `route()` en JS para generar URLs con nombre de ruta
- **Usuarios guest:** sistema de login invitado con middleware `CleanExpiredGuests` y `RestrictGuest`
- **Freemium:** límites por plan definidos en `config/plans.php`, consultados en Services
- **LocationService:** países y ciudades servidos via `/api/locations/` con selects encadenados en JS (`settings/locationSelects.js`)

### Reglas de ORM / Eloquent
- `User::find($id)` NO — `User::findOrFail($id)` SÍ, o route model binding mejor
- `where('active', 1)->where('verified', 1)` NO — **local scopes** SÍ: `scopeActive()`, `scopeVerified()`
- Relaciones siempre tipadas con docblock o PHP 8.2+ native return-type (cuando Laravel lo soporte)
- `$model->relation()->create([...])` sobre `$model->relation()->save(new Related([...]))`
- `loadCount()` / `loadSum()` para agregados sin cargar toda la relación
- Evitar `withCount` en listas grandes — cachear o columna denormalizada

## Features implementados
- OAuth social login + 2FA (Fortify)
- Sistema de invitado (guest login + auto-cleanup)
- Onboarding multi-paso (tecnologías → rol → sugerencias de usuarios)
- Feed de proyectos con paginación
- Explore con filtros: trending, reciente, por topic/tecnología
- Perfil público con tabs (proyectos, experiencia, educación, bookmarks)
- CRUD Proyectos con media múltiple (Cloudinary), likes, bookmarks, reportes, endorsements
- CRUD Experiencia Laboral y Educación (con límites de plan)
- Comentarios en proyectos con likes
- Sistema de follows con contador en tiempo real
- Mensajería en tiempo real (Reverb + Echo, typing indicator)
- Notificaciones in-app + web push (VAPID) en tiempo real
- Preferencias de notificaciones por tipo
- Exportación CV en PDF (Browsershot, template Blade)
- Dark mode (Livewire ThemeToggle)
- Configuración de cuenta, privacidad, seguridad
- Selects de país/ciudad encadenados via API
- Panel de sugerencias (admin)
- Página de contacto, términos, privacidad, about
- Reporte de usuarios y proyectos
- Búsqueda de usuarios (API interna)

## Notas para el agente
- **Leer AGENTS.md y UI_UX.md antes de cualquier cambio** (ambos son obligatorios)
- **Pedir permiso antes de leer o modificar `.env` o archivos con credenciales**
- Nunca usar Alpine.js salvo indicación explícita
- Nunca poner directivas Blade dentro de `<script>`; usar `data-*`
- Nunca JS inline en vistas Blade
- Todo JS nuevo debe registrarse en `vite.config.js` → `input[]`
- CSS nuevo debe seguir la convención `resources/css/[sección]/[vista].css` y registrarse en `vite.config.js`
- Siempre importar `variables.css` o asegurarse de que esté cargado antes del CSS de cualquier vista
- Los tokens de diseño (`--accent`, `--ink-*`, `--surface`, etc.) vienen de `variables.css`, no hardcodear colores hex
- Lógica nueva → Service o Action, nunca directo en el Controller
- Validación → siempre Form Request, nunca `$request->validate()` en el controller
- Autorización → siempre Policy, nunca condicionales manuales en controllers
- Nuevas rutas protegidas van dentro del grupo `verified + restrict.guest` salvo excepción justificada
- **Overengineering está prohibido** — si crees que necesitas un patrón de diseño, una interfaz, una abstracción extra, pregúntate "¿qué problema resuelve HOY?" Si la respuesta es "ninguno, pero en el futuro quizás", no lo hagas
- **Busca simplicidad primero** — antes de crear un Service, pregunta si una función en el modelo o un-scope no sería suficiente
- **No añadas dependencias sin preguntar** — cada composer.json nuevo debe justificarse
- **Si ves un patrón que se repite 3+ veces, refactoriza** — antes de eso, duplicar está bien
- **No dejes commented code** — si no se necesita, se borra. Git guarda el historial
- **No anidar más de 3 niveles** (if dentro de if dentro de if) — refactoriza a early return o método separado
- **Respeta el principio de sorpresa mínima** — el código debe hacer lo que parece hacer
