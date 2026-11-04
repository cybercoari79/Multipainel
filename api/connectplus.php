<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/connectplus.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    ensureConnectPlusTables($pdo);

    $credential = trim((string)($_GET['URL'] ?? $_GET['user'] ?? ''));
    $userId = 0;

    if ($credential !== '') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$credential]);
        $userId = (int)($stmt->fetchColumn() ?: 0);

        if ($userId === 0 && ctype_digit($credential)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND status = 'active' LIMIT 1");
            $stmt->execute([(int)$credential]);
            $userId = (int)($stmt->fetchColumn() ?: 0);
        }
    }

    if ($userId === 0) {
        $stmt = $pdo->query("SELECT id FROM users WHERE id=1 AND status='active' LIMIT 1");
        $userId = (int)($stmt->fetchColumn() ?: 0);
    }

    if ($userId === 0) {
        $stmt = $pdo->query("SELECT id FROM users WHERE role='admin' AND status='active' ORDER BY id ASC LIMIT 1");
        $userId = (int)($stmt->fetchColumn() ?: 0);
    }

    if ($userId === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuário ConnectPlus ativo não encontrado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $output = buildConnectPlusConfig($pdo, $userId);

    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    @file_put_contents(
        $logDir . '/connectplus_update.log',
        '[' . date('Y-m-d H:i:s') . '] ConnectPlus OK | user_id=' . $userId .
        ' | servers=' . count($output['Servers'] ?? []) . ' | IP=' . ($_SERVER['REMOTE_ADDR'] ?? '-') . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    error_log('ConnectPlus update endpoint: ' . $e->getMessage());

    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    @file_put_contents(
        $logDir . '/connectplus_update.log',
        '[' . date('Y-m-d H:i:s') . '] ConnectPlus ERROR | ' . $e->getMessage() . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao gerar configuração ConnectPlus.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}