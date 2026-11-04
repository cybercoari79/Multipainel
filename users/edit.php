<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();
$baseUrl = '../';
$activePage = 'users';
$pageTitle = 'Editar Usuário';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateUser(
        $pdo,
        $id,
        (string)($_POST['username'] ?? ''),
        (string)($_POST['status'] ?? 'active'),
        ($_POST['password'] ?? '') !== '' ? (string)$_POST['password'] : null
    );

    if ($result['success']) {
        generateClientUpdateFiles($pdo, $id);
    redirect('index.php');
    }
    $error = $result['message'];
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Editar Usuário - V2</title>
<link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../includes/header.php'; ?>
        <div class="container-form">
            <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST">
                <label>Usuário *</label><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                <label>Nova Senha (deixe em branco para manter)</label><input type="password" name="password">
                <label>Status</label><select name="status"><option value="active" <?= $user['status']=='active'?'selected':'' ?>>Ativo</option><option value="inactive" <?= $user['status']=='inactive'?'selected':'' ?>>Inativo</option><option value="suspended" <?= $user['status']=='suspended'?'selected':'' ?>>Suspenso</option></select>
                <div class="actions"><button type="submit" class="btn btn-save">Salvar</button><a href="index.php" class="btn-cancel">Cancelar</a></div>
            </form>
        </div>
    </div>
</div>
</body></html>