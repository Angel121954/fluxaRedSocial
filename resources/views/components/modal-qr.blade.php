{{-- resources/views/components/modal-qr.blade.php --}}
<div id="modal2FA" style="display:none;" class="modal-overlay">
    <div class="modal-box">

        <h3 id="modal2FATitle">Escanea el código QR</h3>
        <p>Abre Google Authenticator o Authy y escanea el código para vincular tu cuenta.</p>

        <div id="qrContainer"></div>

        <div class="modal-secret">
            <span>O ingresa la clave manualmente:</span>
            <code id="secretKey"></code>
        </div>

        <input
            type="text"
            id="codeInput2FA"
            class="form-input"
            placeholder="000000"
            maxlength="6"
            inputmode="numeric"
            autocomplete="one-time-code" />

        <span id="error2FA" class="modal-error"></span>

        <div class="modal-actions" id="modalActions">
            <button type="button" id="btnCancel2FA" class="btn-cancel">Cancelar</button>
            <button type="button" id="btnConfirm2FA" class="btn-submit">Confirmar y activar</button>
        </div>

        {{-- Sección de códigos de recuperación (oculta hasta confirmar) --}}
        <div id="recoveryCodes" style="display:none;">
            <p style="font-size:0.82rem;color:var(--ink-400);margin-bottom:0.75rem;line-height:1.5">
                Guarda estos códigos en un lugar seguro. Cada uno se usa <strong>una sola vez</strong> si pierdes acceso a tu app.
            </p>
            <div style="
    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:8px;
    padding:0.6rem 0.875rem;
    font-size:0.78rem;
    color:#92400e;
    margin-bottom:0.5rem;
">
                Si ya tenias 2FA configurado, debes volver a escanear este nuevo codigo en tu app.
            </div>
            <div id="recoveryList" style="
                background:#f8f9fb;
                border:1px solid #e8eaef;
                border-radius:8px;
                padding:0.75rem 1rem;
                font-family:monospace;
                font-size:0.85rem;
                display:grid;
                grid-template-columns:1fr 1fr;
                gap:0.4rem;
                color:#0b0f1a;
            "></div>
            <button type="button" id="btnCloseModal" class="btn-submit" style="margin-top:1rem;width:100%">
                Entendido, los guardé
            </button>
        </div>

    </div>
</div>