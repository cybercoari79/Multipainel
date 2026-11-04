<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireLogin();
$baseUrl = '../';
$activePage = '4g-servers';
$pageTitle = 'Servidores';
$userId = currentUserId();
$servers = get4gServers($pdo, $userId);
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Servidores - V2</title>
<link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/includes/header.php'; ?>
        <div style="display:flex;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
            <h2 style="color:#8899aa;font-size:18px;font-weight:normal;">Lista de Servidores</h2>
            <a href="servers/add.php" class="btn">+ Adicionar</a>
        </div>
        <div class="table-container"><table><thead><tr><th>Nome</th><th>IP</th><th>Porta</th><th>Tipo</th><th>Ações</th></tr></thead><tbody>
        <?php foreach ($servers as $s): ?>
        <tr><td><strong><?= htmlspecialchars($s['name']) ?></strong></td><td><?= htmlspecialchars($s['server_ip']) ?></td><td><?= htmlspecialchars($s['server_port']) ?></td><td><?= getStatusBadge($s['type']) ?></td><td><a href="servers/edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-edit">✏️</a> <a href="servers/delete.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Tem certeza?')">🗑️</a></td></tr>
        <?php endforeach; if (empty($servers)): ?><tr><td colspan="5" class="text-center">Nenhum servidor cadastrado</td></tr><?php endif; ?>
        </tbody></table></div>
    </div>
</div>
</body>
</html>