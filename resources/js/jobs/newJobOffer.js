/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/newJobOffer.js
   Modal: Publicar oferta de empleo
   Sigue el patrón de newProject.js (abrirModal/cerrarModal)
════════════════════════════════════════════════════════════ */

(() => {

const el = id => document.getElementById(id);

if (!el('jobOfferOverlay')) return;

/* ── Abrir / Cerrar / Resetear ──────────────────── */
const abrirJobOffer = () => {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = scrollbarWidth + 'px';

    el('jobOfferOverlay').classList.add('open');
    setTimeout(() => { const t = el('jo-title'); if (t) t.focus(); }, 50);
};

const cerrarJobOffer = () => {
    el('jobOfferOverlay').classList.remove('open');
    setTimeout(() => {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 250);
};

const resetearJobOffer = () => {
    el('jobOfferForm').reset();
    el('jo-success').style.display = 'none';
    el('jo-error-banner').style.display = 'none';
    el('jo-error-list').innerHTML = '';
    document.querySelectorAll('.jo-field-error').forEach(s => s.style.display = 'none');
    el('btnPublishJobOffer').disabled = false;
    el('btnPublishJobOffer').textContent = 'Publicar oferta';
};

/* ── Errores ─────────────────────────────────────── */
const ocultarErrores = () => {
    el('jo-error-banner').style.display = 'none';
    el('jo-error-list').innerHTML = '';
    document.querySelectorAll('.jo-field-error').forEach(s => s.style.display = 'none');
};

const mostrarErrorServidor = (mensajes) => {
    el('jo-error-list').innerHTML = mensajes.map(m => `<li>${m}</li>`).join('');
    el('jo-error-banner').style.display = 'block';
    el('jo-error-banner').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
};

const mostrarErrorCampo = (fieldId, mensaje) => {
    const span = el(`jo-${fieldId}-err`);
    if (span) {
        span.textContent = mensaje;
        span.style.display = 'block';
    }
};

/* ── Envío del formulario ────────────────────────── */
const publicarJobOffer = () => {
    ocultarErrores();

    const boton = el('btnPublishJobOffer');
    boton.disabled = true;
    boton.textContent = 'Publicando...';

    const datos = new FormData(el('jobOfferForm'));

    fetch(el('jobOfferForm').action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: datos,
    })
        .then(respuesta => respuesta.json().then(data => ({ status: respuesta.status, data })))
        .then(({ status, data }) => {
            if (status === 422) {
                const errors = data.errors || {};
                mostrarErrorServidor(Object.values(errors).flat());
                Object.entries(errors).forEach(([campo, msgs]) => {
                    mostrarErrorCampo(campo, msgs[0]);
                });
                boton.disabled = false;
                boton.textContent = 'Publicar oferta';
                return;
            }

            if (!data.success) {
                mostrarErrorServidor([data.message || 'Ocurrió un error inesperado.']);
                boton.disabled = false;
                boton.textContent = 'Publicar oferta';
                return;
            }

            /* Éxito — renderizar en tiempo real */
            const list = document.getElementById('jobsList');
            if (list && data.html) {
                const temp = document.createElement('div');
                temp.innerHTML = data.html;
                const card = temp.querySelector('.job-card');
                if (card) {
                    const emptyState = list.querySelector('.jobs-empty');
                    if (emptyState) {
                        list.innerHTML = '';
                    }
                    list.insertAdjacentElement('afterbegin', card);
                }
            }

            el('jobOfferForm').style.display = 'none';
            el('jo-success').style.display = 'block';
            boton.disabled = false;
            boton.textContent = 'Publicar oferta';

            setTimeout(() => {
                cerrarJobOffer();
                setTimeout(() => {
                    el('jobOfferForm').style.display = '';
                    resetearJobOffer();
                }, 300);
            }, 2200);
        })
        .catch(() => {
            mostrarErrorServidor(['Error de conexión. Inténtalo de nuevo.']);
            boton.disabled = false;
            boton.textContent = 'Publicar oferta';
        });
};

/* ── Eventos ─────────────────────────────────────── */
document.addEventListener('click', function (e) {
    if (e.target === el('jobOfferOverlay')) cerrarJobOffer();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarJobOffer();
});

/* ── Exports ─────────────────────────────────────── */
window.abrirJobOffer = abrirJobOffer;
window.cerrarJobOffer = cerrarJobOffer;
window.publicarJobOffer = publicarJobOffer;

})();
