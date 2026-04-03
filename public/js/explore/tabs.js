/**
 * Inicializa los tabs de navegación (trending, recent, following)
 * @param {Function} onTabChange - Callback que se ejecuta al cambiar de tab
 */
function initTabs(onTabChange = null) {
    const tabs = document.querySelectorAll(".feed-tab");
    const publicationsContainer = document.getElementById("publications-container");

    if (!tabs.length || !publicationsContainer) return;

    tabs.forEach((tab) => {
        tab.addEventListener("click", async function () {
            tabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            const url = this.dataset.url;

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
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