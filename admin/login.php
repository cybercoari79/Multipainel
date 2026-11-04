<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (isLoggedIn()) { redirect('/admin/index.php'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) { $error = 'Preencha todos os campos!'; }
    elseif (login($username, $password)) { redirect('/admin/index.php'); }
    else { $error = 'Usuário ou senha inválidos!'; }
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login - Painel</title>
<link rel="stylesheet" href="<?= appUrl('/../assets/css/style.css?v=1') ?>">
</head>
<body style="display:flex;justify-content:center;align-items:center;min-height:100vh;background:#0a0e17;padding:20px;">
<div class="login-box">
    <h1>Painel <span>Connecta 3.1</span></h1>
    <div class="subtitle">ConnectPlus <small>Servidores + Redes</small></div>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <label>Usuário</label>
        <input type="text" name="username" placeholder="Digite seu usuário" required>
        <label>Senha</label>
        <input type="password" name="password" placeholder="Digite sua senha" required>
        <button type="submit">Entrar</button>
    </form>
    <div class="register-link">Não tem conta? <a href="<?= appUrl('/admin/register.php') ?>">Criar conta agora</a>
    <a href="https://github.com/cybecoari/vip/raw/refs/heads/main/vip_2.3.apk">
    </div>
    <div class="footer">&copy; <?= date('Y') ?> - Todos os direitos reservados</div>
    <div class="version">Versão 2.1</div>
</div>
</body></html>