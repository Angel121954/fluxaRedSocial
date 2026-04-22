let a,l,n,i,r,m,o;const v={post1:[{avatar:"https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100",author:"Mario Gómez",time:"Hace 15 min",text:"¡Se ve increíble! ¿Qué stack usaste para el backend?"},{avatar:"https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100",author:"Laura Martínez",time:"Hace 1 hora",text:"Excelente trabajo, me encanta la UI 🎨"}],post2:[{avatar:"https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100",author:"Carlos Ruiz",time:"Hace 30 min",text:"Muy buenos consejos, especialmente el punto 3 💡"}],post3:[]};function u(){a=document.getElementById("commentsModal"),l=document.getElementById("closeCommentsModal"),n=document.getElementById("commentTextarea"),i=document.getElementById("commentActions"),r=document.getElementById("btnCancelComment"),m=document.getElementById("btnSubmitComment"),o=document.getElementById("modalCommentsList")}function h(){u(),!(!a||!l)&&(l.addEventListener("click",c),document.addEventListener("keydown",t=>{t.key==="Escape"&&a.classList.contains("show")&&c()}),a.addEventListener("click",t=>{t.target===a&&c()}),n.addEventListener("input",t=>{const e=t.target.value.trim().length>0;i.style.display=e?"flex":"none",m.disabled=!e}),n.addEventListener("input",function(){this.style.height="auto",this.style.height=Math.min(this.scrollHeight,120)+"px"}),r.addEventListener("click",d),m.addEventListener("click",()=>{const t=n.value.trim();t&&(console.log("💬 Comentario enviado:",t),f(t),d())}))}function p(t){u(),a&&(document.getElementById("modalPostAvatar").src=t.avatar,document.getElementById("modalPostAuthor").textContent=t.author,document.getElementById("modalPostHandleTime").textContent=`${t.handle} · ${t.time}`,document.getElementById("modalPostContent").textContent=t.content,g(t.commentsKey),a.classList.add("show"),setTimeout(()=>n==null?void 0:n.focus(),100))}window.openCommentsModal=p;function c(){a.classList.remove("show"),d()}function g(t){const e=v[t]||[];console.log(e),e.length===0?o.innerHTML=`
            <div class="comments-empty">
                <div class="comments-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="comments-empty-title">Sin comentarios aún</div>
                <div class="comments-empty-text">Sé el primero en comentar este post</div>
            </div>
        `:o.innerHTML=e.map(s=>`
            <div class="comment-item">
                <img src="${s.avatar}" alt="${s.author}" class="comment-avatar">
                <div class="comment-body">
                    <div class="comment-header">
                        <span class="comment-author">${s.author}</span>
                        <span style="color:var(--ink-200);">·</span>
                        <span class="comment-time">${s.time}</span>
                    </div>
                    <p class="comment-text">${s.text}</p>
                    <div class="comment-actions">
                        <button class="comment-action">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Me gusta
                        </button>
                        <button class="comment-action">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Responder
                        </button>
                    </div>
                </div>
            </div>
        `).join("")}function d(){n&&(n.value="",n.style.height="auto",i&&(i.style.display="none"),m&&(m.disabled=!0))}function f(t){if(!o)return;const e={avatar:"https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100",author:"Tú",time:"Justo ahora",text:t},s=`
        <div class="comment-item">
            <img src="${e.avatar}" alt="${e.author}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${e.author}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time">${e.time}</span>
                </div>
                <p class="comment-text">${e.text}</p>
                <div class="comment-actions">
                    <button class="comment-action">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Me gusta
                    </button>
                    <button class="comment-action">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Responder
                    </button>
                </div>
            </div>
        </div>
    `;o.querySelector(".comments-empty")?o.innerHTML=s:o.insertAdjacentHTML("beforeend",s),o.scrollTop=o.scrollHeight}document.addEventListener("DOMContentLoaded",h);export{p as o};
