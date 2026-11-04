<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();

$baseUrl = '../';
$activePage = '4g-networks';
$pageTitle = 'Redes';
$userId = currentUserId();

// Busca todas as redes do usuário
$stmt = $pdo->prepare('SELECT * FROM 4g_networks WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$userId]);
$networks = $stmt->fetchAll();

// Se houver payloads associados, busca também (opcional)
// $payloads = [];
// if (!empty($networks)) { ... }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redes - V2</title>
    <link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../../includes/header.php'; ?>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <h2 style="color:#8899aa; font-size:18px; font-weight:normal;">Lista de Redes</h2>
            <a href="add.php" class="btn btn-primary">+ Adicionar</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Operadora</th>
                        <th>Proxy IP</th>
                        <th>Info</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($networks)): ?>
                    <tr><td colspan="6" class="text-center">Nenhuma rede cadastrada</td></tr>
                <?php else: ?>
                    <?php foreach ($networks as $n): ?>
                    <tr>
                        <td>#<?= (int)$n['id'] ?></td>
                        <td><strong><?= htmlspecialchars($n['name'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($n['operator'] ?? '') ?></td>
                        <td><?= htmlspecialchars($n['proxy_ip'] ?? '') ?></td>
                        <td><?= htmlspecialchars($n['info'] ?? '') ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$n['id'] ?>" class="btn btn-sm btn-edit" title="Editar">✏️</a>
                            <a href="delete.php?id=<?= (int)$n['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta rede?')" title="Excluir">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>