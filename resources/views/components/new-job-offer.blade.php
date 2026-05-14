{{-- resources/views/components/new-job-offer.blade.php --}}

<div id="jobOfferOverlay" role="dialog" aria-modal="true" aria-labelledby="jobOfferTitle">
    <div id="jobOfferCard">

        {{-- ── Header ──────────────────────────────────────────── --}}
        <div id="jobOfferHeader">
            <div>
                <div id="jobOfferTitle">Publicar oferta de empleo</div>
                <div id="jobOfferSubtitle">Llega a miles de desarrolladores en Latinoamérica</div>
            </div>
            <button class="close-btn" onclick="cerrarJobOffer()" aria-label="Cerrar">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- ── Body ─────────────────────────────────────────────── --}}
        <div id="jobOfferBody">
            <form id="jobOfferForm">
                @csrf

                <div class="jo-field">
                    <label for="jo-title">Título del cargo <span class="req" aria-hidden="true">*</span></label>
                    <input id="jo-title" name="title" type="text" class="jo-input"
                        placeholder="Ej: Desarrollador Laravel Senior"
                        aria-required="true" maxlength="120" />
                </div>

                <div class="jo-field">
                    <label for="jo-description">Descripción <span class="req" aria-hidden="true">*</span></label>
                    <textarea id="jo-description" name="description" class="jo-input jo-textarea"
                        placeholder="Describe las responsabilidades, requisitos y lo que ofrecen..."
                        aria-required="true" rows="5" maxlength="2000"></textarea>
                </div>

                <div class="jo-row">
                    <div class="jo-field">
                        <label for="jo-modality">Modalidad <span class="req" aria-hidden="true">*</span></label>
                        <select id="jo-modality" name="modality" class="jo-input jo-select" aria-required="true">
                            <option value="remoto">Remoto</option>
                            <option value="hibrido">Híbrido</option>
                            <option value="presencial">Presencial</option>
                        </select>
                    </div>
                    <div class="jo-field">
                        <label for="jo-seniority">Seniority <span class="req" aria-hidden="true">*</span></label>
                        <select id="jo-seniority" name="seniority" class="jo-input jo-select" aria-required="true">
                            <option value="junior">Junior</option>
                            <option value="mid">Semi-Senior</option>
                            <option value="senior">Senior</option>
                            <option value="lead">Lead</option>
                        </select>
                    </div>
                </div>

                <div class="jo-field">
                    <label for="jo-location">Ubicación</label>
                    <input id="jo-location" name="location" type="text" class="jo-input"
                        placeholder="Ej: Bogotá, Colombia / Remoto" maxlength="100" />
                </div>

                <div class="jo-row jo-row--3">
                    <div class="jo-field">
                        <label for="jo-salary-min">Salario mín.</label>
                        <input id="jo-salary-min" name="salary_min" type="number" class="jo-input"
                            placeholder="1000" min="0" />
                    </div>
                    <div class="jo-field">
                        <label for="jo-salary-max">Salario máx.</label>
                        <input id="jo-salary-max" name="salary_max" type="number" class="jo-input"
                            placeholder="3000" min="0" />
                    </div>
                    <div class="jo-field">
                        <label for="jo-currency">Moneda</label>
                        <select id="jo-currency" name="currency" class="jo-input jo-select">
                            <option value="usd">USD</option>
                            <option value="eur">EUR</option>
                            <option value="cop">COP</option>
                            <option value="mxn">MXN</option>
                            <option value="ars">ARS</option>
                            <option value="clp">CLP</option>
                            <option value="pen">PEN</option>
                        </select>
                    </div>
                </div>

            </form>
        </div>

        {{-- ── Footer ───────────────────────────────────────────── --}}
        <div id="jobOfferFooter">
            <button class="btn btn-secondary" onclick="cerrarJobOffer()">Cancelar</button>
            <button class="btn btn-primary" id="btnPublishJobOffer" onclick="window.toast?.success('Funcionalidad en desarrollo')">
                Publicar oferta
            </button>
        </div>

    </div>
</div>

@push('styles')
@vite('resources/css/jobs/newJobOffer.css')
@endpush
@push('scripts')
@vite('resources/js/jobs/newJobOffer.js')
@endpush
