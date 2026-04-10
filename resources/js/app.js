import "./bootstrap";
import "../css/app.css";
import "./shared/toast.js";

document.addEventListener("DOMContentLoaded", () => {
    window.addEventListener("popstate", () => {
        window.location.reload();
    });
});

if (window.location.hash === "#_=_") {
    history.replaceState
        ? history.replaceState(null, null, window.location.href.split("#")[0])
        : (window.location.hash = "");
}
