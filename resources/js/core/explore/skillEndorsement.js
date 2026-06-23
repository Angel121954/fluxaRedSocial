import { showToast } from '../../shared/toast.js';

export function initSkillEndorsement() {
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.endorsement-btn');
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();

            const wrap = toggleBtn.closest('.endorsement-wrap');
            const panel = wrap?.querySelector('.skill-panel');
            if (!panel) return;

            document.querySelectorAll('.skill-panel.show').forEach(p => {
                if (p !== panel) p.classList.remove('show');
            });

            panel.classList.toggle('show');
            return;
        }

        const skillBtn = e.target.closest('.skill-btn');
        if (skillBtn) {
            e.preventDefault();
            e.stopPropagation();

            const panel = skillBtn.closest('.skill-panel');
            const projectId = panel.dataset.projectId;
            const skillType = skillBtn.dataset.skillType;
            const wrap = panel.closest('.endorsement-wrap');
            const countEl = wrap?.querySelector('.endorsement-count');

            panel.classList.remove('show');

            toggleEndorsement(projectId, skillType, skillBtn, panel, countEl);
            return;
        }

        if (!e.target.closest('.endorsement-wrap')) {
            document.querySelectorAll('.skill-panel.show').forEach(p => p.classList.remove('show'));
        }
    });
}

async function toggleEndorsement(projectId, skillType, skillBtn, panel, countEl) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const endorsementBtn = panel.closest('.endorsement-wrap')?.querySelector('.endorsement-btn');

    // ── Snapshot estado actual para revertir ──
    const prevActiveBtn = panel.querySelector('.skill-btn.active');
    const prevActiveType = prevActiveBtn?.dataset.skillType;
    const prevEndorsementActive = endorsementBtn?.classList.contains('active');
    const prevCount = parseInt(countEl?.textContent || '0', 10);

    // ── Optimistic: toggle skill button ──
    const wasActive = skillBtn.classList.contains('active');
    skillBtn.classList.toggle('active');

    if (wasActive) {
        // Quitando endorsement: bajar contador, desactivar endorsement-btn
        countEl.textContent = Math.max(0, prevCount - 1);
        endorsementBtn?.classList.remove('active');
    } else {
        // Agregando endorsement:
        // si había otro skill activo, desactivarlo (sin cambiar contador, ya estaba contado)
        if (prevActiveBtn && prevActiveType !== skillType) {
            prevActiveBtn.classList.remove('active');
        } else if (!prevActiveBtn) {
            // Nuevo endorsement: subir contador, activar endorsement-btn
            countEl.textContent = prevCount + 1;
            endorsementBtn?.classList.add('active');
        }
    }

    try {
        const res = await fetch(`/projects/${projectId}/endorse`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ skill_type: skillType }),
        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || 'No se pudo actualizar');
        }

        // ── Confirmar con datos del servidor ──
        panel.querySelectorAll('.skill-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.skillType === data.user_endorsement);
        });

        if (data.skill_counts) {
            const total = Object.values(data.skill_counts).reduce((sum, val) => sum + (val || 0), 0);
            countEl.textContent = total;
        }

        if (data.user_endorsement) {
            endorsementBtn?.classList.add('active');
        } else {
            endorsementBtn?.classList.remove('active');
        }
    } catch (e) {
        // ── Revertir al estado anterior ──
        skillBtn.classList.toggle('active');
        countEl.textContent = prevCount;
        if (prevEndorsementActive) {
            endorsementBtn?.classList.add('active');
        } else {
            endorsementBtn?.classList.remove('active');
        }
        if (prevActiveBtn && prevActiveType !== skillType) {
            prevActiveBtn.classList.add('active');
        }
        showToast(e.message || 'Error de conexión. Inténtalo de nuevo.', 'error');
    }
}
