import "./bootstrap";
import "../css/app.css";

document.addEventListener("DOMContentLoaded", () => {
    window.addEventListener("popstate", () => {
        window.location.reload();
    });
});
