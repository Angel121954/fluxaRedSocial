<div class="modal-backdrop" id="reportModal">
    <div class="modal-card">
        {{-- Header --}}
        <div class="modal-header">
            <div>
                <div class="modal-title" id="reportModalTitle">Reportar proyecto</div>
                <div class="modal-subtitle">Ayúdanos a mantener Fluxa seguro</div>
            </div>
            <button class="modal-close" data-close="reportModal" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body">
            <p class="modal-report-desc" id="reportModalDesc" style="font-size:0.875rem;color:var(--ink-500);margin-bottom:1rem;">¿Por qué quieres reportar este proyecto?</p>
            <form id="reportForm">
                <textarea
                    class="modal-report-textarea"
                    id="reportReason"
                    placeholder="Explica el motivo del reporte (mínimo 10 caracteres)"
                    rows="4"
                    required
                    minlength="10"></textarea>
            </form>
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-close="reportModal">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="background:#ef4444;" form="reportForm">Reportar</button>
        </div>
    </div>
</div>