(function () {
    'use strict';

    const backdrop = document.getElementById('stackModal');
    const saveBtn = document.getElementById('stackModalSave');
    const searchInput = document.getElementById('stackModalSearch');
    const grid = document.getElementById('stackModalGrid');

    if (!backdrop) return;

    function openStack() {
        window.openModal('stackModal');
        setTimeout(function () {
            if (searchInput) searchInput.focus();
        }, 100);
    }

    function closeStack() {
        window.closeModal('stackModal');
        if (searchInput) searchInput.value = '';
        filterItems('');
    }

    function filterItems(query) {
        if (!grid) return;
        const items = grid.querySelectorAll('.stack-modal-item');
        const q = query.toLowerCase();
        items.forEach(function (item) {
            const name = item.getAttribute('data-name') || '';
            item.style.display = name.includes(q) ? '' : 'none';
        });
    }

    function saveTechnologies() {
        if (!grid) return;
        const checked = grid.querySelectorAll('input[type="checkbox"]:checked');
        const ids = Array.from(checked).map(function (cb) {
            return cb.value;
        });

        saveBtn.disabled = true;
        saveBtn.textContent = 'Guardando...';

        fetch('/profile/technologies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ technologies: ids }),
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Error al guardar');
            return res.json();
        })
        .then(function () {
            closeStack();
            window.location.href = '/profile?tab=stack';
        })
        .catch(function () {
            if (typeof window.showToast === 'function') {
                window.showToast('No se pudieron guardar las tecnologías', 'error');
            }
        })
        .finally(function () {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Guardar cambios';
        });
    }

    if (saveBtn) saveBtn.addEventListener('click', saveTechnologies);

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            filterItems(this.value);
        });
    }

    window.openStackModal = openStack;
})();
