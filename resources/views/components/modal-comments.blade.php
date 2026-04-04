<!-- ══════════════════════════════════════════
     MODAL DE COMENTARIOS
══════════════════════════════════════════ -->
<div class="comments-modal" id="commentsModal">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h3 class="modal-title">Comentarios</h3>
            <button
                class="modal-close"
                id="closeCommentsModal"
                aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Original post -->
        <div class="modal-original-post">
            <div class="modal-post-header">
                <img src="" alt="" class="modal-post-avatar" id="modalPostAvatar" />
                <div class="modal-post-meta">
                    <div class="modal-post-author">
                        <span id="modalPostAuthor"></span>
                        <div class="verify-badge">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div
                        class="modal-post-handle-time"
                        id="modalPostHandleTime"></div>
                </div>
            </div>
            <p class="modal-post-content" id="modalPostContent"></p>
        </div>

        <!-- Comments list -->
        <div class="modal-comments" id="modalCommentsList">
            <!-- Los comentarios se cargan dinámicamente aquí -->
        </div>

        <!-- Comment input -->
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
                    <div
                        class="comment-input-actions"
                        id="commentActions"
                        style="display: none">
                        <button class="btn-comment-cancel" id="btnCancelComment">
                            Cancelar
                        </button>
                        <button
                            class="btn-comment-submit"
                            id="btnSubmitComment"
                            disabled>
                            Comentar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
