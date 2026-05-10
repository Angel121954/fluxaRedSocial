# Fluxa — Reglas del Proyecto

## Descripción del proyecto
Red social para desarrolladores latinoamericanos que combina conceptos de LinkedIn, GitHub y Dev.to.
Proyecto final de grado SENA (programa ADSO). Dominio en evaluación: getfluxa.com / fluxahq.com.

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

## Convenciones de código

### PHP / Laravel
- Lógica de negocio en `app/Services/` o `app/Actions/`, **nunca** en controladores
- Observers para efectos secundarios en modelos (ej. sincronización Cloudinary)
- Comandos Artisan para tareas programadas (ej. `cloudinary:cleanup`)
- Validación exclusivamente via Form Requests (`app/Http/Requests/`)
- Policies para autorización (`app/Policies/`)
- Sin lógica en vistas Blade
- Límites de plan definidos en `config/plans.php`
- Imágenes para PDF: siempre base64, nunca URLs externas (Browsershot no las resuelve)

### JavaScript
- **Sin Alpine.js** — vanilla JS únicamente (salvo autorización explícita)
- Sin directivas Blade (`@`) dentro de `<script>`; pasar datos del servidor via atributos `data-*`
- Sin JS inline en vistas Blade
- IIFEs para evitar conflictos de scope global entre módulos
- Todos los archivos JS están registrados individualmente en `vite.config.js` → `input[]`
- No usar `import` dinámico entre módulos de feature; cada archivo es independiente

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

### Commits
- Conventional Commits (`feat:`, `fix:`, `chore:`, `refactor:`, `docs:`, etc.)

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
│   │                          # securePassword, modalScrollFix, emailModalSend
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
| Technology | belongsToMany Projects, Users (onboarding) |
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