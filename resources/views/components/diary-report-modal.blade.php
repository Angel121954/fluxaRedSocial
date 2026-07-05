<x-modal id="diaryReportModal" title="Reportar respuesta" subtitle="Ayúdanos a mantener Fluxa seguro">
    <form id="diaryReportForm">
        <textarea
            class="modal-report-textarea"
            id="diaryReportReason"
            placeholder="Explica el motivo del reporte (mínimo 10 caracteres)"
            rows="4"
            required
            minlength="10"></textarea>
    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-secondary" data-close="diaryReportModal">Cancelar</button>
        <button type="submit" class="btn btn-primary" style="background:#ef4444;" form="diaryReportForm" id="diaryReportSubmit">Reportar</button>
    </x-slot:footer>
</x-modal>
