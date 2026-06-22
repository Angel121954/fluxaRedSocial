/* ══════════════════════════════════════════════════════════
   cv.js  —  CV / Perfil profesional
   Depende de: SortableJS (cargado vía CDN antes de este script)
══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    console.log('cv.js loaded');

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

    console.log('sortableList:', sortableList);
    console.log('Sortable:', typeof Sortable);

    if (sortableList && typeof Sortable !== 'undefined') {
        console.log('Creating Sortable');
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

        // Regenerar inputs hidden como array section_order[]
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

        // Sincronizar el orden en ambas vistas previas
        [previewSections, document.getElementById('cvpSectionsAts')].forEach(container => {
            if (!container) return;
            order.forEach(key => {
                const sec = container.querySelector(`[data-section="${key}"]`);
                if (sec) container.appendChild(sec);
            });
        });
    }

    /* ─── 6. Inicializar al cargar ───────────────────────── */
    function initPreview() {
        checkboxes.forEach(c => handleCheckboxChange(c));
        syncOrder(); // genera los inputs hidden con el orden inicial también
    }

    initPreview();
});
