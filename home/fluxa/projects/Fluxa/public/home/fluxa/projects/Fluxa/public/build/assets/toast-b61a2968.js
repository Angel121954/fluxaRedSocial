class m{constructor(){this.toasts=[],this.defaults={duration:4e3,position:"bottom-center",dismissible:!0,id:null},this.positions={"top-left":{top:"20px",left:"20px",right:"auto",bottom:"auto"},"top-center":{top:"20px",left:"50%",right:"auto",bottom:"auto",transform:"translateX(-50%)"},"top-right":{top:"20px",right:"20px",left:"auto",bottom:"auto"},"bottom-left":{bottom:"20px",left:"20px",right:"auto",top:"auto"},"bottom-center":{bottom:"20px",left:"50%",right:"auto",top:"auto",transform:"translateX(-50%)"},"bottom-right":{bottom:"20px",right:"20px",left:"auto",top:"auto"}},this.styles={success:{bg:"#12b3b6"},error:{bg:"#ef4444"},warning:{bg:"#f59e0b"},info:{bg:"#3b82f6"}},this.createContainer()}createContainer(){if(document.getElementById("toast-container"))return;const t=document.createElement("div");t.id="toast-container",t.style.cssText=`
            position: fixed;
            z-index: 999999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            pointer-events: none;
        `,document.body.appendChild(t)}getPositionStyles(t){const e=this.positions[t]||this.positions["bottom-right"];return Object.entries(e).filter(([s])=>s!=="transform").map(([s,i])=>`${s}: ${i}`).join("; ")+(e.transform?`; transform: ${e.transform}`:"")}getIcon(t){return{check:'<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',x:'<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',alert:'<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',info:'<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'}[t]}createToastElement(t){const e=this.styles[t.type]||this.styles.success,s=t.id||`toast-${Date.now()}`,i=`${t.type}-${t.message}`,o=document.createElement("div");return o.id=s,o.dataset.messageKey=i,o.className="toast-entry",o.style.cssText=`
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: ${e.bg};
            color: #fff;
            border-radius: var(--r-lg, 14px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 14px;
            font-family: system-ui, -apple-system, sans-serif;
            min-width: 280px;
            max-width: 400px;
            pointer-events: auto;
            animation: toast-in 0.3s ease-out;
        `,o.innerHTML=`
            <div class="toast-content" style="flex: 1;">${t.message}</div>
            ${t.dismissible?`
            <button class="toast-close" style="
                flex-shrink: 0;
                background: none;
                border: none;
                color: rgba(255,255,255,0.8);
                cursor: pointer;
                padding: 4px;
                display: flex;
                border-radius: 4px;
                transition: background 0.2s;
            ">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            `:""}
        `,o}show(t,e={}){const s={...this.defaults,...e,message:t},i=document.getElementById("toast-container"),o=`${s.type}-${t}`,d=this.toasts.find(a=>a.messageKey===o);if(d)return this.updateExisting(d,s.duration),d.id;i.style.cssText=`
            position: fixed;
            z-index: 999999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            pointer-events: none;
            ${this.getPositionStyles(s.position)}
        `;const l=this.createToastElement(s);i.appendChild(l);const r={id:l.id,element:l,timeout:null,messageKey:o};if(this.toasts.push(r),s.dismissible){const a=l.querySelector(".toast-close");a==null||a.addEventListener("click",()=>this.dismiss(r.id))}return s.duration>0&&(r.timeout=setTimeout(()=>this.dismiss(r.id),s.duration)),r.id}updateExisting(t,e){const s=t.element;s.style.animation="none",s.offsetHeight,s.style.animation="toast-pulse 0.2s ease-out",t.timeout&&clearTimeout(t.timeout),e>0&&(t.timeout=setTimeout(()=>this.dismiss(t.id),e))}dismiss(t){const e=this.toasts.findIndex(o=>o.id===t);if(e===-1)return;const s=this.toasts[e];s.timeout&&clearTimeout(s.timeout);const i=s.element;i.style.animation="toast-out 0.2s ease-in forwards",setTimeout(()=>{i.remove(),this.toasts.splice(e,1)},200)}success(t,e={}){return this.show(t,{...e,type:"success"})}error(t,e={}){return this.show(t,{...e,type:"error",duration:5e3})}warning(t,e={}){return this.show(t,{...e,type:"warning"})}info(t,e={}){return this.show(t,{...e,type:"info"})}dismissAll(){this.toasts.forEach(t=>this.dismiss(t.id))}}const c=new m,u=document.createElement("style");u.textContent=`
    @keyframes toast-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes toast-out {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(20px);
        }
    }
    @keyframes toast-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
`;document.head.appendChild(u);function h(n,t="success",e={}){return c.show(n,{...e,type:t})}function p(){const n=window.sessionToast;n&&c.show(n.message,{type:n.type})}window.addEventListener("DOMContentLoaded",()=>{p(),window.toast=c});export{h as s};
