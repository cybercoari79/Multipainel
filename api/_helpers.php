<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function apiJsonResponse(array $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function apiJsonError(string $message, int $status = 400): never {
    apiJsonResponse(['success' => false, 'error' => $message], $status);
}

function apiRequireToken(): array {
    global $pdo;
    $token = trim((string)($_GET['token'] ?? ''));

    // Aceita Authorization: Bearer TOKEN futuramente sem quebrar o formato atual.
    if ($token === '' && !empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/^Bearer\s+(.+)$/i', (string)$_SERVER['HTTP_AUTHORIZATION'], $m)) {
        $token = trim($m[1]);
    }

    if ($token === '') {
        apiJsonError('Token não informado.', 401);
    }

    try {
        $user = verifyApiToken($pdo, $token);
    } catch (Throwable $e) {
        apiJsonError('Erro ao validar token.', 500);
    }

    if (!$user) {
        apiJsonError('Token inválido ou usuário inativo.', 401);
    }

    return $user;
}

function apiAbsoluteUrl(?string $value): string {
    return absoluteUrl((string)($value ?? ''));
}