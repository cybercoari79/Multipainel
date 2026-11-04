<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireLogin();

$baseUrl = '';
$activePage = 'profile';
$pageTitle = 'Meu Perfil';
$userId = currentUserId();

$error = '';
$success = '';
$novoToken = '';

// ============================================================
// DADOS DO USUÁRIO
// ============================================================
$userData = getUser($pdo, $userId) ?: [];
$tokenAtual = '';

// ============================================================
// ALTERAR SENHA
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $result = changeUserPassword(
        $pdo,
        $userId,
        (string)($_POST['current_password'] ?? ''),
        (string)($_POST['new_password'] ?? ''),
        (string)($_POST['confirm_password'] ?? '')
    );

    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// ============================================================
// GERAR NOVO TOKEN
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'generate_token') {
    $novoToken = regenerateApiToken($pdo, $userId);
    if ($novoToken !== null) {
        // O novo token só é mostrado nesta resposta. No banco fica somente o hash.
        $tokenAtual = $novoToken;
        $success = 'Novo token gerado com sucesso! Copie e guarde-o. Por segurança, ele não será exibido novamente.';
    } else {
        $error = 'Erro ao gerar novo token.';
    }
}

// ============================================================
// URL DA API
// ============================================================
$urlApi = getApiUrl();
$dominioAtual = getPublicBaseUrl();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - V2</title>
    <link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
    <style>
        .token-box {
            background: var(--surface-2);
            padding: 12px 15px;
            border-radius: 8px;
            word-break: break-all;
            font-family: monospace;
            font-size: 13px;
            border: 1px solid var(--border);
            margin: 8px 0 12px 0;
            color: var(--text);
        }
        .btn-copy {
            background: var(--success);
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-copy:hover {
            background: #15803d;
        }
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 25px 0;
        }
        .token-section {
            background: var(--surface-2);
            padding: 18px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            margin-top: 10px;
        }
        .dominio-info {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 12px;
            font-size: 13px;
            color: var(--muted);
        }
        .dominio-info strong {
            color: var(--text);
        }
    </style>
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../includes/header.php'; ?>
        <div class="container-form">

            <!-- ============================================ -->
            <!-- NOME DO USUÁRIO -->
            <!-- ============================================ -->
            <div style="background:rgba(255,107,0,0.08);border:1px solid rgba(255,107,0,0.15);border-radius:8px;padding:12px;margin-bottom:25px;text-align:center;">
                <span style="color:var(--muted);">Usuário:</span>
                <strong style="color:#ff6b00;font-size:18px;"><?= htmlspecialchars($userData['username'] ?? $_SESSION['v2_username'] ?? 'Usuário') ?></strong>
            </div>

            <!-- ============================================ -->
            <!-- MENSAGENS -->
            <!-- ============================================ -->
            <?php if ($error): ?>
                <div class="alert alert-error" style="color:#991b1b;background:#fee2e2;border:1px solid #fecaca;padding:12px;border-radius:8px;margin-bottom:15px;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" style="color:#166534;background:#dcfce7;border:1px solid #bbf7d0;padding:12px;border-radius:8px;margin-bottom:15px;">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- ============================================ -->
            <!-- 1. ALTERAR SENHA -->
            <!-- ============================================ -->
            <div class="card" style="margin-bottom:25px;">
                <div class="card-header"><h2>🔒 Alterar Senha</h2></div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Senha atual *</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nova senha * (mínimo 4 caracteres)</label>
                                <input type="password" name="new_password" class="form-control" minlength="4" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmar nova senha *</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="4" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Alterar Senha</button>
                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 2. GERENCIAR TOKEN DA API -->
            <!-- ============================================ -->
            <div class="card">
                <div class="card-header"><h2>🔑 Token de API</h2></div>
                <div class="card-body">
                    <p style="color:var(--muted); font-size:14px; margin-bottom:12px;">
                        Este token é usado pelo aplicativo (cliente) para baixar as configurações do servidor via API.
                    </p>

                    <?php if ($tokenAtual): ?>
                        <div class="token-section">
                            <label style="font-weight:600; display:block; margin-bottom:5px;">Token atual:</label>
                            <div class="token-box" id="currentToken"><?= htmlspecialchars($tokenAtual) ?></div>
                            <button class="btn-copy" onclick="copiarToken('currentToken')">📋 Copiar token</button>
                        </div>
                    <?php else: ?>
                        <div style="color:var(--muted); padding:12px; border:1px dashed var(--border); border-radius:8px; text-align:center;">
                            ⚠️ Nenhum token gerado ainda. Clique no botão abaixo para criar um.
                        </div>
                    <?php endif; ?>

                    <form method="POST" style="margin-top:15px;">
                        <input type="hidden" name="action" value="generate_token">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('⚠️ Ao gerar um novo token, o token antigo será revogado imediatamente. Deseja continuar?')">
                            🔄 Gerar novo token
                        </button>
                    </form>

                    <!-- ============================================ -->
                    <!-- URL DA API (COM DOMÍNIO ATUAL) -->
                    <!-- ============================================ -->
                    <div class="dominio-info">
                        <strong>📡 URL da API (automática):</strong><br>
                        <code style="background:var(--surface-2); padding:4px 8px; border-radius:4px; display:block; margin-top:4px; word-break:break-all;">
                            <?= htmlspecialchars($urlApi . '?token=SEU_TOKEN_AQUI') ?>
                        </code>
                        <span style="font-size:12px; color:var(--muted); display:block; margin-top:4px;">
                            🌐 Domínio detectado: <strong><?= htmlspecialchars($dominioAtual, ENT_QUOTES, 'UTF-8') ?></strong>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function copiarToken(elementId) {
    const tokenElement = document.getElementById(elementId);
    if (!tokenElement) return;
    const token = tokenElement.innerText;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(token).then(() => {
            alert('✅ Token copiado para a área de transferência!');
        }).catch(() => {
            fallbackCopiar(token);
        });
    } else {
        fallbackCopiar(token);
    }
}

function fallbackCopiar(texto) {
    const input = document.createElement('input');
    input.value = texto;
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.select();
    try {
        document.execCommand('copy');
        alert('✅ Token copiado!');
    } catch (e) {
        alert('❌ Não foi possível copiar. Selecione o token manualmente.');
    }
    document.body.removeChild(input);
}
</script>

</body>
</html>