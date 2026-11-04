<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireLogin();

$activePage = 'servers';
$pageTitle  = 'Servidores';
$userId     = currentUserId();
$servers    = getServers($pdo, $userId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidores - ConnectPlus</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(appUrl('/assets/css/style.css?v=1'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="dashboard">

    <?php include_once __DIR__ . '/../includes/menu.php'; ?>

    <div class="main-content">
        <?php include_once __DIR__ . '/../includes/header.php'; ?>

        <div class="page-toolbar">
            <h2>Lista de Servidores</h2>
            <a href="<?= htmlspecialchars(appUrl('/connectplus/add.php'), ENT_QUOTES, 'UTF-8') ?>" class="btn">+ Adicionar</a>
        </div>

        <?php if (empty($servers)): ?>
            <div class="table-container server-empty">Nenhum servidor cadastrado</div>
        <?php else: ?>
            <div class="table-container servers-table-container">
                <table class="server-list-table">
                    <colgroup>
                        <col class="col-name">
                        <col class="col-ip">
                        <col class="col-port">
                        <col class="col-type">
                        <col class="col-proxy">
                        <col class="col-sni">
                        <col class="col-payload">
                        <col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>IP / Host</th>
                            <th>Porta</th>
                            <th>Tipo</th>
                            <th>MultiProxy / ProxyIP</th>
                            <th>SNI</th>
                            <th>Payload</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($servers as $s): ?>
                        <tr>
                            <td data-label="Nome" class="server-name-cell"><strong><?= htmlspecialchars((string)($s['name'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong></td>
                            <td data-label="IP / Host" class="server-ip-cell"><div class="server-value-scroll"><?= htmlspecialchars((string)($s['server_ip'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div></td>
                            <td data-label="Porta" class="server-port-cell"><?= htmlspecialchars((string)($s['server_port'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                            <td data-label="Tipo" class="server-type-cell"><?= getStatusBadge((string)($s['type'] ?? '')) ?></td>
                            <td data-label="MultiProxy / ProxyIP" class="server-long-cell"><div class="server-value-scroll"><?= htmlspecialchars((string)($s['proxy_ip'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div></td>
                            <td data-label="SNI" class="server-long-cell"><div class="server-value-scroll"><?= htmlspecialchars((string)($s['sni'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div></td>
                            <td data-label="Payload" class="server-long-cell"><div class="server-value-scroll payload"><?= htmlspecialchars((string)($s['payload'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div></td>
                            <td data-label="Ações" class="server-actions-cell">
                                <div class="server-actions">
                                    <a href="<?= htmlspecialchars(appUrl('/connectplus/edit.php?id=' . (int)$s['id']), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-edit" aria-label="Editar">✏️</a>
                                    <a href="<?= htmlspecialchars(appUrl('/connectplus/delete.php?id=' . (int)$s['id']), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-delete" aria-label="Excluir" data-swal-confirm="Tem certeza?">🗑️</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>