<x-modal id="reportModal" title="Reportar proyecto" subtitle="Ayúdanos a mantener Fluxa seguro">
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

    <x-slot:footer>
        <button type="button" class="btn btn-secondary" data-close="reportModal">Cancelar</button>
        <button type="submit" class="btn btn-primary" style="background:#ef4444;" form="reportForm">Reportar</button>
    </x-slot:footer>
</x-modal>
