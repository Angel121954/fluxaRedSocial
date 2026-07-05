(function () {
    'use strict';

    function escapeHtml(str) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(str ?? '').replace(/[&<>"']/g, function (c) { return map[c]; });
    }

    const bodyEl = document.getElementById('projectsModalBody');
    const titleEl = document.getElementById('projectsModalTitle');
    const subtitleEl = document.getElementById('projectsModalSubtitle');

    if (!bodyEl) return;

    function openProjects(userId) {
        titleEl.textContent = 'Proyectos';
        subtitleEl.textContent = 'Proyectos publicados por este usuario';

        showLoading();
        window.openModal('projectsModal');

        fetchProjects(userId);
    }

    function closeProjects() {
        window.closeModal('projectsModal');
        bodyEl.innerHTML = '';
    }

    function showLoading() {
        bodyEl.innerHTML =
            '<div class="projects-loading">' +
            '<div class="projects-skeleton"></div>' +
            '<div class="projects-skeleton"></div>' +
            '<div class="projects-skeleton"></div>' +
            '</div>';
    }

    function showError() {
        bodyEl.innerHTML =
            '<div class="projects-empty">' +
            '<p>No se pudieron cargar los proyectos. Intenta de nuevo.</p>' +
            '</div>';
    }

    function showEmpty() {
        bodyEl.innerHTML =
            '<div class="projects-empty">' +
            '<p>Este usuario aún no ha publicado proyectos.</p>' +
            '</div>';
    }

    function fetchProjects(userId) {
        const url = '/users/' + userId + '/projects';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
        })
            .then(function (res) {
                if (!res.ok) throw new Error('Error al cargar');
                return res.json();
            })
            .then(function (data) {
                const projects = data.projects;
                if (!projects || projects.length === 0) {
                    showEmpty();
                    return;
                }
                renderProjects(projects);
            })
            .catch(function () {
                showError();
            });
    }

    function renderProjects(projects) {
        let html = '<div class="projects-list">';

        projects.forEach(function (p) {
            var statsHtml = '';

            statsHtml +=
                '<span class="projects-item-stat">' +
                '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>' +
                escapeHtml(String(p.likes_count)) +
                '</span>';

            statsHtml +=
                '<span class="projects-item-stat">' +
                '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>' +
                escapeHtml(String(p.comments_count)) +
                '</span>';

            html +=
                '<a href="/projects/' + escapeHtml(String(p.id)) + '" class="projects-item">' +
                '<div class="projects-item-content">' +
                '<span class="projects-item-title">' + escapeHtml(p.title) + '</span>' +
                (p.content ? '<span class="projects-item-excerpt">' + escapeHtml(p.content) + '</span>' : '') +
                '<div class="projects-item-stats">' + statsHtml + '</div>' +
                '</div>' +
                '</a>';
        });

        html += '</div>';
        bodyEl.innerHTML = html;
    }

    window.openProjectsModal = openProjects;
})();
