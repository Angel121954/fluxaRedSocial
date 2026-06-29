/* ══════════════════════════════════════════════════════════
   cv.js  —  CV / Perfil profesional
   Depende de: SortableJS (cargado vía CDN antes de este script)
══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {


    /* ─── 1. Selección de formatos ─────────────────────── */
    const formatCards = document.querySelectorAll('.cv-format-card');
    const formatRadios = document.querySelectorAll('.cv-format-radio');
    const formatPreviews = document.querySelectorAll('.cv-preview-format');

    function showPreview(format) {
        formatPreviews.forEach(el => {
            el.style.display = el.dataset.format === format ? '' : 'none';
        });
    }

    formatCards.forEach(card => {
        card.addEventListener('click', () => {
            formatCards.forEach(c => c.classList.remove('cv-format-card--selected'));
            card.classList.add('cv-format-card--selected');
        });
    });

    /* ─── 2. Checkboxes de contenido ────────────────────── */
    const checkboxes = document.querySelectorAll('.cv-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => handleCheckboxChange(checkbox));
    });

    function handleCheckboxChange(checkbox) {
        const name = checkbox.id;
        const checked = checkbox.checked;

        if (name === 'show_photo') {
            const avatar = document.getElementById('cvpAvatar');
            if (avatar) {
                avatar.style.display = checked ? '' : 'none';
            }
        }

        if (name === 'show_projects') {
            const picker = document.getElementById('cvProjectPicker');
            if (picker) {
                picker.style.display = checked ? '' : 'none';
            }
        }

        const sectionMap = {
            show_experience: 'cvpExperience',
            show_projects: 'cvpProjects',
            show_education: 'cvpEducation',
            show_skills: 'cvpSkills',
        };

        if (sectionMap[name]) {
            const section = document.getElementById(sectionMap[name]);
            if (section) section.style.display = checked ? '' : 'none';
        }
    }

    /* ─── 4. Botones de descarga: actualizar según formato seleccionado ── */
    const selectedFormat = document.querySelector('.cv-format-radio:checked')?.value || 'pdf';
    const downloadBtns = {
        pdf: document.getElementById('downloadPdfBtn'),
        ats: document.getElementById('downloadAtsBtn'),
        json: document.getElementById('downloadJsonBtn'),
    };

    function highlightDownloadBtn(format) {
        Object.values(downloadBtns).forEach(btn => {
            if (btn) btn.classList.remove('cv-download-btn--active');
        });
        if (downloadBtns[format]) {
            downloadBtns[format].classList.add('cv-download-btn--active');
        }
    }

    highlightDownloadBtn(selectedFormat);
    showPreview(selectedFormat);

    formatRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            highlightDownloadBtn(radio.value);
            showPreview(radio.value);
        });
    });

    /* ─── 5. SortableJS: orden de secciones ─────────────── */
    const sortableList = document.getElementById('cvSortable');
    const orderContainer = document.getElementById('sectionsOrderInputs');
    const previewSections = document.getElementById('cvpSections');

    if (sortableList && typeof Sortable !== 'undefined') {
        Sortable.create(sortableList, {
            animation: 180,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            handle: '.cv-sortable__item',
            onEnd() {
                syncOrder();
            },
        });
    }

    function syncOrder() {
        const items = sortableList.querySelectorAll('.cv-sortable__item');
        const order = Array.from(items).map(el => el.dataset.section);

        if (orderContainer) {
            orderContainer.innerHTML = '';
            order.forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'section_order[]';
                input.value = key;
                orderContainer.appendChild(input);
            });
        }

        [previewSections, document.getElementById('cvpSectionsAts')].forEach(container => {
            if (!container) return;
            order.forEach(key => {
                const sec = container.querySelector(`[data-section="${key}"]`);
                if (sec) container.appendChild(sec);
            });
        });
    }

    /* ─── 6. Modal: selección de proyectos ─────────────── */
    const MAX_PROJECTS = 3;
    const modal = document.getElementById('cvProjectModal');
    const modalBtn = document.getElementById('cvProjectPickerBtn');
    const modalConfirm = document.getElementById('cvProjectConfirm');
    const projectGrid = document.getElementById('cvProjectGrid');
    const projectCount = document.getElementById('cvProjectCount');
    const projectSummary = document.getElementById('cvProjectSummary');
    const projectInputs = document.getElementById('selectedProjectInputs');

    let selectedIds = [];

    function loadSelectedIds() {
        selectedIds = [];
        if (projectInputs) {
            projectInputs.querySelectorAll('input[name="selected_project_ids[]"]').forEach(inp => {
                const val = parseInt(inp.value, 10);
                if (!isNaN(val)) selectedIds.push(val);
            });
        }
    }

    function updateModalSelection() {
        if (!projectGrid) return;

        const cards = projectGrid.querySelectorAll('.cv-project-card');
        cards.forEach(card => {
            const id = parseInt(card.dataset.projectId, 10);
            const isSel = selectedIds.includes(id);
            card.classList.toggle('cv-project-card--selected', isSel);

            const isDisabled = !isSel && selectedIds.length >= MAX_PROJECTS;
            card.classList.toggle('cv-project-card--disabled', isDisabled);
        });

        if (projectCount) {
            projectCount.textContent = `${selectedIds.length}/${MAX_PROJECTS}`;
        }
    }

    function toggleProject(id) {
        const idx = selectedIds.indexOf(id);
        if (idx !== -1) {
            selectedIds.splice(idx, 1);
        } else if (selectedIds.length < MAX_PROJECTS) {
            selectedIds.push(id);
        }
        updateModalSelection();
    }

    function saveSelection() {
        if (projectInputs) {
            projectInputs.innerHTML = '';
            selectedIds.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'selected_project_ids[]';
                inp.value = id;
                projectInputs.appendChild(inp);
            });
        }

        if (projectSummary) {
            projectSummary.textContent = selectedIds.length > 0
                ? `${selectedIds.length}/${MAX_PROJECTS} seleccionados`
                : 'Los 3 más recientes';
        }

        persistSelection(selectedIds);

        closeModal();
    }

    function persistSelection(ids) {
        const token = document.querySelector('input[name="_token"]')?.value;
        if (!token) return;

        const fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('_token', token);
        ids.forEach(id => fd.append('selected_project_ids[]', id));

        fetch('/settings/cv/projects', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
        }).catch(() => {});
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('is-open');
            unlockBodyScroll();
        }
    }

    function openModal() {
        loadSelectedIds();
        updateModalSelection();
        if (modal) {
            modal.classList.add('is-open');
            lockBodyScroll();
        }
    }

    // Open modal button
    if (modalBtn) {
        modalBtn.addEventListener('click', openModal);
    }

    // Confirm button
    if (modalConfirm) {
        modalConfirm.addEventListener('click', saveSelection);
    }

    // Click on project card
    if (projectGrid) {
        projectGrid.addEventListener('click', e => {
            const card = e.target.closest('.cv-project-card');
            if (!card) return;
            if (card.classList.contains('cv-project-card--disabled')) return;
            toggleProject(parseInt(card.dataset.projectId, 10));
        });
    }

    // Close via data-close attribute (shared modal pattern)
    document.addEventListener('click', e => {
        const closeBtn = e.target.closest('[data-close="cvProjectModal"]');
        if (closeBtn) {
            closeModal();
        }
    });

    // Close on backdrop click
    if (modal) {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });

        // Close on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });
    }

    /* ─── 7. Inicializar al cargar ───────────────────────── */
    function initPreview() {
        checkboxes.forEach(c => handleCheckboxChange(c));
        syncOrder();
    }

    initPreview();
});
