const IS_OWN = true; // o pasado desde Blade

const ownOpts = document.getElementById("ownOpts");
const otherOpts = document.getElementById("otherOpts");
const btnFollow = document.getElementById("btnFollow");

if (ownOpts) ownOpts.style.display = IS_OWN ? "" : "none";
if (otherOpts) otherOpts.style.display = IS_OWN ? "none" : "";
if (btnFollow) btnFollow.style.display = IS_OWN ? "none" : "";

if (btnFollow) {
    btnFollow.addEventListener("click", () => {
        const following = btnFollow.classList.toggle("is-following");
        btnFollow.textContent = following ? "Siguiendo" : "Seguir";
    });
}
