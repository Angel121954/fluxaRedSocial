(function () {
    'use strict';

    var backdrop = document.getElementById('followersModal');
    var closeBtn = document.getElementById('followersModalClose');
    var bodyEl = document.getElementById('followersModalBody');
    var titleEl = document.getElementById('followersModalTitle');
    var subtitleEl = document.getElementById('followersModalSubtitle');

    if (!backdrop) return;

    var currentUserId = null;

    function openModal(userId, type) {
        currentUserId = userId;

        var labels = {
            followers: { title: 'Seguidores', subtitle: 'Personas que siguen a este usuario' },
            following: { title: 'Siguiendo', subtitle: 'Personas a las que sigue este usuario' },
        };

        var label = labels[type] || labels.followers;
        titleEl.textContent = label.title;
        subtitleEl.textContent = label.subtitle;

        showLoading();
        backdrop.classList.add('is-open');
        lockBodyScroll();

        fetchUsers(userId, type);
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        unlockBodyScroll();
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
        var msg = type === 'followers'
            ? 'Este usuario aún no tiene seguidores.'
            : 'Este usuario aún no sigue a nadie.';
        bodyEl.innerHTML =
            '<div class="followers-empty">' +
            '<p>' + msg + '</p>' +
            '</div>';
    }

    function fetchUsers(userId, type) {
        var url = '/users/' + userId + '/' + type;
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

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
                var users = data[type];
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
        var html = '<div class="followers-list">';

        users.forEach(function (u) {
            var avatar = u.avatar_url
                ? '<img src="' + u.avatar_url + '" alt="' + u.name + '" class="followers-avatar-img" />'
                : '<span class="followers-avatar-letter">' + u.name.charAt(0).toUpperCase() + '</span>';

            html +=
                '<a href="/profile/' + u.username + '" class="followers-user">' +
                '<div class="followers-avatar">' + avatar + '</div>' +
                '<div class="followers-info">' +
                '<span class="followers-name">' + u.name + '</span>' +
                '<span class="followers-handle">@' + u.username + '</span>' +
                '</div>' +
                '</a>';
        });

        html += '</div>';
        bodyEl.innerHTML = html;
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop.classList.contains('is-open')) {
            closeModal();
        }
    });

    window.openFollowersModal = openModal;

})();
