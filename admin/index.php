<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireLogin();

$userId = currentUserId();
$admin = isAdmin();
$baseUrl = '';
$activePage = 'dashboard';
$pageTitle = 'Dashboard';

/*
 * O Dashboard é somente leitura. Nenhuma rotina de API/APK é alterada aqui.
 * Administrador vê os totais gerais; usuário comum vê somente seus próprios dados.
 */
if ($admin) {
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $activeUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
    $inactiveUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status <> 'active'")->fetchColumn();

    $totalServers = (int)$pdo->query("
        SELECT (SELECT COUNT(*) FROM atx_servers)
             + (SELECT COUNT(*) FROM 4g_servers)
             + (SELECT COUNT(*) FROM connectplus_servers)
    ")->fetchColumn();
    $totalNetworks = (int)$pdo->query("SELECT COUNT(*) FROM 4g_networks")->fetchColumn();
} else {
    $stmt = $pdo->prepare("
        SELECT (
            (SELECT COUNT(*) FROM atx_servers WHERE user_id = ?)
          + (SELECT COUNT(*) FROM 4g_servers WHERE user_id = ?)
          + (SELECT COUNT(*) FROM connectplus_servers WHERE user_id = ?)
        )
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $totalServers = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM 4g_networks WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalNetworks = (int)$stmt->fetchColumn();

    $totalUsers = 1;
    $activeUsers = 1;
    $inactiveUsers = 0;
}

$configVersion = '—';
$configReady = false;

$stmt = $pdo->prepare('SELECT version, update_url FROM atx_config WHERE user_id = ? LIMIT 1');
$stmt->execute([$userId]);
$config = $stmt->fetch();
if ($config) {
    $configVersion = (string)($config['version'] ?? '—');
    $configReady = trim((string)($config['update_url'] ?? '')) !== '';
}

$recentServers = [];
$recentNetworks = [];
if ($admin) {
    $recentServers = $pdo->query("
        SELECT name, server_ip, type FROM (
            SELECT name, server_ip, type, id FROM atx_servers
            UNION ALL SELECT name, server_ip, type, id FROM 4g_servers
            UNION ALL SELECT name, server_ip, type, id FROM connectplus_servers
        ) s ORDER BY id DESC LIMIT 5
    ")->fetchAll();
    $recentNetworks = $pdo->query('SELECT name, proxy_ip, info FROM 4g_networks ORDER BY id DESC LIMIT 5')->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT name, server_ip, type FROM (
            SELECT name, server_ip, type, id FROM atx_servers WHERE user_id = ?
            UNION ALL SELECT name, server_ip, type, id FROM 4g_servers WHERE user_id = ?
            UNION ALL SELECT name, server_ip, type, id FROM connectplus_servers WHERE user_id = ?
        ) s ORDER BY id DESC LIMIT 5
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $recentServers = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT name, proxy_ip, info FROM 4g_networks WHERE user_id = ? ORDER BY id DESC LIMIT 5');
    $stmt->execute([$userId]);
    $recentNetworks = $stmt->fetchAll();
}

$h = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ConnectPlus</title>
    <link rel="stylesheet" href="<?= appUrl('/../assets/css/style.css?v=1') ?>">
    <style>
        .dashboard-summary {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:20px;
            margin-bottom:30px;
        }
        .dashboard-summary .stat-card { min-height:96px; }
        .dashboard-section { margin-bottom:30px; }
        .dashboard-section-title {
            margin:0 0 15px;
            color:#fff;
            font-size:18px;
        }
        .quick-links {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(170px,1fr));
            gap:15px;
        }
        .quick-link {
            display:flex;
            align-items:center;
            gap:12px;
            padding:16px;
            background:#111927;
            border:1px solid #1a2a3a;
            border-radius:12px;
            color:#fff;
            text-decoration:none;
            transition:.2s ease;
        }
        .quick-link:hover { transform:translateY(-2px); border-color:#2d455c; }
        .quick-link-icon { font-size:24px; }
        .quick-link small { display:block; color:#8899aa; margin-top:3px; }
        .dashboard-status {
            display:flex;
            flex-wrap:wrap;
            gap:15px;
        }
        .status-card {
            flex:1 1 240px;
            padding:18px 20px;
            background:#111927;
            border:1px solid #1a2a3a;
            border-radius:12px;
        }
        .status-card h3 { margin:0 0 8px; color:#8899aa; font-size:14px; }
        .status-value { color:#fff; font-size:24px; font-weight:700; }
        .status-muted { color:#8899aa; font-size:13px; margin-top:5px; }
        @media (max-width:700px) {
            .quick-links { grid-template-columns:1fr 1fr; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../includes/menu.php'; ?>

    <div class="main-content">
        <?php include_once __DIR__ . '/../includes/header.php'; ?>

        <div class="dashboard-summary">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info"><h3><?= $totalUsers ?></h3><p>Usuários</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info"><h3><?= $activeUsers ?></h3><p>Usuários ativos</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏸️</div>
                <div class="stat-info"><h3><?= $inactiveUsers ?></h3><p>Usuários não ativos</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🖥️</div>
                <div class="stat-info"><h3><?= $totalServers ?></h3><p>Servidores</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📡</div>
                <div class="stat-info"><h3><?= $totalNetworks ?></h3><p>Redes</p></div>
            </div>
        </div>

        <div class="dashboard-section">
            <h2 class="dashboard-section-title">Status do aplicativo</h2>
            <div class="dashboard-status">
                <div class="status-card">
                    <h3>Versão atual</h3>
                    <div class="status-value"><?= $h($configVersion) ?></div>
                    <div class="status-muted">Versão cadastrada na configuração do usuário logado.</div>
                </div>
                <div class="status-card">
                    <h3>Configuração</h3>
                    <div class="status-value">
                        <?= $configReady ? '<span class="badge badge-success">Configurada</span>' : '<span class="badge badge-warning">Pendente</span>' ?>
                    </div>
                    <div class="status-muted">URL da API de atualização.</div>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <h2 class="dashboard-section-title">Acesso rápido</h2>
            <div class="quick-links">
                <a class="quick-link" href="<?= appUrl('/users/index.php') ?>"><span class="quick-link-icon">👥</span><span>Usuários<small>Gerenciar clientes</small></span></a>
                <a class="quick-link" href="<?= appUrl('/connectplus/servers.php') ?>"><span class="quick-link-icon">🖥️</span><span>Servidores<small>Gerenciar servidores</small></span></a>
                <a class="quick-link" href="<?= appUrl('/4g/networks.php') ?>"><span class="quick-link-icon">📡</span><span>Redes<small>Gerenciar redes</small></span></a>
                <a class="quick-link" href="<?= appUrl('/index.php') ?>"><span class="quick-link-icon">⚙️</span><span>Configurações<small>Versão e API</small></span></a>
                <a class="quick-link" href="<?= appUrl('/admin/tema.php') ?>"><span class="quick-link-icon">🎨</span><span>Tema<small>Personalização</small></span></a>
                <a class="quick-link" href="<?= appUrl('/messages/index.php') ?>"><span class="quick-link-icon">💬</span><span>Mensagens<small>SMS/atualização</small></span></a>
                <a class="quick-link" href="<?= appUrl('/backups/index.php') ?>"><span class="quick-link-icon">💾</span><span>Backups<small>Backup e restauração</small></span></a>
                <a class="quick-link" href="<?= appUrl('/logs/index.php') ?>"><span class="quick-link-icon">📋</span><span>Logs<small>Atividades do painel</small></span></a>
            </div>
        </div>

        <div class="recent-grid">
            <div class="recent-card">
                <h3>🖥️ Últimos Servidores</h3>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Nome</th><th>IP</th><th>Tipo</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentServers as $server): ?>
                            <tr>
                                <td><?= $h($server['name'] ?? '') ?></td>
                                <td><?= $h($server['server_ip'] ?? '') ?></td>
                                <td><?= getStatusBadge((string)($server['type'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$recentServers): ?>
                            <tr><td colspan="3" class="text-center">Nenhum servidor cadastrado.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="recent-card">
                <h3>📡 Últimas Redes</h3>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Nome</th><th>Proxy IP</th><th>Info</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentNetworks as $network): ?>
                            <tr>
                                <td><?= $h($network['name'] ?? '') ?></td>
                                <td><?= $h($network['proxy_ip'] ?? '') ?></td>
                                <td><?= $h($network['info'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$recentNetworks): ?>
                            <tr><td colspan="3" class="text-center">Nenhuma rede cadastrada.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
