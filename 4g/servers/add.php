<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();
$baseUrl = '../';
$activePage = 'servers';
$pageTitle = 'Adicionar Servidor';
$userId = currentUserId();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO 4g_servers (user_id, name, color_name, description, color_description, type, flag_url, server_ip, check_user, server_port, ssl_port, username, password, color) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$userId, $_POST['name'], $_POST['color_name']??'#ffffff', $_POST['description'], $_POST['color_description']??'#ffffff', $_POST['type'], $_POST['flag_url'], $_POST['server_ip'], $_POST['check_user'], $_POST['server_port'], $_POST['ssl_port'], $_POST['username'], $_POST['password'], $_POST['color']??'#ffffff']);
    
    logActivity($pdo, 'Criou servidor', 'Servidores', 'Servidor criado.', currentUserId());
    redirect('/4g/servers.php');
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Adicionar Servidor - V2</title>
<link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../../includes/header.php'; ?>
        <div class="container-form">
            <form method="POST">
                <label>Nome *</label><input type="text" name="name" required>
                <label>IP do Servidor *</label><input type="text" name="server_ip" required>
                <label>Porta *</label><input type="text" name="server_port" value="22">
                <label>Porta SSL</label><input type="text" name="ssl_port" value="443">
                <label>Tipo</label><select name="type"><option value="premium">Premium</option><option value="free">Free</option><option value="teste">Teste</option></select>
                <label>Usuário</label><input type="text" name="username">
                <label>Senha</label><input type="text" name="password">
                <label>Check User URL</label><input type="text" name="check_user" placeholder="http://vp.dspeed.shop:9090/check?user={user}">
                <label>Flag URL</label><input type="text" name="flag_url">
                <label>Descrição</label><input type="text" name="description">
                <label>Cor</label><input type="color" name="color" value="#ffffff" class="color-input">
                <div class="actions"><button type="submit" class="btn btn-save">Salvar</button><a href="index.php" class="btn-cancel">Cancelar</a></div>
            </form>
        </div>
    </div>
</div>
</body></html>