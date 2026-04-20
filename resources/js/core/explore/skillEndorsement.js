import { showToast } from '../../shared/toast.js';

const SKILLS = {
    technical_communication: { label: 'Comunicación Técnica', color: '#3B82F6' },
    logical_thinking: { label: 'Pensamiento Lógico', color: '#8B5CF6' },
    collaboration: { label: 'Colaboración', color: '#10B981' },
    architecture: { label: 'Arquitectura', color: '#F59E0B' },
    leadership: { label: 'Liderazgo', color: '#EF4444' },
};

const SKILL_ICONS = {
    technical_communication: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>`,
    logical_thinking: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m0 0a8.001 8.001 0 011.682 7.337l-.582-.581m0 0L12 12m-6.418-6.418a8 8 0 0111.764 0l.582.581M12 12v5.418"/> `,
    collaboration: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H8m0 0H3v-2a3 3 0 015.356-1.857M3 20h5v-2a3 3 0 00-5.356-1.857M10 6V5a2 2 0 00-4 0v1m4 0a2 2 0 014 0v1m-6 4h6"/>`,
    architecture: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 12a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM4 19a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2z"/>`,
    leadership: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/> `,
};

function createPanel(projectId, existingSkill) {
    const panel = document.createElement('div');
    panel.className = 'skill-panel';
    panel.dataset.projectId = projectId;

    Object.entries(SKILLS).forEach(([key, skill]) => {
        const btn = document.createElement('button');
        btn.className = 'skill-btn' + (existingSkill === key ? ' active' : '');
        btn.dataset.skillType = key;
        btn.style.setProperty('--skill-color', skill.color);
        btn.title = skill.label;

        btn.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                ${SKILL_ICONS[key]}
            </svg>
            <span class="skill-tooltip">${skill.label}</span>
        `;

        panel.appendChild(btn);
    });

    return panel;
}

function showPanel(wrapper) {
    const panel = wrapper.querySelector('.skill-panel');
    if (panel && !panel.classList.contains('show')) {
        panel.classList.add('show');
    }
}

export function initSkillEndorsement() {
    document.addEventListener('click', async (e) => {
        const skillBtn = e.target.closest('.skill-btn');
        if (skillBtn) {
            e.preventDefault();
            e.stopPropagation();
            const panel = skillBtn.closest('.skill-panel');
            const projectId = panel.dataset.projectId;
            const skillType = skillBtn.dataset.skillType;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const wrapper = panel.closest('.endorsement-wrapper');

            const countEl = wrapper.querySelector('.endorsement-count');
            const prevCount = parseInt(countEl?.textContent || '0', 10);

            const prevActive = panel.querySelector('.skill-btn.active');
            const wasActive = prevActive?.dataset.skillType === skillType;
            const skillColor = skillBtn.style.getPropertyValue('--skill-color');

            if (prevActive && !wasActive) {
                prevActive.classList.remove('active');
            }
            const isNowActive = !wasActive;
            panel.querySelectorAll('.skill-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            skillBtn.classList.toggle('active', isNowActive);

            if (wrapper) {
                if (isNowActive) {
                    wrapper.classList.add('active');
                    wrapper.style.setProperty('--skill-color', skillColor);
                } else {
                    wrapper.classList.remove('active');
                    wrapper.style.removeProperty('--skill-color');
                }
            }

            try {
                const res = await fetch(`/projects/${projectId}/endorse`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ skill_type: skillType }),
                });

                const data = await res.json();

                if (!res.ok) {
                    skillBtn.classList.toggle('active', wasActive);
                    if (prevActive) prevActive.classList.add('active');
                    if (!wasActive) wrapper.classList.remove('active');
                    showToast(data.message || 'No se pudo actualizar el endorsement', 'error');
                    return;
                }

                if (data.skill_counts) {
                    const total = Object.values(data.skill_counts).reduce((sum, val) => sum + (val || 0), 0);
                    countEl.textContent = total;
                }

                panel.querySelectorAll('.skill-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.skillType === data.user_endorsement);
                });

                if (data.user_endorsement) {
                    wrapper.classList.add('active');
                    wrapper.style.setProperty('--skill-color', SKILLS[data.user_endorsement]?.color || '#6B7280');
                } else {
                    wrapper.classList.remove('active');
                    wrapper.style.removeProperty('--skill-color');
                }
            } catch (err) {
                skillBtn.classList.toggle('active', wasActive);
                if (prevActive) prevActive.classList.add('active');
                if (!wasActive) wrapper.classList.remove('active');
                showToast('Error de conexión. Inténtalo de nuevo.', 'error');
                countEl.textContent = prevCount;
            }
        }

        const endorsementBtn = e.target.closest('.endorsement-btn');
        if (endorsementBtn) {
            e.preventDefault();
            e.stopPropagation();
            const wrapper = endorsementBtn.closest('.endorsement-wrapper');
            const panel = wrapper.querySelector('.skill-panel');
            panel.classList.toggle('show');
            return;
        }

        document.querySelectorAll('.skill-panel.show').forEach(p => p.classList.remove('show'));
    });
}

export { SKILLS };