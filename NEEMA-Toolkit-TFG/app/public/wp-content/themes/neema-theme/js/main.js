document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-login");
    const openButtons = document.querySelectorAll("a.btn-descargar.no-login");
    const closeButton = document.querySelector(".btn-cerrar");
    if (!modal) return; 
    if (openButtons.length > 0) {
        openButtons.forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                modal.classList.add("active");
            });
        });
    }
    if (closeButton) {
        closeButton.addEventListener("click", () => {
            modal.classList.remove("active");
        });
    }
    modal.addEventListener("click", (e) => {
        if (e.target === modal) modal.classList.remove("active");
    });
});
