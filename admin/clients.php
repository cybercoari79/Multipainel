<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $username = trim((string)($_POST['username'] ?? ''));
        $credential = trim((string)($_POST['credential'] ?? ''));
        if ($username !== '' && $credential !== '') {
            try {
                $stmt = $pdo->prepare('INSERT INTO clients (username, credential_hash, credential_preview, status) VALUES (?, SHA2(?,256), ?, "active")');
                $stmt->execute([$username, $credential, substr($credential, 0, 4) . '****']);
                $msg = 'Cliente criado.';
            } catch (Throwable $e) { $msg = 'Não foi possível criar: credencial ou usuário já existe.'; }
        }
    }
}
$rows = $pdo->query('SELECT id, username, credential_preview, status, created_at FROM clients ORDER BY id DESC')->fetchAll();
?>
<?php $pageTitle='Clientes — ATX Panel'; require __DIR__.'/includes/header.php'; ?>
<div class="wrap"><a href="index.php">← Dashboard</a><h1>Clientes</h1><p><?=htmlspecialchars($msg)?></p>
<form method="post"><input type="hidden" name="action" value="create"><input name="username" placeholder="Nome do cliente" required><input name="credential" placeholder="credential.txt" required><button>Criar</button></form>
<table><tr><th>ID</th><th>Cliente</th><th>Credential</th><th>Status</th><th>Criado</th></tr><?php foreach($rows as $r): ?><tr><td><?=$r['id']?></td><td><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['credential_preview'])?></td><td><?=htmlspecialchars($r['status'])?></td><td><?=htmlspecialchars($r['created_at'])?></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__.'/includes/footer.php'; ?>
