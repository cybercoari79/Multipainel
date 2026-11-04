<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();
$baseUrl = '../';
$activePage = 'users';
$pageTitle = 'Adicionar Usuário';
$error = '';
$createdToken = '';
$createdUsername = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $createdUsername = trim((string)($_POST['username'] ?? ''));
    $result = createUser(
        $pdo,
        $createdUsername,
        (string)($_POST['password'] ?? ''),
        (string)($_POST['status'] ?? 'active'),
        'user'
    );

    if ($result['success']) {
        $createdToken = (string)$result['token'];
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Adicionar Usuário - V2</title>
<link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
<style>
.token-box{margin:15px 0;padding:15px;background:var(--surface-2);border:1px solid var(--border);border-radius:8px;word-break:break-all;font-family:monospace}
.token-box code{display:block;margin-top:8px;color:var(--text);font-size:14px}
</style>
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../includes/header.php'; ?>
        <div class="container-form">
            <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($createdToken): ?>
                <div class="success">Usuário criado com sucesso. Guarde o token abaixo; ele não será exibido novamente.</div>
                <div class="token-box"><strong>Token da API — <?= htmlspecialchars($createdUsername) ?></strong><code id="createdToken"><?= htmlspecialchars($createdToken) ?></code></div>
                <button type="button" class="btn btn-save" onclick="copiarToken()">Copiar token</button>
                <a href="index.php" class="btn-cancel">Voltar</a>
            <?php else: ?>
            <form method="POST">
                <label>Usuário *</label><input type="text" name="username" required minlength="3">
                <label>Senha *</label><input type="password" name="password" required minlength="6">
                <label>Status</label><select name="status"><option value="active">Ativo</option><option value="inactive">Inativo</option><option value="suspended">Suspenso</option></select>
                <div class="actions"><button type="submit" class="btn btn-save">Salvar</button><a href="/index.php" class="btn-cancel">Cancelar</a></div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function copiarToken(){
    const el=document.getElementById('createdToken');
    if(!el)return;
    const text=el.textContent.trim();
    if(navigator.clipboard){navigator.clipboard.writeText(text).then(()=>alert('Token copiado!'));}
    else {const t=document.createElement('textarea');t.value=text;document.body.appendChild(t);t.select();document.execCommand('copy');t.remove();alert('Token copiado!');}
}
</script>
</body>
</html>
