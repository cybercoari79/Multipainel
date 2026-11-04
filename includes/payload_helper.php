<?php
/**
 * PAINEL V3 - PAYLOAD HELPER
 * Funções para gerenciar arquivos de payload
 */

function getPayloadFromFile(int $networkId): string {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT payload_file FROM networks WHERE id = ?');
        $stmt->execute([$networkId]);
        $file = $stmt->fetchColumn();
        if (!$file) return '';
        $path = PAYLOAD_DIR . $file;
        if (!file_exists($path)) return '';
        return file_get_contents($path);
    } catch (Exception $e) {
        return '';
    }
}

function listPayloadFiles(int $userId): array {
    $files = [];
    $dir = PAYLOAD_DIR;
    if (!is_dir($dir)) return $files;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . $item;
        if (is_file($path)) {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, ['txt', 'payload', 'json', 'conf'])) {
                $files[] = [
                    'name' => $item,
                    'size' => filesize($path),
                    'modified' => date('d/m/Y H:i', filemtime($path)),
                    'ext' => $ext
                ];
            }
        }
    }
    usort($files, function($a, $b) { return strcmp($a['name'], $b['name']); });
    return $files;
}

function getPayloadContent(string $filename): string {
    $path = PAYLOAD_DIR . $filename;
    if (!file_exists($path)) return '';
    return file_get_contents($path);
}

function savePayloadFile(string $filename, string $content): bool {
    $path = PAYLOAD_DIR . $filename;
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    return file_put_contents($path, $content) !== false;
}

function deletePayloadFile(string $filename): bool {
    $path = PAYLOAD_DIR . $filename;
    if (!file_exists($path)) return false;
    return unlink($path);
}

function validatePayloadContent(string $content): array {
    $errors = [];
    $lines = explode("\n", $content);
    $lineCount = 0;
    $hasHost = false;
    $hasPort = false;
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $lineCount++;
        if (strpos($line, '://') !== false) { $hasHost = true; }
        if (preg_match('/:\d+/', $line)) { $hasPort = true; }
    }
    if ($lineCount === 0) { $errors[] = 'Payload vazio'; }
    if (!$hasHost) { $errors[] = 'Nenhum host/URL encontrado'; }
    return $errors;
}
?>