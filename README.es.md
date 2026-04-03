<div align="center">

# 🌊 Fluxa

**La red social construida para desarrolladores, por desarrolladores.**

Comparte tus proyectos. Documenta tu camino. Crece en público.

[![Laravel](https://img.shields.io/badge/Laravel-11.48.0-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.1.0-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](https://www.docker.com)
[![Cloudinary](https://img.shields.io/badge/Cloudinary-Almacenamiento_de_Medios-3448C5?style=flat-square&logo=cloudinary&logoColor=white)](https://cloudinary.com)
[![Licencia: Proprietary](https://img.shields.io/badge/Licencia-Propietaria-red.svg?style=flat-square)](LICENSE)
[![PRs Bienvenidos](https://img.shields.io/badge/PRs-bienvenidos-brightgreen.svg?style=flat-square)](CONTRIBUTING.md)

[Funcionalidades](#-funcionalidades) · [Capturas de pantalla](#-capturas-de-pantalla) · [Tecnologías](#-tecnologías) · [Instalación](#-instalación) · [Roadmap](#-roadmap) · [Contribuir](#-contribuir)

</div>

---

## 📖 Acerca de Fluxa

**Fluxa** es una plataforma social de código abierto diseñada específicamente para desarrolladores. Ofrece a los programadores un espacio dedicado para compartir sus proyectos, documentar su progreso, mostrar su stack tecnológico y conectarse con otros desarrolladores — todo de forma pública.

Inspirada en lo mejor de **GitHub**, **Twitter/X** y **Dev.to**, Fluxa se centra en lo que más les importa a los desarrolladores: *proyectos*, *crecimiento* y *comunidad*. Tanto si estás construyendo un proyecto personal, aprendiendo una nueva tecnología o buscando colaboradores, Fluxa es el lugar para compartirlo.

> Construye en público. Crece con la comunidad.

---

## 📸 Capturas de pantalla

<div align="center">

### 👤 Página de Perfil
![Página de Perfil](docs/profile.png)

### ⚙️ Configuración de Perfil
![Configuración de Perfil](docs/settings.png)

### 🚀 Crear Proyecto
![Crear Proyecto](docs/new-project.png)

</div>

---

## ✨ Funcionalidades

### 👤 Perfiles de Usuario
Cada desarrollador en Fluxa tiene un perfil público rico que cuenta su historia de un vistazo.

- **Foto de perfil** — Sube y gestiona tu avatar de desarrollador
- **Biografía** — Cuéntale a la comunidad quién eres y qué construyes
- **Ubicación** — Indica a otros dónde te encuentras
- **Sitio web / Portafolio** — Conecta tu perfil con tu sitio personal
- **Contador de proyectos** — Se actualiza automáticamente con tus proyectos publicados
- **Seguidores y Seguidos** — Construye tu red dentro de la plataforma
- **Días de actividad** — Registra y muestra tu racha de constancia
- **Descarga de CV** — Exporta tu perfil de desarrollador completo como un PDF profesional con un solo clic, impulsado por `html2pdf.js`

---

### 🗂️ Proyectos
El núcleo de Fluxa es compartir lo que construyes.

- **Publicar proyectos** — Crea una página dedicada para cada uno de tus proyectos
- **Título y Descripción** — Presenta tu proyecto claramente con soporte de texto enriquecido
- **Etiquetas de stack tecnológico** — Etiqueta cada tecnología utilizada para que otros puedan encontrar tu trabajo por stack
- **Galería de medios** — Sube capturas de pantalla, demostraciones o banners a través de Cloudinary

---

### 📰 Feed de Actividad
Mantente al día con lo que la comunidad está construyendo.

- Un feed en tiempo real de proyectos y actualizaciones de los desarrolladores que sigues
- Ve los últimos proyectos publicados, nuevos perfiles y la actividad reciente en un solo lugar
- Un centro de información para descubrir lo que está sucediendo en toda la plataforma

---

### 🔍 Página de Exploración
Descubre lo mejor que Fluxa tiene para ofrecer.

- **Proyectos en tendencia** — Los proyectos con más tracción en este momento
- **Proyectos recientes** — Las últimas incorporaciones a la plataforma
- **Desarrolladores para seguir** — Perfiles de desarrolladores sugeridos con quienes conectarte
- Filtra y navega por tecnología para encontrar proyectos en tu stack

---

### 🏷️ Etiquetas de Stack Tecnológico
- Añade etiquetas de tecnología a tus proyectos (p. ej., `Laravel`, `React`, `PostgreSQL`, `Docker`)
- Las etiquetas son buscables y navegables en toda la plataforma
- Ayuda a otros a encontrar proyectos construidos con herramientas específicas

---

### 📄 Exportación de CV desde el Perfil
- Genera un PDF limpio y formateado como currículum a partir de tu perfil de desarrollador en Fluxa
- Incluye tu biografía, ubicación, stack tecnológico, proyectos y estadísticas sociales
- Impulsado por [`html2pdf.js`](https://github.com/eKoopmans/html2pdf.js) — sin necesidad de renderizado en el servidor
- Descarga con un solo clic directamente desde tu página de perfil público

---

### ⚙️ Ajustes
- **Edición de perfil** — Actualiza tu foto, biografía, ubicación y sitio web en cualquier momento
- **Preferencias de cuenta** — Gestiona tu nombre de usuario, correo electrónico y opciones de notificación
- **Opciones de seguridad** — Cambia tu contraseña y administra las sesiones activas

---

## 🛠️ Tecnologías

| Capa | Tecnología |
|---|---|
| **Backend** | [Laravel](https://laravel.com) |
| **Frontend** | [Blade Templates](https://laravel.com/docs/blade) + [Tailwind CSS](https://tailwindcss.com) |
| **Autenticación** | [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze) |
| **Almacenamiento de Medios** | [Cloudinary](https://cloudinary.com) |
| **Infraestructura** | [Docker](https://www.docker.com) + [Laravel Sail](https://laravel.com/docs/sail) |
| **Exportación PDF** | [html2pdf.js](https://github.com/eKoopmans/html2pdf.js) |
| **Base de datos** | MySQL (vía Sail) |

---

## ⚡ Instalación

### Requisitos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu máquina:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (se recomienda v24 o superior)
- [Git](https://git-scm.com/)
- Una terminal con soporte para `bash`

> **Nota:** Laravel Sail gestiona PHP, Composer, Node y todas las demás dependencias dentro de contenedores Docker. **No** necesitas instalar PHP ni Composer localmente.

---

### 1. Clonar el Repositorio

```bash
git clone https://github.com/Angel121954/fluxaRedSocial.git
cd fluxa
```

---

### 2. Copiar el Archivo de Entorno

```bash
cp .env.example .env
```

---

### 3. Configurar las Variables de Entorno

Abre el archivo `.env` y completa los valores requeridos:

```env
# Aplicación
APP_NAME=Fluxa
APP_URL=http://localhost

# Base de datos (valores por defecto de Sail — modifica solo si es necesario)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fluxa
DB_USERNAME=sail
DB_PASSWORD=password

# Cloudinary — obtén tus credenciales en https://cloudinary.com
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
```

> ⚠️ Nunca subas tu archivo `.env` real al control de versiones. Ya está incluido en `.gitignore`.

---

### 4. Instalar Dependencias de PHP vía Docker

Si no tienes Composer instalado localmente, usa este comando de Docker para instalar las dependencias:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

---

### 5. Iniciar Laravel Sail

```bash
./vendor/bin/sail up -d
```

Esto levantará los siguientes contenedores:

| Contenedor | Descripción |
|---|---|
| `fluxa_laravel` | Aplicación Laravel (PHP 8.3) |
| `fluxa_mysql` | Base de datos MySQL |
| `fluxa_redis` | Redis para colas y caché |
| `fluxa_mailpit` | Captura local de correos para desarrollo |

---

### 6. Generar la Clave de la Aplicación

```bash
./vendor/bin/sail artisan key:generate
```

---

### 7. Ejecutar las Migraciones de Base de Datos

```bash
./vendor/bin/sail artisan migrate
```

---

### 8. (Opcional) Sembrar la Base de Datos

Rellena la base de datos con datos de ejemplo para el desarrollo local:

```bash
./vendor/bin/sail artisan db:seed
```

---

### 9. Instalar Dependencias Frontend y Compilar Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Para una compilación de producción:

```bash
./vendor/bin/sail npm run build
```

---

### ✅ Acceder a la Aplicación

Una vez completados todos los pasos, abre tu navegador y visita:

```
http://localhost
```

---

## 🗂️ Estructura del Proyecto

```
fluxa/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controladores de la aplicación
│   │   └── Requests/           # Validación de formularios (Form Requests)
│   ├── Models/                 # Modelos Eloquent (User, Project, Follow...)
│   └── Services/               # Servicios de lógica de negocio
├── database/
│   ├── migrations/             # Esquema de base de datos
│   └── seeders/                # Seeders con datos de ejemplo
├── docs/                       # Capturas del proyecto para el readme
├── public/
│     ├── css/                  # Estilos personalizados de las vistas
│     └── js/                   # JavaScript (incluye uso de html2pdf.js)
├── resources/
│   ├── views/                  # Plantillas Blade
│   │   ├── auth/               # Vistas de autentificación
│   │   ├── components/         # Componentes reutilizables para las demás vistas
│   │   ├── layouts/            # Layouts base
│   │   ├── profile/            # Vistas relacionadas al perfil
│   │   ├── projects/           # Vistas de proyectos
│   │   ├── feed/               # Vistas del feed
│   │   └── explore/            # Vistas de la página Explorar
│   └── css/                    # Punto de entrada de Tailwind
├── routes/
│   ├── web.php                 # Rutas web
│   └── auth.php                # Rutas de autenticación
├── docker-compose.yml          # Configuración de Sail / Docker
└── .env.example                # Plantilla de variables de entorno
```

---

## 🗺️ Roadmap

Fluxa está en desarrollo activo. Esto es lo que viene a continuación:

- [ ] 💬 **Comentarios en proyectos** — Permitir que los desarrolladores dejen comentarios y retroalimentación en cualquier proyecto
- [ ] ❤️ **Likes en proyectos** — Reaccionar a proyectos con likes y destacar los más valorados
- [ ] 🔔 **Sistema de notificaciones** — Notificaciones en tiempo real para seguimientos, comentarios y actividad
- [ ] ✅ **Verificación de desarrolladores** — Sistema de insignias para verificar contribuidores activos y confiables
- [ ] 🔌 **API pública** — API RESTful para integraciones de terceros y herramientas de desarrollador
- [ ] 🌐 **Internacionalización (i18n)** — Soporte completo de múltiples idiomas en toda la plataforma
- [ ] 🔎 **Búsqueda avanzada** — Búsqueda de texto completo en proyectos, perfiles y etiquetas
- [ ] 📊 **Panel de analíticas** — Estadísticas e insights para tu propio perfil y proyectos

¿Tienes una idea? [Abre una solicitud de funcionalidad](https://github.com/tu-usuario/fluxa/issues/new?template=feature_request.md) — las contribuciones y sugerencias siempre son bienvenidas.

---

## 🤝 Contribuir

Las contribuciones son lo que hace prosperar los proyectos de código abierto. Cada aporte, por pequeño que sea, es apreciado y valorado.

### Cómo Contribuir

1. **Haz un fork** del repositorio
2. **Crea** tu rama de funcionalidad:
   ```bash
   git checkout -b feature/nombre-de-tu-funcionalidad
   ```
3. **Realiza un commit** de tus cambios con un mensaje claro:
   ```bash
   git commit -m "feat: agregar funcionalidad de likes en proyectos"
   ```
4. **Sube** tu rama:
   ```bash
   git push origin feature/nombre-de-tu-funcionalidad
   ```
5. **Abre un Pull Request** — describe tus cambios claramente y vincula los issues relacionados

### Lineamientos

- Sigue el estilo y las convenciones de código existentes
- Escribe mensajes de commit claros y descriptivos (recomendamos [Conventional Commits](https://www.conventionalcommits.org/es/))
- Añade comentarios a la lógica compleja
- Prueba tus cambios localmente antes de abrir un PR
- Sé respetuoso y constructivo en todas las comunicaciones

Por favor lee nuestro [CONTRIBUTING.md](CONTRIBUTING.md) para ver las guías de contribución completas.

---

## 🐛 Reportar Problemas

¿Encontraste un error? ¿Tienes alguna pregunta? [Abre un issue](https://github.com/tu-usuario/fluxa/issues/new) y te responderemos lo antes posible.

Por favor incluye:
- Una descripción clara del problema
- Pasos para reproducirlo
- Comportamiento esperado vs comportamiento real
- Tu entorno (sistema operativo, versión de Docker, navegador)

---

## 📄 Licencia

Este proyecto usa una **Licencia Propietaria** — consulta el archivo [LICENSE](LICENSE) para ver los detalles completos.

**Lo que PUEDES hacer:**
- Estudiar y aprender de la estructura del código
- Usarlo para proyectos personales/de aprendizaje
- Usarlo como referencia para aprender arquitecturas y patrones

**Lo que NO PUEDES hacer:**
- Usarlo comercialmente o como SaaS
- Crear una red social competidora
- Vender o redistribuir el código

Para licencias comerciales, contacta al propietario del proyecto.

---

<div align="center">

Hecho con ❤️ para la comunidad de desarrolladores.

**[⬆ Volver al inicio](#-fluxa)**

</div>