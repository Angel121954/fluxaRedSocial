<div align="center">

# 🌊 Fluxa

**The social network built for developers, by developers.**

Share your projects. Document your journey. Grow in public.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](https://www.docker.com)
[![Cloudinary](https://img.shields.io/badge/Cloudinary-Media_Storage-3448C5?style=flat-square&logo=cloudinary&logoColor=white)](https://cloudinary.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](CONTRIBUTING.md)

[Features](#-features) · [Screenshots](#-screenshots) · [Tech Stack](#-tech-stack) · [Installation](#-installation) · [Roadmap](#-roadmap) · [Contributing](#-contributing)

</div>

---

## 📖 About Fluxa

**Fluxa** is an open-source social platform designed specifically for developers. It gives programmers a dedicated space to share their projects, document their progress, showcase their tech stack, and connect with other developers — all in public.

Inspired by the best parts of **GitHub**, **Twitter/X**, and **Dev.to**, Fluxa focuses on what developers care about most: *projects*, *growth*, and *community*. Whether you are building a side project, learning a new technology, or looking for collaborators, Fluxa is the place to share it.

> Build in public. Grow with the community.

---

## 📸 Screenshots

<div align="center">

### 👤 Profile Page
![Profile Page](https://res.cloudinary.com/demo/image/upload/fluxa/profile-page.png)

### ⚙️ Profile Settings
![Profile Settings](https://res.cloudinary.com/demo/image/upload/fluxa/profile-settings.png)

### 🚀 Create Project
![Create Project](https://res.cloudinary.com/demo/image/upload/fluxa/create-project.png)

</div>

---

## ✨ Features

### 👤 User Profiles
Every developer on Fluxa gets a rich, public profile that tells their story at a glance.

- **Profile photo** — Upload and manage your developer avatar
- **Biography** — Tell the community who you are and what you build
- **Location** — Let others know where you are based
- **Website / Portfolio link** — Connect your profile to your personal site
- **Projects count** — Automatically reflects the number of published projects
- **Followers & Following** — Build your network inside the platform
- **Activity days** — Track and display your consistency streak
- **CV Download** — Export your entire developer profile as a polished PDF résumé with one click, powered by `html2pdf.js`

---

### 🗂️ Projects
The core of Fluxa is sharing what you build.

- **Publish projects** — Create a dedicated page for each of your projects
- **Title & Description** — Present your project clearly with rich text support
- **Tech stack tags** — Tag every technology used so others can find your work by stack
- **Media gallery** — Upload screenshots, demos, or banners via Cloudinary

---

### 📰 Activity Feed
Stay up to date with what the community is building.

- A real-time feed of projects and updates from developers you follow
- See the latest publishes, new profiles, and recent activity in one place
- A central hub for discovering what is happening across the platform

---

### 🔍 Explore Page
Discover the best of what Fluxa has to offer.

- **Trending projects** — Projects gaining the most traction right now
- **Recent projects** — The newest additions to the platform
- **Developers to follow** — Suggested developer profiles to connect with
- Filter and browse by technology to find projects in your stack

---

### 🏷️ Technology Stack Tags
- Attach technology tags to your projects (e.g., `Laravel`, `React`, `PostgreSQL`, `Docker`)
- Tags are searchable and browsable across the platform
- Help others find projects built with specific tools

---

### 📄 Profile CV Export
- Generates a clean, formatted PDF résumé from your Fluxa developer profile
- Includes your biography, location, tech stack, projects, and social stats
- Powered by [`html2pdf.js`](https://github.com/eKoopmans/html2pdf.js) — no server-side rendering required
- One-click download directly from your public profile page

---

### ⚙️ Settings
- **Profile editing** — Update your photo, bio, location, and website at any time
- **Account preferences** — Manage your username, email, and notification options
- **Security options** — Change your password and manage active sessions

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | [Laravel](https://laravel.com) |
| **Frontend** | [Blade Templates](https://laravel.com/docs/blade) + [Tailwind CSS](https://tailwindcss.com) |
| **Authentication** | [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze) |
| **Media Storage** | [Cloudinary](https://cloudinary.com) |
| **Infrastructure** | [Docker](https://www.docker.com) + [Laravel Sail](https://laravel.com/docs/sail) |
| **PDF Export** | [html2pdf.js](https://github.com/eKoopmans/html2pdf.js) |
| **Database** | MySQL (via Sail) |

---

## ⚡ Installation

### Requirements

Before getting started, make sure you have the following installed on your machine:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (v24 or higher recommended)
- [Git](https://git-scm.com/)
- A terminal with `bash` support

> **Note:** Laravel Sail handles PHP, Composer, Node, and all other dependencies inside Docker containers. You do **not** need to install PHP or Composer locally.

---

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/fluxa.git
cd fluxa
```

---

### 2. Copy the Environment File

```bash
cp .env.example .env
```

---

### 3. Configure Environment Variables

Open `.env` and fill in the required values:

```env
# Application
APP_NAME=Fluxa
APP_URL=http://localhost

# Database (Sail defaults — change only if needed)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fluxa
DB_USERNAME=sail
DB_PASSWORD=password

# Cloudinary — get your credentials at https://cloudinary.com
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

> ⚠️ Never commit your real `.env` file to version control. It is already listed in `.gitignore`.

---

### 4. Install PHP Dependencies via Docker

If you do not have Composer installed locally, use this one-line Docker command to install dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

---

### 5. Start Laravel Sail

```bash
./vendor/bin/sail up -d
```

This will spin up the following containers:

| Container | Description |
|---|---|
| `fluxa_laravel` | Laravel application (PHP 8.3) |
| `fluxa_mysql` | MySQL database |
| `fluxa_redis` | Redis for queues and cache |
| `fluxa_mailpit` | Local mail catching interface |

---

### 6. Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```

---

### 7. Run Database Migrations

```bash
./vendor/bin/sail artisan migrate
```

---

### 8. (Optional) Seed the Database

Populate the database with sample data for local development:

```bash
./vendor/bin/sail artisan db:seed
```

---

### 9. Install Frontend Dependencies and Compile Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

For a production build:

```bash
./vendor/bin/sail npm run build
```

---

### ✅ Access the Application

Once all steps are complete, open your browser and visit:

```
http://localhost
```

---

## 🗂️ Project Structure

```
fluxa/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Application controllers
│   │   └── Requests/           # Form request validation
│   ├── Models/                 # Eloquent models (User, Project, Follow...)
│   └── Services/               # Business logic services
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Sample data seeders
├── resources/
│   ├── views/                  # Blade templates
│   │   ├── layouts/            # Base layouts
│   │   ├── profile/            # Profile-related views
│   │   ├── projects/           # Project views
│   │   ├── feed/               # Feed views
│   │   └── explore/            # Explore page views
│   ├── css/                    # Tailwind entry point
│   └── js/                     # JavaScript (including html2pdf.js usage)
├── routes/
│   ├── web.php                 # Web routes
│   └── auth.php                # Authentication routes
├── docker-compose.yml          # Sail / Docker configuration
└── .env.example                # Environment variable template
```

---

## 🗺️ Roadmap

Fluxa is actively developed. Here is what is coming next:

- [ ] 💬 **Comments on projects** — Allow developers to leave comments and feedback on any project
- [ ] ❤️ **Project likes** — React to projects with likes and track the most appreciated work
- [ ] 🔔 **Notifications system** — Real-time notifications for follows, comments, and activity
- [ ] ✅ **Developer verification** — Badge system to verify active and trusted contributors
- [ ] 🔌 **Public API** — RESTful API for third-party integrations and developer tooling
- [ ] 🌐 **Internationalization (i18n)** — Full multi-language support across the platform
- [ ] 🔎 **Advanced search** — Full-text search across projects, profiles, and tags
- [ ] 📊 **Analytics dashboard** — Insights and stats for your own profile and projects

Have an idea? [Open a feature request](https://github.com/your-username/fluxa/issues/new?template=feature_request.md) — contributions and suggestions are always welcome.

---

## 🤝 Contributing

Contributions are what make open-source projects thrive. Every contribution, no matter how small, is appreciated and valued.

### How to Contribute

1. **Fork** the repository
2. **Create** your feature branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Commit** your changes with a clear message:
   ```bash
   git commit -m "feat: add project like functionality"
   ```
4. **Push** to your branch:
   ```bash
   git push origin feature/your-feature-name
   ```
5. **Open a Pull Request** — describe your changes clearly and link any related issues

### Guidelines

- Follow the existing code style and conventions
- Write clear, descriptive commit messages (we recommend [Conventional Commits](https://www.conventionalcommits.org/))
- Add comments to complex logic
- Test your changes locally before opening a PR
- Be respectful and constructive in all communications

Please read our [CONTRIBUTING.md](CONTRIBUTING.md) for the full contribution guidelines.

---

## 🐛 Reporting Issues

Found a bug? Have a question? [Open an issue](https://github.com/your-username/fluxa/issues/new) and we will get back to you as soon as possible.

Please include:
- A clear description of the problem
- Steps to reproduce it
- Expected vs actual behavior
- Your environment (OS, Docker version, browser)

---

## 📄 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for full details.

You are free to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of this software, subject to the conditions of the MIT License.

---

<div align="center">

Made with ❤️ for the developer community.

**[⬆ Back to top](#-fluxa)**

</div>