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

            {{-- Estado de éxito --}}
            <div id="jo-success" class="jo-success" role="status" aria-live="polite" style="display:none">
                <div class="jo-success-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <h3>Oferta publicada</h3>
                <p>Tu oferta ya está visible en la bolsa de empleo</p>
            </div>

            {{-- Banner de error --}}
            <div id="jo-error-banner" class="jo-error-banner" role="alert" aria-live="assertive" style="display:none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <ul id="jo-error-list"></ul>
            </div>

            <form id="jobOfferForm" action="{{ route('jobs.store') }}" method="POST">
                @csrf

                <div class="jo-field">
                    <label for="jo-title">Título del cargo <span class="req" aria-hidden="true">*</span></label>
                    <input id="jo-title" name="title" type="text" class="jo-input"
                        placeholder="Ej: Desarrollador Laravel Senior"
                        aria-required="true" maxlength="120" />
                    <span id="jo-title-err" class="jo-field-error" style="display:none"></span>
                </div>

                <div class="jo-field">
                    <label for="jo-description">Descripción <span class="req" aria-hidden="true">*</span></label>
                    <textarea id="jo-description" name="description" class="jo-input jo-textarea"
                        placeholder="Describe las responsabilidades, requisitos y lo que ofrecen..."
                        aria-required="true" rows="5" maxlength="2000"></textarea>
                    <span id="jo-desc-err" class="jo-field-error" style="display:none"></span>
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

                <div class="jo-field">
                    <label for="jo-whatsapp">WhatsApp <span class="hint">(opcional)</span></label>
                    <div class="jo-input-wrap">
                        <span class="jo-input-prefix">+</span>
                        <input id="jo-whatsapp" name="whatsapp" type="text" class="jo-input jo-input--prefixed"
                            placeholder="573001234567" maxlength="20" />
                    </div>
                    <span class="jo-field-hint">Se mostrará un botón de contacto en la oferta</span>
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
            <button class="btn btn-secondary" onclick="cerrarJobOffer()" id="jo-btn-cancel">Cancelar</button>
            <button class="btn btn-primary" id="btnPublishJobOffer" onclick="publicarJobOffer()">
                Publicar oferta
            </button>
        </div>

    </div>
</div>

@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/jobs/newJobOffer.css')
@endpush
@push('scripts')
@vite('resources/js/jobs/newJobOffer.js')
@endpush
