<?php
declare(strict_types=1);

/** CSRF helpers exclusivos do módulo C4GMOD. */
function c4gmod_csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new RuntimeException('A sessão precisa estar ativa para gerar o token CSRF.');
    }
    if (empty($_SESSION['c4gmod_csrf_token']) || !is_string($_SESSION['c4gmod_csrf_token'])) {
        $_SESSION['c4gmod_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['c4gmod_csrf_token'];
}

function c4gmod_check_csrf(): void {
    $sent = $_POST['csrf'] ?? '';
    $expected = $_SESSION['c4gmod_csrf_token'] ?? '';
    if (!is_string($sent) || !is_string($expected) || $sent === '' || $expected === '' || !hash_equals($expected, $sent)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}
