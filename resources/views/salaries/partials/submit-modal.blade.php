<div class="salary-modal-backdrop" id="salaryModal">
    <div class="salary-modal">
        <div class="salary-modal-header">
            <div class="salary-modal-header-text">
                <h3 class="salary-modal-title">Aportar mi sueldo</h3>
                <p class="salary-modal-subtitle">Tus datos son completamente anónimos. Nunca asociamos esta información a tu perfil público.</p>
            </div>
            <button class="salary-modal-close" id="salaryModalClose" aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="salary-modal-body">
            <div class="salary-modal-privacy-banner">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Tu identidad nunca será revelada. No mostramos tu nombre ni enlace a tu perfil.</span>
            </div>

            <form id="salaryForm">
                @csrf

                <div class="salary-form-row">
                    <div class="salary-form-group">
                        <label class="salary-form-label">País</label>
                        <select class="salary-form-input" name="country" id="inputCountry" required>
                            <option value="">Seleccionar país...</option>
                            @foreach($countries as $c)
                            <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="salary-form-group">
                        <label class="salary-form-label">Ciudad</label>
                        <input class="salary-form-input" type="text" name="city" id="inputCity" placeholder="Ej: Medellín">
                    </div>
                </div>

                <div class="salary-form-row">
                    <div class="salary-form-group">
                        <label class="salary-form-label">Seniority</label>
                        <select class="salary-form-input" name="seniority" id="inputSeniority" required>
                            <option value="">Seleccionar...</option>
                            <option value="junior">Junior</option>
                            <option value="mid">Mid-Level</option>
                            <option value="senior">Senior</option>
                            <option value="lead">Lead / Architect</option>
                        </select>
                    </div>
                    <div class="salary-form-group">
                        <label class="salary-form-label">Años de experiencia</label>
                        <input class="salary-form-input" type="number" name="experience_years" id="inputExperience" min="0" max="70" placeholder="5" required>
                    </div>
                </div>

                <div class="salary-form-row">
                    <div class="salary-form-group">
                        <label class="salary-form-label">Sueldo anual (USD)</label>
                        <input class="salary-form-input" type="number" name="salary_usd" id="inputSalary" min="1000" max="999999" placeholder="45000" required>
                    </div>
                    <div class="salary-form-group">
                        <label class="salary-form-label">Modalidad</label>
                        <select class="salary-form-input" name="modality" id="inputModality" required>
                            <option value="">Seleccionar...</option>
                            <option value="remote">Remoto</option>
                            <option value="hybrid">Híbrido</option>
                            <option value="onsite">Presencial</option>
                        </select>
                    </div>
                </div>

                <div class="salary-form-group">
                    <label class="salary-form-label">Empresa <span class="salary-form-optional">(opcional)</span></label>
                    <input class="salary-form-input" type="text" name="company" id="inputCompany" placeholder="Ej: Mercado Libre" maxlength="150">
                </div>

                <div class="salary-form-group">
                    <label class="salary-form-label">Tecnologías que usas</label>
                    <div class="salary-form-techs" id="salaryFormTechs">
                        @foreach($technologies as $tech)
                        <label class="salary-form-tech-item">
                            <input type="checkbox" name="technologies[]" value="{{ $tech->id }}">
                            <img src="{{ $tech->iconUrl() }}" alt="{{ $tech->name }}" class="" loading="lazy" width="16" height="16">
                            {{ $tech->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <div class="salary-modal-footer">
            <button class="salary-modal-btn-cancel" id="salaryModalCancel">Cancelar</button>
            <button class="salary-modal-btn-save" id="salaryModalSubmit">Enviar anónimamente</button>
        </div>
    </div>
</div>
