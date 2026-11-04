<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();

$baseUrl = '../';
$activePage = 'networks';
$pageTitle = 'Adicionar Rede';
$userId = currentUserId();

$error = '';

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

    $name = trim((string)($_POST['name'] ?? ''));
    $operator = trim((string)($_POST['operator'] ?? ''));
    $proxyIp = trim((string)($_POST['proxy_ip'] ?? ''));
    $proxyPort = trim((string)($_POST['proxy_port'] ?? '443'));
    $sni = trim((string)($_POST['sni'] ?? ''));
    $tlsIp = trim((string)($_POST['tls_ip'] ?? ''));
    $payload = (string)($_POST['payload'] ?? '');
    $info = trim((string)($_POST['info'] ?? 'Tlsws'));
    $flagUrl = trim((string)($_POST['flag_url'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $color = trim((string)($_POST['color'] ?? '#0000ff'));
    $colorName = trim((string)($_POST['color_name'] ?? '#ffffff'));
    $colorDescription = trim((string)($_POST['color_description'] ?? '#ffffff'));

    if ($name === '') {
        $error = 'Informe o nome da rede.';
    } else {
        try {
            $pdo->beginTransaction();

            // Insere na tabela networks
            $stmt = $pdo->prepare('
                INSERT INTO 4g_networks (
                    user_id, name, color_name, description, color_description,
                    flag_url, payload_file, proxy_ip, proxy_port, sni,
                    tls_ip, color, info, operator
                ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?
                )
            ');
            $stmt->execute([
                $userId,
                $name,
                $colorName,
                $description,
                $colorDescription,
                $flagUrl,
                '',
                $proxyIp,
                $proxyPort,
                $sni,
                $tlsIp,
                $color,
                $info,
                $operator
            ]);

            $networkId = (int)$pdo->lastInsertId();

            // Insere o payload se houver
            if ($payload !== '') {
                $stmt = $pdo->prepare('
                    INSERT INTO 4g_payloads (
                        user_id, name, content, type, network_id
                    ) VALUES (?, ?, ?, ?, ?)
                ');
                $stmt->execute([
                    $userId,
                    $name . ' Payload',
                    $payload,
                    'sni',
                    $networkId
                ]);
            }

            $pdo->commit();
            
    logActivity($pdo, 'Criou rede', 'Redes', 'Rede criada.', currentUserId());

            // Resposta AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => true]);
                exit;
            }

            redirect('index.php');

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Não foi possível salvar a rede.';
            error_log('Erro ao adicionar rede: ' . $e->getMessage());
            
            // Se for AJAX, retorna erro
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Rede - V2</title>
    <link rel="stylesheet" href="<?= appUrl('/assets/css/style.css?v=1') ?>">
</head>
<body>
<div class="dashboard">
    <?php include_once __DIR__ . '/../../includes/menu.php'; ?>
    <div class="main-content">
        <?php include_once __DIR__ . '/../../includes/header.php'; ?>
        <div class="container-form">
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form id="networkForm" method="POST" action="">
                <label for="name">Nome *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

                <label for="operator">Operadora</label>
                <select id="operator" name="operator">
                    <?php
                    $operators = ['TIM', 'VIVO', 'CLARO', 'OI'];
                    $selectedOperator = $_POST['operator'] ?? 'TIM';
                    foreach ($operators as $op):
                    ?>
                        <option value="<?= htmlspecialchars($op, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedOperator === $op ? 'selected' : '' ?>><?= htmlspecialchars($op, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="proxy_ip">Proxy IP</label>
                <input type="text" id="proxy_ip" name="proxy_ip" value="<?= htmlspecialchars($_POST['proxy_ip'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <label for="proxy_port">Proxy Porta</label>
                <input type="text" id="proxy_port" name="proxy_port" value="<?= htmlspecialchars($_POST['proxy_port'] ?? '443', ENT_QUOTES, 'UTF-8') ?>">

                <label for="sni">SNI</label>
                <input type="text" id="sni" name="sni" value="<?= htmlspecialchars($_POST['sni'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <label for="tls_ip">TlsIP</label>
                <input type="text" id="tls_ip" name="tls_ip" value="<?= htmlspecialchars($_POST['tls_ip'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <label for="payload">Payload</label>
                <textarea id="payload" name="payload" rows="10" placeholder="Cole o conteúdo do payload aqui..."><?= htmlspecialchars($_POST['payload'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                <label for="info">Info</label>
                <select id="info" name="info">
                    <?php
                    $infos = ['Tlsws', 'XHTTP', 'Proxy'];
                    $selectedInfo = $_POST['info'] ?? 'Tlsws';
                    foreach ($infos as $item):
                    ?>
                        <option value="<?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedInfo === $item ? 'selected' : '' ?>><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="flag_url">Flag URL</label>
                <input type="text" id="flag_url" name="flag_url" value="<?= htmlspecialchars($_POST['flag_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <label for="description">Descrição</label>
                <input type="text" id="description" name="description" value="<?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <label for="color">Cor</label>
                <input type="color" id="color" name="color" value="<?= htmlspecialchars($_POST['color'] ?? '#0000ff', ENT_QUOTES, 'UTF-8') ?>" class="color-input">

                <!-- Campos ocultos para cores adicionais -->
                <input type="hidden" name="color_name" value="<?= htmlspecialchars($_POST['color_name'] ?? '#ffffff', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="color_description" value="<?= htmlspecialchars($_POST['color_description'] ?? '#ffffff', ENT_QUOTES, 'UTF-8') ?>">

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
document.getElementById('networkForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Monta objeto com todos os dados do formulário
    var data = {};
    new FormData(this).forEach((value, key) => {
        data[key] = value;
    });

    // Converte para JSON e codifica em Base64
    var jsonString = JSON.stringify(data);
    var base64 = btoa(unescape(encodeURIComponent(jsonString)));

    // Envia via AJAX
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