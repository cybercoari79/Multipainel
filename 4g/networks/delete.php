<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$userId = currentUserId();

// Verifica se a rede pertence ao usuário
$stmt = $pdo->prepare('SELECT id FROM 4g_networks WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
if ($stmt->fetch()) {
    // Exclui payloads associados (opcional)
    $stmt = $pdo->prepare('DELETE FROM 4g_payloads WHERE network_id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    // Exclui a rede
    $stmt = $pdo->prepare('DELETE FROM 4g_networks WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    
}

redirect('/4g/networks.php');