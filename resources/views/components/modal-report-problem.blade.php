<x-modal id="reportProblemModal" title="Reportar problema" subtitle="Cuéntanos qué está fallando para solucionarlo">
    <div class="modal-error-banner" id="reportProblemError" style="display:none">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span id="reportProblemErrorText">Ocurrió un error al enviar tu reporte.</span>
    </div>
    <form id="reportProblemForm">
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="reportProblemType" style="display:block; font-size:13px; font-weight:500; margin-bottom:6px; color: var(--text-primary);">
                Tipo de problema
            </label>
            <select
                id="reportProblemType"
                class="modal-report-select"
                required>
                <option value="" disabled selected>Selecciona una categoría</option>
                <option value="error_tecnico">Error técnico</option>
                <option value="contenido_inapropiado">Contenido inapropiado</option>
                <option value="problema_cuenta">Problema con mi cuenta</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reportProblemMessage" style="display:block; font-size:13px; font-weight:500; margin-bottom:6px; color: var(--text-primary);">
                Descripción
            </label>
            <textarea
                class="modal-report-textarea"
                id="reportProblemMessage"
                placeholder="Describe el problema detalladamente (mínimo 10 caracteres)"
                rows="5"
                required
                minlength="10"></textarea>
        </div>
    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-secondary" data-close="reportProblemModal">Cancelar</button>
        <button type="submit" class="btn btn-primary" form="reportProblemForm" id="reportProblemSubmit">Enviar reporte</button>
    </x-slot:footer>
</x-modal>
