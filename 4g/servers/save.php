<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

requireLogin();

$userId = currentUserId();

if ($userId <= 0) {
    redirect('/admin/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/4g/servers.php');
}

try {

    // CSRF
    if (function_exists('cpVerifyCsrf')) {
        cpVerifyCsrf();
    } elseif (function_exists('verifyCsrfToken')) {
        if (!verifyCsrfToken()) {
            throw new RuntimeException('Token CSRF inválido.');
        }
    }

    $name = trim((string)($_POST['name'] ?? ''));

    if ($name === '') {
        throw new RuntimeException('Informe o nome do servidor.');
    }

    $description = trim((string)($_POST['description'] ?? ''));
    $type = trim((string)($_POST['type'] ?? ''));
    $info = trim((string)($_POST['info'] ?? 'Tlsws'));

    $serverIp = trim((string)($_POST['server_ip'] ?? ''));
    $checkUser = trim((string)($_POST['check_user'] ?? ''));

    $serverPort = trim((string)($_POST['server_port'] ?? ''));
    $sslPort = trim((string)($_POST['ssl_port'] ?? ''));

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $payload = (string)($_POST['payload'] ?? '');
    $proxyIp = (string)($_POST['proxy_ip'] ?? '');
    $proxyPort = trim((string)($_POST['proxy_port'] ?? ''));

    $sni = trim((string)($_POST['sni'] ?? ''));
    $path = trim((string)($_POST['path'] ?? ''));

    $flagUrl = trim((string)($_POST['flag_url'] ?? ''));

    $colorName = trim((string)($_POST['color_name'] ?? '#ffffff'));
    $colorDescription = trim((string)($_POST['color_description'] ?? '#ffffff'));
    $color = trim((string)($_POST['color'] ?? '#ffffff'));

    $status = (($_POST['status'] ?? 'active') === 'inactive')
        ? 'inactive'
        : 'active';

    $name = mb_substr($name, 0, 150);
    $description = mb_substr($description, 0, 255);
    $type = mb_substr($type, 0, 50);
    $info = mb_substr($info, 0, 100);

    $serverIp = mb_substr($serverIp, 0, 255);
    $checkUser = mb_substr($checkUser, 0, 255);

    $serverPort = mb_substr($serverPort, 0, 20);
    $sslPort = mb_substr($sslPort, 0, 20);

    $username = mb_substr($username, 0, 255);
    $password = mb_substr($password, 0, 255);

    $payload = mb_substr($payload, 0, 1000000);
    $proxyIp = mb_substr($proxyIp, 0, 255);
    $proxyPort = mb_substr($proxyPort, 0, 20);

    $sni = mb_substr($sni, 0, 255);
    $path = mb_substr($path, 0, 255);
    $flagUrl = mb_substr($flagUrl, 0, 255);

    $colorName = mb_substr($colorName, 0, 30);
    $colorDescription = mb_substr($colorDescription, 0, 30);
    $color = mb_substr($color, 0, 30);

    $sql = "
        INSERT INTO 4g_servers (
            user_id,
            name,
            color_name,
            description,
            color_description,
            type,
            flag_url,
            server_ip,
            check_user,
            server_port,
            ssl_port,
            username,
            password,
            payload,
            proxy_ip,
            proxy_port,
            sni,
            path,
            color,
            info,
            status
        ) VALUES (
            :user_id,
            :name,
            :color_name,
            :description,
            :color_description,
            :type,
            :flag_url,
            :server_ip,
            :check_user,
            :server_port,
            :ssl_port,
            :username,
            :password,
            :payload,
            :proxy_ip,
            :proxy_port,
            :sni,
            :path,
            :color,
            :info,
            :status
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $userId,
        ':name' => $name,
        ':color_name' => $colorName,
        ':description' => $description,
        ':color_description' => $colorDescription,
        ':type' => $type,
        ':flag_url' => $flagUrl,
        ':server_ip' => $serverIp,
        ':check_user' => $checkUser,
        ':server_port' => $serverPort,
        ':ssl_port' => $sslPort,
        ':username' => $username,
        ':password' => $password,
        ':payload' => $payload,
        ':proxy_ip' => $proxyIp,
        ':proxy_port' => $proxyPort,
        ':sni' => $sni,
        ':path' => $path,
        ':color' => $color,
        ':info' => $info,
        ':status' => $status
    ]);

    redirect('/4g/servers.php');

} catch (Throwable $e) {

    error_log(
        'ConnectPlus - erro ao salvar servidor: '
        . $e->getMessage()
    );

    http_response_code(500);

    echo '<!DOCTYPE html>';
    echo '<html lang="pt-BR">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Erro ao salvar servidor</title>';
    echo '<style>';
    echo 'body{font-family:Arial;background:#0a0e17;color:#fff;padding:30px}';
    echo '.box{max-width:700px;margin:auto;background:#111927;padding:25px;border-radius:12px}';
    echo '.error{color:#ff6b6b;white-space:pre-wrap}';
    echo 'a{color:#ff6b00}';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<div class="box">';
    echo '<h2>Erro ao salvar o servidor</h2>';
    echo '<p class="error">'
        . htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
        . '</p>';
    echo '<p><a href="'
        . htmlspecialchars(
            appUrl('/admin/servers/add.php'),
            ENT_QUOTES,
            'UTF-8'
        )
        . '">Voltar</a></p>';
    echo '</div>';
    echo '</body>';
    echo '</html>';
    exit;
}