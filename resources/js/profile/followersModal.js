(function () {
    'use strict';

    function escapeHtml(str) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(str ?? '').replace(/[&<>"']/g, function(c) { return map[c]; });
    }

    const backdrop = document.getElementById('followersModal');
    const bodyEl = document.getElementById('followersModalBody');
    const titleEl = document.getElementById('followersModalTitle');
    const subtitleEl = document.getElementById('followersModalSubtitle');

    if (!backdrop) return;

    let currentUserId = null;

    function openFollowers(userId, type) {
        currentUserId = userId;

        const labels = {
            followers: { title: 'Seguidores', subtitle: 'Personas que siguen a este usuario' },
            following: { title: 'Siguiendo', subtitle: 'Personas a las que sigue este usuario' },
        };

        const label = labels[type] || labels.followers;
        titleEl.textContent = label.title;
        subtitleEl.textContent = label.subtitle;

        showLoading();
        window.openModal('followersModal');

        fetchUsers(userId, type);
    }

    function closeFollowers() {
        window.closeModal('followersModal');
        bodyEl.innerHTML = '';
    }

    function showLoading() {
        bodyEl.innerHTML =
            '<div class="followers-loading">' +
            '<div class="followers-skeleton"></div>' +
            '<div class="followers-skeleton"></div>' +
            '<div class="followers-skeleton"></div>' +
            '</div>';
    }

    function showError() {
        bodyEl.innerHTML =
            '<div class="followers-empty">' +
            '<p>No se pudo cargar la lista. Intenta de nuevo.</p>' +
            '</div>';
    }

    function showEmpty(type) {
        const msg = type === 'followers'
            ? 'Este usuario aún no tiene seguidores.'
            : 'Este usuario aún no sigue a nadie.';
        bodyEl.innerHTML =
            '<div class="followers-empty">' +
            '<p>' + msg + '</p>' +
            '</div>';
    }

    function fetchUsers(userId, type) {
        const url = '/users/' + userId + '/' + type;
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
                const users = data[type];
                if (!users || users.length === 0) {
                    showEmpty(type);
                    return;
                }
                renderUsers(users);
            })
            .catch(function () {
                showError();
            });
    }

    function renderUsers(users) {
        let html = '<div class="followers-list">';

        users.forEach(function (u) {
            const avatar = u.avatar_url
                ? '<img src="' + escapeHtml(u.avatar_url) + '" alt="' + escapeHtml(u.name) + '" class="followers-avatar-img" />'
                : '<span class="followers-avatar-letter">' + escapeHtml(u.name.charAt(0).toUpperCase()) + '</span>';

            html +=
                '<a href="/profile/' + escapeHtml(u.username) + '" class="followers-user">' +
                '<div class="followers-avatar">' + avatar + '</div>' +
                '<div class="followers-info">' +
                '<span class="followers-name">' + escapeHtml(u.name) + '</span>' +
                '<span class="followers-handle">@' + escapeHtml(u.username) + '</span>' +
                '</div>' +
                '</a>';
        });

        html += '</div>';
        bodyEl.innerHTML = html;
    }

    window.openFollowersModal = openFollowers;
})();
