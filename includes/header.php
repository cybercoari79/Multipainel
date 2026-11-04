<?php
/**
 * ============================================================
 * HEADER PADRÃO - CONNECTPLUS
 * ============================================================
 */

$pageTitleHeader = $pageTitle ?? 'Connecta Plus';
?>

<div class="header">

    <!-- ========================================================
         LADO ESQUERDO
         ======================================================== -->
    <div
        style="
            display:flex;
            align-items:center;
            gap:10px;
            min-width:0;
        "
    >

        <!-- BOTÃO MOBILE -->
        <button
            type="button"
            class="menu-toggle-mobile"
            onclick="toggleMenu()"
            aria-label="Abrir menu"
            title="Abrir menu"
        >
            ☰
        </button>

        <!-- TÍTULO DA PÁGINA -->
        <h1>
            <?= htmlspecialchars(
                (string)$pageTitleHeader,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ) ?>
        </h1>

    </div>

    <!-- ========================================================
         DATA E HORA
         ======================================================== -->
    <span class="date">
        <?= date('d/m/Y H:i') ?>
    </span>

</div>