<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$erro = $sucesso = $tokenGerado = '';

/* ---------- Já logado? Redireciona ---------- */

if (isset($_SESSION['v2_user_id'])) {
    header('Location: ' . appUrl('/admin/index.php'));
    exit;
}

/* ---------- Processa formulário ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim((string)($_POST['username']  ?? ''));
    $password  = (string)($_POST['password']  ?? '');
    $confirmar = (string)($_POST['confirmar'] ?? '');

    if ($username === '' || $password === '' || $confirmar === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($username) < 3) {
        $erro = 'Usuário deve ter pelo menos 3 caracteres.';
    } elseif (!validatePassword($password)) {
        $erro = 'Senha deve ter pelo menos 4 caracteres.';
    } elseif ($password !== $confirmar) {
        $erro = 'Senhas não coincidem.';
    } else {
        $result = createUser($pdo, $username, $password, 'active', 'user');

        if ($result['success']) {
            $sucesso     = 'Usuário criado com sucesso!';
            $tokenGerado = (string)$result['token'];
        } else {
            $erro = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar - V2</title>
    <link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
    <style>
        .main-content { display:flex; justify-content:center; align-items:center; min-height:80vh }
        .container-form { max-width:500px; width:100%; margin:0 auto }
        .token-box {
            background:#0d1520; border:1px solid #ff6b00; border-radius:8px;
            padding:15px; margin:20px 0; word-break:break-all;
            font-family:monospace; font-size:14px; color:#00cc00; position:relative
        }
        .token-box .copy-btn {
            position:absolute; top:8px; right:8px; background:#ff6b00; color:#fff;
            border:none; border-radius:4px; padding:4px 12px; font-size:12px; cursor:pointer
        }
        .token-box .copy-btn:hover { background:#e85e00 }
        .link-login { text-align:center; margin-top:20px; color:#8899aa }
        .link-login a { color:#ff6b00 }
        .btn-block { width:100% }
        .subtitle { text-align:center; color:#8899aa; margin-bottom:20px; font-size:14px }
    </style>
</head>
<body>
<div class="dashboard">

    <?php include_once __DIR__ . '/../includes/menu.php'; ?>

    <div class="main-content">
        <div class="container-form">

            <h1>Criar <span>Conta</span></h1>
            <div class="subtitle">Cadastre-se e receba seu token</div>

            <?php if ($sucesso): ?>

                <div class="success">✅ <?= $sucesso ?></div>

                <?php if ($tokenGerado): ?>
                    <div class="token-box">
                        <strong>🔑 Token:</strong><br>
                        <code id="token" style="font-size:13px;"><?= $tokenGerado ?></code>
                        <button class="copy-btn" onclick="copiarToken()">📋 Copiar</button>
                    </div>
                    <div style="text-align:center;margin-top:15px;">
                        <p style="color:#8899aa;font-size:14px;">
                            👉 <a href="<?= htmlspecialchars(getApiUrl() . '?token=' . rawurlencode($tokenGerado)) ?>" target="_blank">Testar API</a>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="link-login">
                    <a href="<?= appUrl('/admin/login.php') ?>">← Ir para o Login</a>
                </div>

            <?php else: ?>

                <?php if ($erro): ?>
                    <div class="error">❌ <?= $erro ?></div>
                <?php endif; ?>

                <form method="POST">
                    <label>Usuário *</label>
                    <input type="text" name="username" placeholder="Mínimo 3 caracteres" required minlength="3">

                    <label>Senha *</label>
                    <input type="password" name="password" placeholder="Mínimo 4 caracteres" required minlength="4">

                    <label>Confirmar Senha *</label>
                    <input type="password" name="confirmar" placeholder="Digite novamente" required>

                    <button type="submit" class="btn btn-save btn-block" style="width:100%;">Criar Conta</button>
                </form>

                <div class="link-login">
                    Já tem conta? <a href="<?= appUrl('/admin/login.php') ?>">Faça login</a>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function copiarToken() {
        var t = document.getElementById('token');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t.textContent).then(() => alert('✅ Token copiado!'));
        } else {
            var r = document.createElement('textarea');
            r.value = t.textContent;
            document.body.appendChild(r);
            r.select();
            document.execCommand('copy');
            document.body.removeChild(r);
            alert('✅ Token copiado!');
        }
    }
</script>
</body>
</html>