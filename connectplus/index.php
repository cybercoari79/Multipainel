<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/connectplus.php';

requireLogin();

global $pdo;
ensureConnectPlusTables($pdo);

$activePage = 'connectplus';
$pageTitle  = 'Configurações ConnectPlus';
$uid        = currentUserId();

/* ---------- Helpers ---------- */

function cpx(mixed $v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function cpurl(string $v, string $label, bool $req = false): string
{
    $v = trim($v);
    if ($req && $v === '') {
        throw new RuntimeException("Informe {$label}.");
    }
    if ($v !== '' && !filter_var($v, FILTER_VALIDATE_URL)) {
        throw new RuntimeException("URL inválida: {$label}.");
    }
    return $v;
}

/* ---------- Seleção de usuário ---------- */

$users = isAdmin() ? getUsers($pdo) : [getUser($pdo, $uid)];

$selected = isAdmin()
    ? (int)($_GET['user'] ?? $_POST['user_id'] ?? $uid)
    : $uid;

if ($selected <= 0) {
    $selected = $uid;
}

$u = getUser($pdo, $selected);

if (!$u || $u['status'] !== 'active' || (!isAdmin() && $selected !== $uid)) {
    http_response_code(403);
    exit('Acesso negado.');
}

$cfg   = getConnectPlusConfig($pdo, $selected);
$error = '';

/* ---------- Processamento do formulário ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $p = fn($k, $d = '') => trim((string)($_POST[$k] ?? $d));

        $version = $p('version', '0');
        $update  = cpurl($p('update_url'), 'Update', true);

        $parts = preg_split('/[;,#,\s]+/', $p('udp_port', '7300'), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($parts as $port) {
            if (!ctype_digit($port) || (int)$port < 1 || (int)$port > 65535) {
                throw new RuntimeException('Porta UDP inválida: ' . $port);
            }
        }

        $udp = implode(';', array_values(array_unique(array_map('strval', array_map('intval', $parts)))));

        $contato = cpurl($p('contato'), 'Contato');
        $site    = cpurl($p('site'), 'Site');

        $st = $pdo->prepare(
            'INSERT INTO connectplus_config(user_id, version, update_url, udp_port, contato, site)
             VALUES(?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                version    = VALUES(version),
                update_url = VALUES(update_url),
                udp_port   = VALUES(udp_port),
                contato    = VALUES(contato),
                site       = VALUES(site)'
        );

        $st->execute([$selected, $version, $update, $udp, $contato, $site]);

        if (!generateClientUpdateFiles($pdo, $selected)) {
            throw new RuntimeException('Configuração salva, mas não foi possível atualizar os arquivos de atualização.');
        }

        header('Location: ' . appUrl('/connectplus/index.php?user=' . $selected . '&saved=1'));
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$cfg = getConnectPlusConfig($pdo, $selected);
$api = appUrl('/api/connectplus.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= cpx($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= cpx(appUrl('/assets/css/style.css?v=1')) ?>">
    <style>
        .cp-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px }
        .cp-section { margin-bottom:24px }
        .cp-section h2 { margin:0 0 16px; color:#fff; font-size:20px }
        .cp-actions { display:flex; gap:10px; flex-wrap:wrap }
        .cp-actions a { padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; background:#2563eb; color:#fff }
        .cp-actions .secondary { background:#374151 }
        @media(max-width:768px){ .cp-grid { grid-template-columns:1fr } }
    </style>
</head>
<body>
<div class="dashboard">

    <?php require __DIR__ . '/../includes/menu.php'; ?>

    <main class="main-content">

        <?php require __DIR__ . '/../includes/header.php'; ?>

        <?php if (!empty($_GET['saved'])): ?>
            <div class="success">Configurações do ConnectPlus salvas com sucesso.</div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="error"><?= cpx($error) ?></div>
        <?php endif; ?>

        <div class="container-form" style="max-width:1100px">

            <h1>Configurações <span>ConnectPlus</span></h1>
            <p class="subtitle">Ajustes exclusivos do ConnectPlus. Estas informações não são compartilhadas com os servidores ATX ou VIP.</p>

            <form method="post">
                <input type="hidden" name="user_id" value="<?= $selected ?>">

                <div class="cp-section">
                    <h2>Usuário e API</h2>
                    <div class="cp-grid">
                        <div>
                            <label>Usuário</label>
                            <select name="user_id" onchange="location.href='<?= cpx(appUrl('/connectplus/index.php')) ?>?user='+this.value">
                                <?php foreach ($users as $x): if (!$x) continue; ?>
                                    <option value="<?= (int)$x['id'] ?>" <?= $selected === (int)$x['id'] ? 'selected' : '' ?>>
                                        <?= cpx($x['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Endpoint do ConnectPlus</label>
                            <input value="<?= cpx($api) ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="cp-section">
                    <h2>Configurações gerais</h2>
                    <div class="cp-grid">
                        <div>
                            <label>Versão</label>
                            <input name="version" value="<?= cpx($cfg['version'] ?? '0') ?>" required>
                        </div>
                        <div>
                            <label>Atualização</label>
                            <input type="url" name="update_url" value="<?= cpx($cfg['update_url'] ?? $api) ?>" required>
                        </div>
                        <div>
                            <label>Porta UDP</label>
                            <input name="udp_port" value="<?= cpx($cfg['udp_port'] ?? '7300') ?>" placeholder="7300;7400">
                        </div>
                        <div>
                            <label>Contato</label>
                            <input type="url" name="contato" value="<?= cpx($cfg['contato'] ?? '') ?>">
                        </div>
                        <div>
                            <label>Site</label>
                            <input type="url" name="site" value="<?= cpx($cfg['site'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="cp-section">
                    <h2>Config.json</h2>
                    <p class="subtitle">A pré-visualização exibe o JSON atual fornecido pela API do ConnectPlus.</p>
                    <div class="cp-actions">
                        <!--<a href="<?= cpx(appUrl('/api/connectplus.php?user=' . $selected)) ?>">Config.json</a>-->
                        <a class="secondary" href="<?= cpx($api) ?>" target="_blank">Abrir API</a>
                        
        <a href="<?= cpx(appUrl('/connectplus/servers.php?user=' . $selected)) ?>">Servidor</a>
                        
        <a href="<?= cpx(appUrl('/connectplus/tema.php?user=' . $selected)) ?>">Tema</a>               
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-save">Salvar configurações do ConnectPlus</button>
                </div>
            </form>

        </div>
    </main>
</div>
</body>
</html>