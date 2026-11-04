<?php
declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

$user = apiRequireToken();
$userId = (int)$user['id'];

global $pdo;

try {
    // O contrato deste endpoint é o JSON oficial consumido pelo aplicativo 4G.
    apiJsonResponse(build4gConfig($pdo, $userId));
} catch (Throwable $e) {
    error_log('API config.php: ' . $e->getMessage());
    apiJsonError('Erro ao gerar a configuração.', 500);
}
