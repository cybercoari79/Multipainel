<?php
/**
 * ============================================================
 * MENU LATERAL COMPLETO - CONNECTPLUS
 * ============================================================
 */

$activePage = $activePage ?? '';
?>

<!-- ============================================================
     OVERLAY MOBILE
     ============================================================ -->
<div
    class="sidebar-overlay"
    id="sidebarOverlay"
    onclick="fecharMenu()"
></div>

<!-- ============================================================
     SIDEBAR
     ============================================================ -->
<div class="sidebar" id="sidebar">

    <!-- ========================================================
         MARCA
         ======================================================== -->
    <div class="sidebar-brand">

        <h2>
            Painel <span>CyberCoari</span>
        </h2>

        <small>
            Aplicativos
        </small>

        <button
            type="button"
            class="menu-toggle"
            onclick="fecharMenu()"
            aria-label="Fechar menu"
            title="Fechar menu"
        >
            ✕
        </button>

    </div>

    <!-- ========================================================
         NAVEGAÇÃO
         ======================================================== -->
    <nav class="sidebar-nav">

        <!-- DASHBOARD -->
        <a
            href="<?= appUrl('/admin/index.php') ?>"
            class="<?= $activePage === 'dashboard' ? 'active' : '' ?>"
        >
            📊
            <span>Dashboard</span>
        </a>
<!-- CONNECTPLUS -->
        <a href="<?= appUrl('/connectplus/index.php') ?>" class="<?= $activePage === 'connectplus' ? 'active' : '' ?>">
            🔷
            <span>Config ConnectPlus</span>
        </a>

        <!-- ATX -->
        <a
            href="<?= appUrl('/atx/index.php') ?>"
            class="<?= $activePage === 'atx' ? 'active' : '' ?>"
        >
            📱
            <span>Config ATX</span>
        </a>
        
        <!-- C4G -->
        <a
            href="<?= appUrl('/c4gmod/index.php') ?>"
            class="<?= $activePage === 'c4g' ? 'active' : '' ?>"
        >
            📱
            <span>Config C4G</span>
        </a>
        
        <!-- 4G -->
        <a href="<?= appUrl('/4g/index.php') ?>" class="<?= $activePage === '4g' ? 'active' : '' ?>">
            📶
            <span>Config 4G</span>
        </a>
<!-- MENSAGENS -->
        <a
            href="<?= appUrl('/messages/index.php') ?>"
            class="<?= $activePage === 'messages' ? 'active' : '' ?>"
        >
            💬
            <span>Mensagens</span>
        </a>

        <!-- LOGS -->
        <a
            href="<?= appUrl('/logs/index.php') ?>"
            class="<?= $activePage === 'logs' ? 'active' : '' ?>"
        >
            📋
            <span>Logs</span>
        </a>

        <!-- USUÁRIOS -->
        <a
            href="<?= appUrl('/users/index.php') ?>"
            class="<?= $activePage === 'users' ? 'active' : '' ?>"
        >
            👥
            <span>Usuários</span>
        </a>

        <!-- BACKUPS -->
        <a
            href="<?= appUrl('/backups/index.php') ?>"
            class="<?= $activePage === 'backups' ? 'active' : '' ?>"
        >
            💾
            <span>Backups</span>
        </a>

        <!-- ALTERAR SENHA -->
        <a
            href="<?= appUrl('/admin/profile.php') ?>"
            class="<?= $activePage === 'profile' ? 'active' : '' ?>"
        >
            🔑
            <span>Alterar Senha</span>
        </a>
        
         <!-- ALTERAR SENHA -->
        <a
            href="<?= appUrl('/atx/credencial_atx.php') ?>"
            class="<?= $activePage === 'credencial' ? 'active' : '' ?>"
        >
            🔑
            <span>Credencial ATX </span>
        </a>

        <!-- SEPARADOR -->
        <hr>

        <!-- SAIR -->
        <a
            href="<?= appUrl('/admin/logout.php') ?>"
            class="logout"
        >
            🚪
            <span>Sair</span>
        </a>

    </nav>

    <!-- ========================================================
         RODAPÉ
         ======================================================== -->
    <div class="sidebar-footer">

        Bem-vindo,
        <?= htmlspecialchars(
            $_SESSION['v2_username'] ?? 'Admin',
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        ) ?>

    </div>

</div>

<!-- ============================================================
     JAVASCRIPT DO MENU
     ============================================================ -->
<script>

function toggleMenu() {

    const sidebar =
        document.getElementById('sidebar');

    const overlay =
        document.getElementById('sidebarOverlay');

    if (sidebar) {
        sidebar.classList.toggle('open');
    }

    if (overlay) {
        overlay.classList.toggle('active');
    }
}


function fecharMenu() {

    const sidebar =
        document.getElementById('sidebar');

    const overlay =
        document.getElementById('sidebarOverlay');

    if (sidebar) {
        sidebar.classList.remove('open');
    }

    if (overlay) {
        overlay.classList.remove('active');
    }
}


// ============================================================
// FECHAR MENU AO CLICAR EM UM LINK NO CELULAR
// ============================================================
document.addEventListener('DOMContentLoaded', function () {

    document
        .querySelectorAll('.sidebar-nav a')
        .forEach(function (link) {

            link.addEventListener('click', function () {

                if (window.innerWidth <= 768) {
                    fecharMenu();
                }

            });

        });

});


// ============================================================
// FECHAR AO AUMENTAR A TELA
// ============================================================
window.addEventListener('resize', function () {

    if (window.innerWidth > 768) {
        fecharMenu();
    }

});


// ============================================================
// ESC PARA FECHAR
// ============================================================
document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {
        fecharMenu();
    }

});

</script>