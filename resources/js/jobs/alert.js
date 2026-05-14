/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/alert.js
   Modal de "Crear alerta de empleo"
   Depende de: SweetAlert2 (cargado en el layout)
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    const btnCreate = document.getElementById('btnCreateAlert');
    if (!btnCreate) return;

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

    btnCreate.addEventListener('click', async () => {
        const { value: formData, isConfirmed } = await Swal.fire({
            title: 'Crear alerta de empleo',
            html: `
                <div style="text-align:left;display:flex;flex-direction:column;gap:.75rem;margin-top:.5rem">
                    <div>
                        <label style="font-size:13px;font-weight:500;color:#374151;display:block;margin-bottom:4px">
                            Palabras clave
                        </label>
                        <input id="swal-keyword" class="swal2-input" placeholder="Ej: React, Laravel, Node.js"
                               style="margin:0;width:100%;box-sizing:border-box">
                    </div>
                    <div>
                        <label style="font-size:13px;font-weight:500;color:#374151;display:block;margin-bottom:4px">
                            Modalidad
                        </label>
                        <select id="swal-modality" class="swal2-input" style="margin:0;width:100%;box-sizing:border-box">
                            <option value="">Cualquier modalidad</option>
                            <option value="remoto">Remoto</option>
                            <option value="hibrido">Híbrido</option>
                            <option value="presencial">Presencial</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:13px;font-weight:500;color:#374151;display:block;margin-bottom:4px">
                            Frecuencia de notificación
                        </label>
                        <select id="swal-freq" class="swal2-input" style="margin:0;width:100%;box-sizing:border-box">
                            <option value="instant">Inmediata</option>
                            <option value="daily">Diaria</option>
                            <option value="weekly">Semanal</option>
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#12b3b6',
            cancelButtonColor:  '#6b7280',
            confirmButtonText:  'Crear alerta',
            cancelButtonText:   'Cancelar',
            focusConfirm: false,
            preConfirm: () => ({
                keyword:  document.getElementById('swal-keyword')?.value.trim(),
                modality: document.getElementById('swal-modality')?.value,
                freq:     document.getElementById('swal-freq')?.value,
            }),
        });

        if (!isConfirmed || !formData) return;

        try {
            const res = await fetch('/jobs/alerts', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData),
            });

            if (!res.ok) throw new Error();

            window.toast?.success('¡Alerta creada! Te notificaremos por email.');

        } catch {
            window.toast?.error('No se pudo crear la alerta. Intenta de nuevo.');
        }
    });
});
