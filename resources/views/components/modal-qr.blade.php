{{-- resources/views/components/modal-qr.blade.php --}}
<div class="modal-backdrop" id="modal2FA">
    <div class="modal-card">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-header-text">
                <div class="modal-title" id="modal2FATitle">Escanea el código QR</div>
                <div class="modal-subtitle">Abre Google Authenticator o Authy y escanea el código para vincular tu cuenta.</div>
            </div>
            <button type="button" class="modal-close" id="btnClose2FAHeader" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body">

            <div id="qrContainer" style="display:flex;justify-content:center;padding:1rem;background:var(--bg-subtle, #f8f9fb);border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:1rem;"></div>

            <div class="modal-secret" style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;padding:0.6rem 0.875rem;background:var(--accent-dim);border:1px solid var(--accent-ring);border-radius:var(--r-md);font-size:0.78rem;color:var(--ink-400);">
                <span>O ingresa la clave manualmente:</span>
                <code id="secretKey" style="font-family:monospace;font-size:0.82rem;font-weight:600;color:var(--accent-dark);letter-spacing:0.04em;word-break:break-all;"></code>
            </div>

            <input type="text" id="codeInput2FA"
                style="width:100%;text-align:center;font-size:1.25rem;font-weight:700;letter-spacing:0.25em;padding:0.65rem;margin-top:1rem;border:1.5px solid var(--border-strong);border-radius:var(--r-md);font-family:inherit;color:var(--ink-900);background:var(--surface);outline:none;box-sizing:border-box;"
                placeholder="000000"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code" />

            <span id="error2FA" style="font-size:0.78rem;color:#dc2626;display:none;margin-top:0.375rem;"></span>

            {{-- Recovery codes --}}
            <div id="recoveryCodes" style="display:none;">
                <p style="font-size:0.82rem;color:var(--ink-400);margin-bottom:0.75rem;line-height:1.5">
                    Guarda estos códigos en un lugar seguro. Cada uno se usa <strong>una sola vez</strong> si pierdes acceso a tu app.
                </p>
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:var(--r-md);padding:0.6rem 0.875rem;font-size:0.78rem;color:#92400e;margin-bottom:0.5rem;">
                    Si ya tenias 2FA configurado, debes volver a escanear este nuevo código en tu app.
                </div>
                <div id="recoveryList" style="background:var(--bg-subtle, #f8f9fb);border:1px solid var(--border);border-radius:var(--r-md);padding:0.75rem 1rem;font-family:monospace;font-size:0.85rem;display:grid;grid-template-columns:1fr 1fr;gap:0.4rem;color:var(--ink-900);"></div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="modal-footer" id="modalActions">
            <button type="button" class="btn btn-secondary" id="btnCancel2FA">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnConfirm2FA">Confirmar y activar</button>
            <button type="button" class="btn btn-primary" id="btnCloseModal" style="display:none;">Entendido, los guardé</button>
        </div>

    </div>
</div>
