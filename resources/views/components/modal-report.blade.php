<!-- ══════════════════════════════════════════
     MODAL DE REPORTAR PROYECTO
═════════════════════════════════════════ -->
<div class="modal-backdrop" id="reportModal">
    <div class="modal-report">
        <div class="modal-report-header">
            <h3>Reportar proyecto</h3>
            <button class="modal-close-btn" data-close="reportModal">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="modal-report-desc">¿Por qué quieres reportar este proyecto?</p>
        <form id="reportForm">
            <textarea 
                class="modal-report-textarea" 
                id="reportReason"
                placeholder="Explica el motivo del reporte (mínimo 10 caracteres)"
                rows="4"
                required
                minlength="10"></textarea>
            <div class="modal-report-actions">
                <button type="button" class="btn-cancel-report" data-close="reportModal">Cancelar</button>
                <button type="submit" class="btn-submit-report">Reportar</button>
            </div>
        </form>
    </div>
</div>
