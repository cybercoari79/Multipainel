/**
 * PAINEL V3 - SCRIPT GLOBAL
 */
document.addEventListener("DOMContentLoaded", function() {
    // Menu mobile
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    window.toggleMenu = function() {
        if (sidebar) { sidebar.classList.toggle("open"); }
        if (overlay) { overlay.classList.toggle("active"); }
    };

    window.fecharMenu = function() {
        if (sidebar) { sidebar.classList.remove("open"); }
        if (overlay) { overlay.classList.remove("active"); }
    };

    // Fecha menu com ESC
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") { fecharMenu(); }
    });

    // Fecha ao clicar fora
    document.addEventListener("click", function(e) {
        if (window.innerWidth <= 768 && sidebar && overlay) {
            if (!sidebar.contains(e.target) && !e.target.closest(".menu-toggle-mobile") && !e.target.closest(".menu-toggle")) {
                fecharMenu();
            }
        }
    });

    // Ativa links do menu
    document.querySelectorAll(".sidebar-nav a").forEach(function(link) {
        if (window.location.pathname === link.pathname) {
            link.classList.add("active");
        }
        link.addEventListener("click", function() {
            if (window.innerWidth <= 768) { fecharMenu(); }
        });
    });

    // Confirmação de delete
    document.querySelectorAll(".btn-delete").forEach(function(btn) {
        btn.addEventListener("click", function(e) {
            if (!confirm("Tem certeza que deseja excluir este item?")) {
                e.preventDefault();
            }
        });
    });

    // Copy token
    document.querySelectorAll(".copy-btn").forEach(function(btn) {
        btn.addEventListener("click", function() {
            const target = document.getElementById(this.dataset.target || "token");
            if (target) {
                const text = target.textContent;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert("✅ Token copiado com sucesso!");
                    });
                } else {
                    const input = document.createElement("input");
                    input.value = text;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand("copy");
                    document.body.removeChild(input);
                    alert("✅ Token copiado com sucesso!");
                }
            }
        });
    });

    // Filter tables
    document.querySelectorAll(".filter-input").forEach(function(input) {
        input.addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = this.closest("table").querySelectorAll("tbody tr");
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    });

    // Auto dismiss alerts
    document.querySelectorAll(".success, .error, .warning, .info").forEach(function(el) {
        if (!el.classList.contains("persistent")) {
            setTimeout(function() {
                el.style.transition = "opacity 0.5s";
                el.style.opacity = "0";
                setTimeout(function() { el.style.display = "none"; }, 500);
            }, 5000);
        }
    });
});