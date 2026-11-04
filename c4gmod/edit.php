<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();
$baseUrl = '../';
$activePage = 'servers';
$pageTitle = 'Editar Servidor';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM 4g_servers WHERE id = ? AND user_id = ?');
$stmt->execute([$id, currentUserId()]);
$server = $stmt->fetch();
if (!$server) redirect('index.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfToken();
    $stmt = $pdo->prepare('UPDATE 4g_servers SET name=?, color_name=?, description=?, color_description=?, type=?, flag_url=?, server_ip=?, check_user=?, server_port=?, ssl_port=?, username=?, password=?, color=? WHERE id=? AND user_id=?');
    $stmt->execute([$_POST['name'], $_POST['color_name']??'#ffffff', $_POST['description'], $_POST['color_description']??'#ffffff', $_POST['type'], $_POST['flag_url'], $_POST['server_ip'], $_POST['check_user'], $_POST['server_port'], $_POST['ssl_port'], $_POST['username'], $_POST['password'], $_POST['color']??'#ffffff', $id, currentUserId()]);
    
    logActivity($pdo, 'Editou servidor', 'Servidores', 'Servidor ID atualizado.', currentUserId());
    redirect('/4g/index.php');
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Editar Servidor - V2</title>
<link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../../includes/header.php'; ?>
        <div class="container-form">
            <form method="POST">
                <?= csrfField() ?>
                <label>Nome *</label><input type="text" name="name" value="<?= htmlspecialchars($server['name']) ?>" required>
                <label>IP do Servidor *</label><input type="text" name="server_ip" value="<?= htmlspecialchars($server['server_ip']) ?>" required>
                <label>Porta</label><input type="text" name="server_port" value="<?= htmlspecialchars($server['server_port']) ?>">
                <label>Porta SSL</label><input type="text" name="ssl_port" value="<?= htmlspecialchars($server['ssl_port']) ?>">
                <label>Tipo</label><select name="type"><option value="premium" <?= $server['type']=='premium'?'selected':'' ?>>Premium</option><option value="free" <?= $server['type']=='free'?'selected':'' ?>>Free</option><option value="teste" <?= $server['type']=='teste'?'selected':'' ?>>Teste</option></select>
                <label>Usuário</label><input type="text" name="username" value="<?= htmlspecialchars($server['username']) ?>">
                <label>Senha</label><input type="text" name="password" value="<?= htmlspecialchars($server['password']) ?>">
                <label>Check User URL</label><input type="text" name="check_user" value="<?= htmlspecialchars($server['check_user']) ?>">
                <label>Flag URL</label><input type="text" name="flag_url" value="<?= htmlspecialchars($server['flag_url']) ?>">
                <label>Descrição</label><input type="text" name="description" value="<?= htmlspecialchars($server['description']) ?>">
                <label>Cor</label><input type="color" name="color" value="<?= htmlspecialchars($server['color']) ?>" class="color-input">
                <div class="actions"><button type="submit" class="btn btn-save">Salvar</button><a href="index.php" class="btn-cancel">Cancelar</a></div>
            </form>
        </div>
    </div>
</div>
</body>
</html>