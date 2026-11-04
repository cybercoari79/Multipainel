<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
requireLogin();

global $pdo;
$id     = (int)($_GET['id'] ?? 0);
$userId = currentUserId();

if ($id > 0) {
    $st = $pdo->prepare('DELETE FROM connectplus_servers WHERE id = ? AND user_id = ?');
    if ($st->execute([$id, $userId]) && $st->rowCount() > 0) {
        generateClientUpdateFiles($pdo, $userId);
        logActivity($pdo, 'Excluiu servidor', 'Servidores', 'Servidor ID ' . $id . ' excluído.', $userId);
    }
}

redirect('/connectplus/servers.php');