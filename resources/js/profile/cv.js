/* ══════════════════════════════════════════════════════════
   cv.js  —  CV / Perfil profesional
   Depende de: SortableJS (cargado vía CDN antes de este script)
══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    console.log('cv.js loaded');

    /* ─── 1. Selección de templates ─────────────────────── */
    const templateCards = document.querySelectorAll('.cv-template-card');

    templateCards.forEach(card => {
        card.addEventListener('click', () => {
            templateCards.forEach(c => c.classList.remove('cv-template-card--selected'));
            card.classList.add('cv-template-card--selected');

            const radio = card.querySelector('.cv-template-radio');
            if (radio) updatePreviewTemplate(radio.value);
        });
    });

    function updatePreviewTemplate(template) {
        const previewCard = document.getElementById('cvPreviewCard');
        if (!previewCard) return;

        previewCard.classList.remove('cvp--classic', 'cvp--modern', 'cvp--creative');
        previewCard.classList.add(`cvp--${template}`);

        const avatar = document.getElementById('cvpAvatar');
        const showPhotoCheckbox = document.getElementById('show_photo');

        if (avatar) {
            const showPhoto = showPhotoCheckbox ? showPhotoCheckbox.checked : true;
            avatar.style.display = (template === 'classic' || !showPhoto) ? 'none' : '';
        }
    }

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
            const selectedTpl = document.querySelector('.cv-template-radio:checked')?.value;
            if (avatar) {
                avatar.style.display = (checked && selectedTpl !== 'classic') ? '' : 'none';
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

    /* ─── 3. SortableJS: orden de secciones ─────────────── */
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

        // Sincronizar el orden en la vista previa
        if (previewSections) {
            order.forEach(key => {
                const sec = previewSections.querySelector(`[data-section="${key}"]`);
                if (sec) previewSections.appendChild(sec);
            });
        }
    }

    /* ─── 4. Inicializar al cargar ───────────────────────── */
    function initPreview() {
        const selectedTpl = document.querySelector('.cv-template-radio:checked')?.value ?? 'classic';
        updatePreviewTemplate(selectedTpl);
        checkboxes.forEach(c => handleCheckboxChange(c));
        syncOrder(); // genera los inputs hidden con el orden inicial también
    }

    initPreview();
});