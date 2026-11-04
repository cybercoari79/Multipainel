<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();

$baseUrl = '../';
$activePage = 'networks';
$pageTitle = 'Editar Rede';

$id = (int)($_GET['id'] ?? 0);
$userId = currentUserId();

// Busca a rede
$stmt = $pdo->prepare('SELECT * FROM 4g_networks WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
$network = $stmt->fetch();
if (!$network) redirect('index.php');

// Busca o payload associado (opcional)
$stmt = $pdo->prepare('SELECT content FROM 4g_payloads WHERE network_id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, $userId]);
$payloadRow = $stmt->fetch();
$payloadContent = $payloadRow ? $payloadRow['content'] : '';

// ============================================================
// TRATAMENTO PARA REQUISIÇÕES AJAX COM PAYLOAD CODIFICADO
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se veio via AJAX com payload_base64, decodifica
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['payload_base64'])) {
            $decoded = base64_decode($input['payload_base64']);
            if ($decoded !== false) {
                $data = json_decode($decoded, true);
                if (is_array($data)) {
                    $_POST = $data;
                }
            }
        }
    }

    // Atualiza a rede
    $stmt = $pdo->prepare('
        UPDATE 4g_networks SET 
            name=?, color_name=?, description=?, color_description=?,
            flag_url=?, proxy_ip=?, proxy_port=?, sni=?, tls_ip=?,
            color=?, info=?, operator=?
        WHERE id=? AND user_id=?
    ');
    $stmt->execute([
        $_POST['name'] ?? '',
        $_POST['color_name'] ?? '#ffffff',
        $_POST['description'] ?? '',
        $_POST['color_description'] ?? '#ffffff',
        $_POST['flag_url'] ?? '',
        $_POST['proxy_ip'] ?? '',
        $_POST['proxy_port'] ?? '',
        $_POST['sni'] ?? '',
        $_POST['tls_ip'] ?? '',
        $_POST['color'] ?? '#0000ff',
        $_POST['info'] ?? '',
        $_POST['operator'] ?? '',
        $id,
        $userId
    ]);

    // Atualiza o payload se existir
    if (isset($_POST['payload']) && $_POST['payload'] !== '') {
        $stmt = $pdo->prepare('UPDATE 4g_payloads SET content = ? WHERE network_id = ? AND user_id = ?');
        $stmt->execute([$_POST['payload'], $id, $userId]);
        if ($stmt->rowCount() === 0) {
            // Se não existir, insere
            $stmt = $pdo->prepare('INSERT INTO 4g_payloads (user_id, name, content, type, network_id) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $userId,
                $_POST['name'] . ' Payload',
                $_POST['payload'],
                'sni',
                $id
            ]);
        }
    }

    
    logActivity($pdo, 'Editou rede', 'Redes', 'Rede ID atualizada.', currentUserId());

    // Se for AJAX, retorna sucesso
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit;
    }

    redirect('index.php');
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Rede - V2</title>
    <link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../includes/header.php'; ?>
        <div class="container-form">
            <form id="editForm" method="POST">
                <label>Nome *</label>
                <input type="text" name="name" value="<?= e($network['name']) ?>" required>

                <label>Operadora</label>
                <select name="operator">
                    <?php
                    $operators = ['TIM', 'VIVO', 'CLARO', 'OI'];
                    foreach ($operators as $op):
                    ?>
                        <option value="<?= $op ?>" <?= ($network['operator'] ?? '') == $op ? 'selected' : '' ?>><?= $op ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Proxy IP</label>
                <input type="text" name="proxy_ip" value="<?= e($network['proxy_ip']) ?>">

                <label>Proxy Porta</label>
                <input type="text" name="proxy_port" value="<?= e($network['proxy_port']) ?>">

                <label>SNI</label>
                <input type="text" name="sni" value="<?= e($network['sni']) ?>">

                <label>TlsIP</label>
                <input type="text" name="tls_ip" value="<?= e($network['tls_ip']) ?>">

                <label>Payload</label>
                <textarea name="payload" rows="10"><?= e($payloadContent) ?></textarea>

                <label>Info</label>
                <select name="info">
                    <option value="Tlsws" <?= ($network['info'] ?? '') == 'Tlsws' ? 'selected' : '' ?>>Tlsws</option>
                    <option value="XHTTP" <?= ($network['info'] ?? '') == 'XHTTP' ? 'selected' : '' ?>>XHTTP</option>
                    <option value="Proxy" <?= ($network['info'] ?? '') == 'Proxy' ? 'selected' : '' ?>>Proxy</option>
                </select>

                <label>Flag URL</label>
                <input type="text" name="flag_url" value="<?= e($network['flag_url']) ?>">

                <label>Descrição</label>
                <input type="text" name="description" value="<?= e($network['description']) ?>">

                <label>Cor</label>
                <input type="color" name="color" value="<?= e($network['color']) ?>" class="color-input">

                <!-- Campos ocultos -->
                <input type="hidden" name="color_name" value="<?= e($network['color_name']) ?>">
                <input type="hidden" name="color_description" value="<?= e($network['color_description']) ?>">

                <div class="actions">
                    <button type="submit" class="btn btn-save">Salvar</button>
                    <a href="index.php" class="btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ============================================================
// CODIFICA TODOS OS DADOS EM BASE64 ANTES DE ENVIAR
// ============================================================
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var data = {};
    new FormData(this).forEach((value, key) => {
        data[key] = value;
    });

    var jsonString = JSON.stringify(data);
    var base64 = btoa(unescape(encodeURIComponent(jsonString)));

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ payload_base64: base64 })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.location.href = 'index.php';
        } else {
            alert('Erro ao salvar: ' + (result.error || 'Erro desconhecido.'));
        }
    })
    .catch(() => {
        alert('Falha na requisição. Tente novamente.');
    });
});
</script>
</body>
</html>