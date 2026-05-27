<div class="diary-report-backdrop" id="diaryReportModal">
    <div class="diary-report-modal">
        <div class="diary-report-header">
            <div class="diary-report-header-text">
                <h3 class="diary-report-title">Reportar respuesta</h3>
                <p class="diary-report-subtitle">Ayúdanos a mantener Fluxa seguro</p>
            </div>
            <button class="diary-report-close" id="diaryReportClose" aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="diary-report-body">
            <form id="diaryReportForm">
                <textarea
                    class="diary-report-textarea"
                    id="diaryReportReason"
                    placeholder="Explica el motivo del reporte (mínimo 10 caracteres)"
                    rows="4"
                    required
                    minlength="10"></textarea>
            </form>
        </div>

        <div class="diary-report-footer">
            <button type="button" class="diary-report-btn diary-report-btn--cancel" id="diaryReportCancel">Cancelar</button>
            <button type="submit" class="diary-report-btn diary-report-btn--submit" form="diaryReportForm" id="diaryReportSubmit">Reportar</button>
        </div>
    </div>
</div>
