<?php
declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

$token = trim((string)($_GET['token'] ?? ''));
if ($token === '') {
    apiJsonError('Token não informado.', 401);
}

$user = verifyApiToken($pdo, $token);
if (!$user) {
    apiJsonError('Token inválido ou usuário inativo.', 401);
}
$userId = (int)$user['id'];

$path = getClientUpdateDirectory($pdo, $userId) . '/theme.json';

if (!is_file($path)) {
    apiJsonError('Arquivo de tema não encontrado.', 404);
}

$content = file_get_contents($path);
if ($content === false) {
    apiJsonError('Erro ao ler o arquivo de tema.', 500);
}

$data = json_decode($content, true);
if (!is_array($data)) {
    apiJsonError('Erro ao decodificar o arquivo de tema.', 500);
}

apiJsonResponse([
    'Version' => (string)($data['Version'] ?? '0'),
    'Update' => getThemeApiUrl() . '?token=' . rawurlencode($token),
    'AppName' => (string)($data['AppName'] ?? 'ConnectPlus'),
    'ImgFundo' => apiAbsoluteUrl($data['ImgFundo'] ?? ''),
    'ImgLogo' => apiAbsoluteUrl($data['ImgLogo'] ?? ''),
    'ImgBanner' => apiAbsoluteUrl($data['ImgBanner'] ?? ''),
    'ImgMenu' => apiAbsoluteUrl($data['ImgMenu'] ?? ''),
    'ImgLogs' => apiAbsoluteUrl($data['ImgLogs'] ?? ''),
    'ImgCheck' => apiAbsoluteUrl($data['ImgCheck'] ?? ''),
    'ImgUser' => apiAbsoluteUrl($data['ImgUser'] ?? ''),
    'ImgPass' => apiAbsoluteUrl($data['ImgPass'] ?? ''),
    'ImgUpdate' => apiAbsoluteUrl($data['ImgUpdate'] ?? ''),
    'ColorOne' => (string)($data['ColorOne'] ?? '#333333'),
    'ColorTwo' => (string)($data['ColorTwo'] ?? '#333333'),
    'ColorStarter' => (string)($data['ColorStarter'] ?? '#333333'),
    'ColorDialogs' => (string)($data['ColorDialogs'] ?? '#ffffff'),
    'ColorButtons' => (string)($data['ColorButtons'] ?? '#667eea')
]);
