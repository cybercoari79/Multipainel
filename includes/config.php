<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'seu_name');
define('DB_USER', 'seu_user');
define('DB_PASS', 'sua_senha');

define('CRON_TOKEN', 'seu_token');

// A instalação do painel fica em /vip. A URL-base é única para todos os módulos.
function detectSiteUrl(): string {
    if (PHP_SAPI === 'cli') return '';
    $https = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
    $scheme = $https ? 'https' : 'http';
    $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost'));
    return $scheme . '://' . $host . '/';
}

define('SITE_URL', rtrim(detectSiteUrl(), '/'));
define('BASE_URL', SITE_URL);
define('SITE_NAME', 'PAINEL VIP 2.1');
define('SITE_EMAIL', 'contato@cybercoari.com.br');
define('PAYLOAD_DIR', dirname(__DIR__) . '/payloads/');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('SESSION_TIMEOUT', 3600);
define('SALT', 'v2_salt_a02d89b8c5067ca0');
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) {
    session_name('v2_session');
    session_start();
}
?>