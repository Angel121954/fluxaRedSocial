import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";
import os from "os";

const cachePath = process.env.LARAVEL_SAIL
    ? path.join(os.homedir(), '.vite')
    : path.join(__dirname, '.vite');

export default defineConfig({
    cacheDir: cachePath,
    plugins: [
        laravel({
            publicDirectory: 'public',
            hotFile: 'public/hot',
            buildDirectory: 'build',
            input: [
                // CSS global
                "resources/css/app.css",
                "resources/css/variables.css",

                // Auth
                "resources/css/auth/login.css",
                "resources/css/auth/register.css",
                "resources/css/auth/forgotPassword.css",
                "resources/css/auth/resetPassword.css",
                "resources/css/auth/twoFactor.css",

                // Shared
                "resources/css/shared/topbar.css",

                // Core
                "resources/css/core/explore.css",
                "resources/css/core/messages.css",

                // Notifications
                "resources/css/notifications.css",
                "resources/css/profile/shared.css",
                "resources/css/profile/sidebar.css",
                "resources/css/profile/profile.css",
                "resources/css/profile/workExperience.css",
                "resources/css/profile/cv.css",
                "resources/css/profile/modalImage.css",
                "resources/css/profile/notificationsProfile.css",

                // Settings
                "resources/css/settings/account.css",
                "resources/css/settings/security.css",
                "resources/css/settings/configuration.css",
                "resources/css/settings/privacy.css",
                "resources/css/settings/preferences.css",

                // Projects
                "resources/css/projects/newProject.css",
                "resources/css/projects/projectMedia.css",

                // Public
                "resources/css/public/aboutFluxa.css",
                "resources/css/public/terms.css",
                "resources/css/public/privacyPolicy.css",
                "resources/css/public/contact.css",
                "resources/css/public/footer.css",

                // Onboarding
                "resources/css/onboarding/role.css",
                "resources/css/onboarding/technologies.css",
                "resources/css/onboarding/suggestions.css",

                // Admin
                "resources/css/admin/suggestions.css",

                // JS global
                "resources/js/app.js",
                "resources/js/shared/index.js",
                "resources/js/core/globals.js",

                // Profile JS
                "resources/js/profile/index.js",
                "resources/js/profile/avatar.js",
                "resources/js/profile/cv.js",
                "resources/js/profile/account.js",
                "resources/js/profile/configuration.js",
                "resources/js/profile/workExperience.js",
                "resources/js/profile/education.js",
                "resources/js/profile/commentHandler.js",
                "resources/js/profile/destroyAccount.js",
                "resources/js/profile/dropdown.js",
                "resources/js/profile/filters.js",
                "resources/js/profile/profileOptions.js",
                "resources/js/profile/shareProfile.js",
                "resources/js/profile/tabs.js",

                // Shared JS
                "resources/js/shared/topbar.js",
                "resources/js/shared/toast.js",
                "resources/js/shared/passwordVisibility.js",
                "resources/js/shared/securePassword.js",
                "resources/js/shared/security.js",
                "resources/js/shared/modalScrollFix.js",
                "resources/js/shared/emailModalSend.js",

                // Core Explore JS
                "resources/js/core/explore/index.js",
                "resources/js/core/explore/like.js",
                "resources/js/core/explore/loadMore.js",
                "resources/js/core/explore/projectMenu.js",
                "resources/js/core/explore/skillEndorsement.js",
                "resources/js/core/explore/tabs.js",
                "resources/js/core/explore/topics.js",

                // Core Messages JS
                "resources/js/core/messages/index.js",
                "resources/js/core/messages/messageRenderer.js",
                "resources/js/core/messages/messageService.js",
                "resources/js/core/messages/messageUtils.js",
                "resources/js/core/messages/realtimeHandler.js",
                "resources/js/core/messages/sender.js",
                "resources/js/core/messages/typingHandler.js",
                "resources/js/core/messages/ui.js",

                // Core Notifications JS
                "resources/js/notifications/index.js",
                "resources/js/notifications/badges.js",
                "resources/js/notifications/realtime.js",

                // Projects JS
                "resources/js/projects/modalComment.js",
                "resources/js/core/projects/newProject.js",
                "resources/js/core/projects/projectMedia.js",

                // Onboarding JS
                "resources/js/onboarding/index.js",
                "resources/js/onboarding/suggestions.js",
                "resources/js/onboarding/technologies.js",

                // Admin JS
                "resources/js/admin/index.js",
            ],
            refresh: true,
        }),
    ],
    server: {
        host: "0.0.0.0",
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
        cors: {
            origin: '*',
        },
    },
    optimizeDeps: {
        include: ['laravel-echo', 'pusher-js'],
        exclude: ['lucide-react'],
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios', 'lodash'],
                },
            },
        },
    },
});