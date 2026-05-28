(function () {
  'use strict';

  const IMPORT_LIST_KEY = 'githubImportList';
  const IMPORT_LOADING_KEY = 'githubImportStatus';
  const IMPORT_ERROR_KEY = 'githubImportError';
  const IMPORT_EMPTY_KEY = 'githubImportEmpty';
  const IMPORT_NO_TOKEN_KEY = 'githubImportNoToken';
  const MODAL_ID = 'githubImportModal';
  const CLOSE_BTN_ID = 'githubModalClose';

  const $ = (id) => document.getElementById(id);
  const DATA = document.getElementById('btnImportGitHub')?.dataset || {};
  const API_REPOS_URL = DATA.apiReposUrl || '/api/github/repos';
  const API_IMPORT_URL = DATA.apiImportUrl || '/api/github/repos/import';
  const CONNECT_URL = DATA.connectUrl || '/auth/github/connect';

  function showOnly() {
    for (let i = 0; i < arguments.length; i++) {
      const el = arguments[i];
      if (!el) continue;
      el.classList.remove('hidden');
    }
    const all = [
      $(IMPORT_LIST_KEY),
      $(IMPORT_LOADING_KEY),
      $(IMPORT_ERROR_KEY),
      $(IMPORT_EMPTY_KEY),
      $(IMPORT_NO_TOKEN_KEY),
    ];
    for (let i = 0; i < all.length; i++) {
      if (!all[i]) continue;
      if (arguments.length === 0 || Array.from(arguments).indexOf(all[i]) === -1) {
        all[i].classList.add('hidden');
      }
    }
  }

  function showToast(msg, type) {
    const fn = type === 'error' ? 'error' : 'success';
    window.toast?.[fn](msg);
  }

  function openModal() {
    const modal = $(MODAL_ID);
    if (!modal) return;
    modal.classList.add('show');
    lockBodyScroll();

    showOnly($(IMPORT_LOADING_KEY));
    $(IMPORT_ERROR_KEY).textContent = '';

    fetch(API_REPOS_URL)
      .then(function (r) {
        if (!r.ok) {
          if (r.status === 401) {
            showOnly($(IMPORT_NO_TOKEN_KEY));
            return null;
          }
          throw new Error('Error del servidor');
        }
        return r.json();
      })
      .then(function (data) {
        if (!data) return;
        if (!data.success) {
          $(IMPORT_ERROR_KEY).textContent = data.message || 'Error al cargar repositorios.';
          showOnly($(IMPORT_ERROR_KEY));
          return;
        }

        const repos = data.repos;
        if (!repos || repos.length === 0) {
          showOnly($(IMPORT_EMPTY_KEY));
          return;
        }

        renderRepos(repos);
        showOnly($(IMPORT_LIST_KEY));
      })
      .catch(function (err) {
        $(IMPORT_ERROR_KEY).textContent = 'No se pudieron cargar los repositorios. Verifica tu conexión e intenta de nuevo.';
        showOnly($(IMPORT_ERROR_KEY));
      });
  }

  function closeModal() {
    const modal = $(MODAL_ID);
    if (!modal) return;
    modal.classList.remove('show');
    unlockBodyScroll();
  }

  function renderRepos(repos) {
    const container = $(IMPORT_LIST_KEY);
    if (!container) return;
    container.innerHTML = '';

    for (let i = 0; i < repos.length; i++) {
      const repo = repos[i];
      const item = document.createElement('div');
      item.className = 'github-repo-item';
      item.setAttribute('data-full-name', repo.full_name);
      item.setAttribute('role', 'button');
      item.setAttribute('tabindex', '0');

      item.innerHTML =
        '<div class="github-repo-icon">' +
        '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" width="20" height="20">' +
        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844a9.59 9.59 0 012.504.338c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.522 2 12 2z" />' +
        '</svg></div>' +
        '<div class="github-repo-info">' +
        '<span class="github-repo-name">' + repo.name + '</span>' +
        '<span class="github-repo-desc">' + (repo.description || 'Sin descripción') + '</span>' +
        '</div>' +
        '<div class="github-repo-meta">' +
        (repo.language ? '<span class="github-repo-lang">' + repo.language + '</span>' : '') +
        '<button class="github-import-btn" data-full-name="' + repo.full_name + '">Importar</button>' +
        '</div>';

      container.appendChild(item);
    }

    container.addEventListener('click', function (e) {
      const btn = e.target.closest('.github-import-btn');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();
      const fullName = btn.getAttribute('data-full-name');
      if (!fullName) return;
      importRepo(fullName);
    });
  }

  function importRepo(fullName) {
    const btn = document.querySelector(`.github-import-btn[data-full-name="${fullName}"]`);
    if (!btn) return;
    btn.disabled = true;
    btn.textContent = 'Importando...';

    fetch(API_IMPORT_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({ full_name: fullName }),
    })
      .then(function (r) {
        if (!r.ok) {
          return r.json().then(function (d) {
            throw new Error(d.message || 'Error del servidor');
          });
        }
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          showToast(data.message, 'success');
          closeModal();
        } else {
          showToast(data.message || 'Error al importar el repositorio.', 'error');
          resetImportBtns();
        }
      })
      .catch(function (err) {
        showToast(err.message || 'Error de conexión.', 'error');
        resetImportBtns();
      });
  }

  function resetImportBtns() {
    const btns = document.querySelectorAll('.github-import-btn');
    for (let i = 0; i < btns.length; i++) {
      btns[i].disabled = false;
      btns[i].textContent = 'Importar';
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnImportGitHub');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const hasToken = btn.getAttribute('data-has-token');
      if (hasToken === 'true') {
        openModal();
      } else {
        window.location.href = CONNECT_URL;
      }
    });

    const closeBtn = $(CLOSE_BTN_ID);
    if (closeBtn) {
      closeBtn.addEventListener('click', closeModal);
    }

    const modal = $(MODAL_ID);
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) {
          closeModal();
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
          closeModal();
        }
      });
    }

    // open automatically if ?github_import=1
    const params = new URLSearchParams(window.location.search);
    if (params.get('github_import') === '1' && btn.getAttribute('data-has-token') === 'true') {
      openModal();
    }
  });
})();
