<div class="modal-backdrop comments-modal" id="commentsModal">
    <div class="modal-card modal-content">
        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-title">Comentarios</div>
            <button class="modal-close" id="closeCommentsModal" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Original post --}}
        <div class="modal-original-post">
            <div class="modal-post-header">
                <img src="" alt="" class="modal-post-avatar" id="modalPostAvatar" />
                <div class="modal-post-meta">
                    <div class="modal-post-author">
                        <span id="modalPostAuthor"></span>
                        <div class="verify-badge">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="modal-post-handle-time" id="modalPostHandleTime"></div>
                </div>
            </div>
            <p class="modal-post-content" id="modalPostContent"></p>
        </div>

        {{-- Comments list --}}
        <div class="modal-comments" id="modalCommentsList">
            {{-- Los comentarios se cargan dinámicamente aquí --}}
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
            <div class="comment-input-wrap">
                <img
                    src="{{ Auth::user()?->avatar_url ?? '' }}"
                    alt="Tú"
                    class="comment-input-avatar" />
                <div class="comment-input-form">
                    <textarea
                        class="comment-textarea"
                        id="commentTextarea"
                        placeholder="Escribe un comentario..."
                        rows="1"></textarea>
                    <div class="comment-input-actions" id="commentActions" style="display: none">
                        <button class="btn-comment-cancel" id="btnCancelComment">Cancelar</button>
                        <button class="btn-comment-submit" id="btnSubmitComment" disabled>Comentar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>