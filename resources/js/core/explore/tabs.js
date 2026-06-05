/**
 * Inicializa los tabs de navegación (trending, recent, following)
 * @param {Function} onTabChange - Callback que se ejecuta al cambiar de tab
 */
export function initTabs(onTabChange = null) {
    const tabs = document.querySelectorAll(".feed-tab");
    const publicationsContainer = document.getElementById("publications-container");

    if (!tabs.length || !publicationsContainer) return;

    var skeletonHTML = Array(3).fill(
        '<div class="post-card-skeleton"><div class="sk-header"><div class="sk-avatar"></div><div class="sk-body"><div class="sk-line sk-line--lg"></div><div class="sk-line sk-line--sm"></div></div><div class="sk-menu"></div></div><div class="sk-title"></div><div class="sk-content"><div class="sk-line sk-line--lg"></div><div class="sk-line sk-line--md"></div></div><div class="sk-tags"><div class="sk-tag"></div><div class="sk-tag"></div><div class="sk-tag"></div></div><div class="sk-actions"><div class="sk-action"></div><div class="sk-action"></div><div class="sk-action"></div></div></div>'
    ).join('');

    tabs.forEach((tab) => {
        tab.addEventListener("click", async function () {
            tabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            const url = this.dataset.url;

            publicationsContainer.innerHTML = skeletonHTML;

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                    credentials: 'same-origin',
                });
                
                if (!response.ok) {
                    window.location.href = url;
                    return;
                }
                
                const html = await response.text();
                
                // If response is a full page, redirect
                if (html.includes('<!DOCTYPE') || html.includes('<html')) {
                    window.location.href = url;
                    return;
                }
                
                publicationsContainer.innerHTML = html;

                if (onTabChange) onTabChange();
            } catch (error) {
                window.location.href = url;
            }
        });
    });
}