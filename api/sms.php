<?php
declare(strict_types=1);
require_once __DIR__ . '/_helpers.php';

$user = apiRequireToken();
$userId = (int)$user['id'];
$path = getClientUpdateDirectory($pdo, $userId) . '/sms.json';

if (!is_file($path)) apiJsonError('Arquivo de SMS não encontrado.', 404);
$content = file_get_contents($path);
if ($content === false) apiJsonError('Erro ao ler o arquivo de SMS.', 500);
$data = json_decode($content, true);
if (!is_array($data)) apiJsonError('Erro ao decodificar o arquivo de SMS.', 500);
apiJsonResponse($data);
