<?php
// ============================================================
// TEMA.PHP - GERENCIADOR COMPLETO DE TEMA
// ============================================================

declare(strict_types=1);

// ============================================================
// EXIBIÇÃO DE ERROS
// ============================================================
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// ============================================================
// BOOTSTRAP / LOGIN
// ============================================================
require_once __DIR__ . '/../includes/bootstrap.php';

requireLogin();

// ============================================================
// CONFIGURAÇÕES DA PÁGINA
// ============================================================
$baseUrl    = '';
$activePage = 'tema';
$pageTitle  = 'Tema do Aplicativo';
$userId = currentUserId();

// Administrador pode editar o tema de qualquer usuário.
// Usuário comum permanece limitado ao próprio tema.
if (isAdmin() && isset($_GET['user_id']) && ctype_digit((string)$_GET['user_id'])) {
    $requestedUserId = (int)$_GET['user_id'];
    if ($requestedUserId > 0) {
        $stmtUser = $pdo->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
        $stmtUser->execute([$requestedUserId]);
        if ($stmtUser->fetchColumn() !== false) {
            $userId = $requestedUserId;
        }
    }
}

$mensagem = '';
$tipo     = '';

// ============================================================
// FUNÇÕES AUXILIARES
// ============================================================

if (!function_exists('e')) {
    function e($valor): string
    {
        return htmlspecialchars(
            (string)($valor ?? ''),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}

/**
 * Detecta o protocolo atual.
 */
function getProtocolo(): string
{
    if (
        isset($_SERVER['HTTPS']) &&
        (
            $_SERVER['HTTPS'] === 'on' ||
            $_SERVER['HTTPS'] === '1'
        )
    ) {
        return 'https';
    }

    if (
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
    ) {
        return 'https';
    }

    return 'http';
}

/**
 * Retorna domínio atual.
 */
function getDominioAtual(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return getProtocolo() . '://' . $host;
}

/**
 * Descobre a pasta base da instalação.
 *
 * Exemplo:
 * /v2/admin/tema.php
 * retorna:
 * /v2
 */
function getBasePath(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';

    $adminPath = str_replace('\\', '/', dirname($script));
    $basePath  = str_replace('\\', '/', dirname($adminPath));

    if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
        return '';
    }

    return '/' . trim($basePath, '/');
}

/**
 * Retorna URL pública da pasta theme.
 */
function getThemePublicUrl(): string
{
    return getDominioAtual() . getBasePath() . '/theme';
}

/**
 * Retorna URL pública do JSON.
 */
function getThemeJsonUrl(): string
{
    return getThemePublicUrl() . '/config.json';
}

/**
 * URL pública do tema individual do usuário.
 */
function getUserThemeJsonUrl($userId): string
{
    global $pdo;
    return getClientUpdateUrl($pdo, (int)$userId, 'theme.json');
}

/**
 * Retorna caminho físico da pasta theme.
 */
function getThemeDirectory(): string
{
    return dirname(__DIR__) . '/theme';
}

/**
 * Incrementa versão.
 */
function incrementarVersao($versaoAtual): string
{
    $versaoAtual = trim((string)$versaoAtual);

    if ($versaoAtual === '') {
        return '1';
    }

    if ($versaoAtual === '0') {
        return '1';
    }

    if (is_numeric($versaoAtual)) {
        return (string)((int)$versaoAtual + 1);
    }

    // Caso seja algo como 1.0, 2.0 etc.
    if (preg_match('/^(\d+)(?:\.(\d+))?$/', $versaoAtual, $matches)) {
        return (string)((int)$matches[1] + 1);
    }

    return '1';
}

/**
 * Normaliza cor.
 *
 * Aceita:
 * #FFFFFF
 * #FFFFFFFF
 * rgb(...)
 * rgba(...)
 * transparent
 */
function normalizarCor(
    $cor,
    string $padrao = '#333333'
): string {
    $cor = trim((string)$cor);

    if ($cor === '') {
        return $padrao;
    }

    if (preg_match('/^#[0-9a-fA-F]{6}$/', $cor)) {
        return strtoupper($cor);
    }

    if (preg_match('/^#[0-9a-fA-F]{8}$/', $cor)) {
        return strtoupper($cor);
    }

    if (preg_match('/^rgb\(/i', $cor)) {
        return $cor;
    }

    if (preg_match('/^rgba\(/i', $cor)) {
        return $cor;
    }

    if (strtolower($cor) === 'transparent') {
        return 'transparent';
    }

    return $padrao;
}

/**
 * Cor compatível com input type=color.
 *
 * O input color aceita somente #RRGGBB.
 */
function corParaPicker($cor, string $padrao = '#333333'): string
{
    $cor = normalizarCor($cor, $padrao);

    if (preg_match('/^#[0-9A-Fa-f]{8}$/', $cor)) {
        return '#' . substr($cor, 1, 6);
    }

    if (preg_match('/^#[0-9A-Fa-f]{6}$/', $cor)) {
        return strtoupper($cor);
    }

    return $padrao;
}

/**
 * Garante que a tabela tenha a coluna app_name.
 */
function garantirColunaAppName(PDO $pdo): bool
{
    try {
        $stmt = $pdo->query(
            "SHOW COLUMNS FROM user_theme LIKE 'app_name'"
        );

        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            $pdo->exec(
                "ALTER TABLE user_theme
                 ADD COLUMN app_name VARCHAR(100)
                 DEFAULT 'ConnectPlus'"
            );
        }

        return true;

    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Garante as colunas de imagens adicionais do ConnectPlus.
 * Executado apenas na tabela user_theme, sem tocar em outros módulos.
 */
function garantirColunasImagensConnectPlus(PDO $pdo): bool
{
    $colunas = [
        'img_roteador' => "VARCHAR(255) DEFAULT ''",
        'img_download' => "VARCHAR(255) DEFAULT ''",
        'img_upload' => "VARCHAR(255) DEFAULT ''"
    ];
    try {
        foreach ($colunas as $nome => $definicao) {
            $stmt = $pdo->query("SHOW COLUMNS FROM user_theme LIKE " . $pdo->quote($nome));
            if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
                $pdo->exec("ALTER TABLE user_theme ADD COLUMN `{$nome}` {$definicao}");
            }
        }
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Carrega tema do usuário.
 */
function carregarTema(PDO $pdo, $userId): array
{
    $stmt = $pdo->prepare(
        'SELECT * FROM user_theme WHERE user_id = ? LIMIT 1'
    );

    $stmt->execute([$userId]);

    $tema = $stmt->fetch(PDO::FETCH_ASSOC);

    $padrao = [
        'user_id'       => $userId,
        'version'       => '1',
        'app_name'      => 'ConnectPlus',

        'img_fundo'     => 'https://cybercoari.com.br/cyber/imagens/fundo.png',
        'img_logo'      => 'https://cybercoari.com.br/cyber/imagens/logo.png',
        'img_banner'    => 'https://cybercoari.com.br/cyber/imagens/banner.png',
        'img_menu'      => 'https://cybercoari.com.br/cyber/imagens/menu.png',
        'img_logs'      => 'https://cybercoari.com.br/cyber/imagens/logs.png',
        'img_check'     => 'https://cybercoari.com.br/cyber/imagens/check.png',
        'img_user'      => 'https://cybercoari.com.br/cyber/imagens/user.png',
        'img_pass'      => 'https://cybercoari.com.br/cyber/imagens/pass.png',
        'img_update'    => 'https://cybercoari.com.br/cyber/imagens/update.png',
        'img_roteador'  => 'https://cybercoari.com.br/cyber/imagens/roteador.png',
        'img_download'  => 'https://cybercoari.com.br/cyber/imagens/download.png',
        'img_upload'    => 'https://cybercoari.com.br/cyber/imagens/upload.png',

        'color_one'     => '#333333',
        'color_two'     => '#333333',
        'color_starter' => '#333333',
        'color_dialogs' => '#ffffff',
        'color_buttons' => '#667eea'
    ];

    if (!$tema) {
        return $padrao;
    }

    foreach ($padrao as $campo => $valor) {
        if (
            !array_key_exists($campo, $tema) ||
            $tema[$campo] === null ||
            $tema[$campo] === ''
        ) {
            $tema[$campo] = $valor;
        }
    }

    return $tema;
}

/**
 * Salva/atualiza o tema sem depender de
 * ON DUPLICATE KEY UPDATE.
 *
 * Isso evita problema caso user_id não esteja
 * configurado como UNIQUE.
 */
function salvarTema(PDO $pdo, $userId, array $dados): bool
{
    $stmt = $pdo->prepare(
        'SELECT id FROM user_theme WHERE user_id = ? LIMIT 1'
    );

    $stmt->execute([$userId]);

    $existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existente) {

        $campos = [];
        $valores = [];

        foreach ($dados as $campo => $valor) {
            $campos[] = "`{$campo}` = ?";
            $valores[] = $valor;
        }

        $valores[] = $userId;

        $sql = "
            UPDATE user_theme
            SET " . implode(', ', $campos) . "
            WHERE user_id = ?
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute($valores);
    }

    $dados['user_id'] = $userId;

    $campos = array_keys($dados);
    $placeholders = array_fill(0, count($campos), '?');

    $sql = "
        INSERT INTO user_theme
        (`" . implode('`,`', $campos) . "`)
        VALUES (" . implode(',', $placeholders) . ")
    ";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute(array_values($dados));
}

/**
 * Gera o tema individual em /update/<cliente>/theme.json.
 */
function gerarThemeConfig(
    PDO $pdo,
    $userId,
    ?string $appName = null,
    ?string $versao = null
): bool {

    $tema = carregarTema($pdo, $userId);

    if ($appName === null || trim($appName) === '') {
        $appName = $tema['app_name'] ?? 'ConnectPlus';
    }

    if ($versao === null || trim($versao) === '') {
        $versao = $tema['version'] ?? '1';
    }

    $json = [
        'Version'      => (string)$versao,
        'Update'       => getUserThemeJsonUrl($userId),

        'AppName'      => $appName,

        'ImgFundo'     => $tema['img_fundo'],
        'ImgLogo'      => $tema['img_logo'],
        'ImgBanner'    => $tema['img_banner'],
        'ImgMenu'      => $tema['img_menu'],
        'ImgLogs'      => $tema['img_logs'],
        'ImgCheck'     => $tema['img_check'],
        'ImgUser'      => $tema['img_user'],
        'ImgPass'      => $tema['img_pass'],
        'ImgUpdate'    => $tema['img_update'],
        'ImgRoteador'  => $tema['img_roteador'],
        'ImgDownload'  => $tema['img_download'],
        'ImgUpload'    => $tema['img_upload'],

        'ColorOne'     => $tema['color_one'],
        'ColorTwo'     => $tema['color_two'],
        'ColorStarter' => $tema['color_starter'],
        'ColorDialogs' => $tema['color_dialogs'],
        'ColorButtons' => $tema['color_buttons']
    ];

    $jsonString = json_encode(
        $json,
        JSON_PRETTY_PRINT |
        JSON_UNESCAPED_SLASHES |
        JSON_UNESCAPED_UNICODE
    );

    if ($jsonString === false) {
        return false;
    }

    $pastaBase = getThemeDirectory();
    $pastaUsuario = getClientUpdateDirectory($pdo, (int)$userId);

    if (!is_dir($pastaUsuario)) {
        if (!mkdir($pastaUsuario, 0755, true) && !is_dir($pastaUsuario)) {
            return false;
        }
    }

    $arquivoUsuario = $pastaUsuario . '/theme.json';
    $ok = file_put_contents(
        $arquivoUsuario,
        $jsonString . PHP_EOL,
        LOCK_EX
    ) !== false;

    return $ok;
}

/**
 * Verifica upload de imagem.
 */
function validarImagemUpload(array $arquivo): array
{
    if (
        !isset($arquivo['error']) ||
        $arquivo['error'] !== UPLOAD_ERR_OK
    ) {
        return [
            false,
            'Erro no envio do arquivo.'
        ];
    }

    if (
        !isset($arquivo['tmp_name']) ||
        !is_uploaded_file($arquivo['tmp_name'])
    ) {
        return [
            false,
            'Upload inválido.'
        ];
    }

    if ($arquivo['size'] > 10 * 1024 * 1024) {
        return [
            false,
            'A imagem não pode ultrapassar 10 MB.'
        ];
    }

    $mime = '';

    if (function_exists('mime_content_type')) {
        $mime = mime_content_type($arquivo['tmp_name']);
    }

    $permitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];

    if (!isset($permitidos[$mime])) {
        return [
            false,
            'Formato de imagem não permitido.'
        ];
    }

    return [
        true,
        $permitidos[$mime]
    ];
}

// ============================================================
// GARANTE COLUNA APP_NAME
// ============================================================
garantirColunaAppName($pdo);
garantirColunasImagensConnectPlus($pdo);

// ============================================================
// CARREGA TEMA
// ============================================================
$tema = carregarTema($pdo, $userId);

$appName = $tema['app_name'] ?? 'ConnectPlus';
$versaoAtual = $tema['version'] ?? '1';

// ============================================================
// CAMPOS DE IMAGEM
// ============================================================
$imageFields = [
    'img_fundo'   => 'ImgFundo (Fundo)',
    'img_logo'    => 'ImgLogo (Logo)',
    'img_banner'  => 'ImgBanner (Banner)',
    'img_menu'    => 'ImgMenu (Menu)',
    'img_logs'    => 'ImgLogs (Logs)',
    'img_check'   => 'ImgCheck (Check)',
    'img_user'    => 'ImgUser (Usuário)',
    'img_pass'    => 'ImgPass (Senha)',
    'img_update'  => 'ImgUpdate (Atualização)',
    'img_roteador' => 'ImgRoteador (Roteador)',
    'img_download' => 'ImgDownload (Download)',
    'img_upload'   => 'ImgUpload (Upload)'
];

// ============================================================
// CAMPOS DE COR
// ============================================================
$colorFields = [
    'color_one'     => 'ColorOne',
    'color_two'     => 'ColorTwo',
    'color_starter' => 'ColorStarter',
    'color_dialogs' => 'ColorDialogs',
    'color_buttons' => 'ColorButtons'
];

// ============================================================
// MAPA DE UPLOAD
// ============================================================
$chaveMap = [
    'fundo'  => 'img_fundo',
    'logo'   => 'img_logo',
    'banner' => 'img_banner',
    'menu'   => 'img_menu',
    'logs'   => 'img_logs',
    'check'  => 'img_check',
    'user'   => 'img_user',
    'pass'   => 'img_pass',
    'update' => 'img_update',
    'roteador' => 'img_roteador',
    'download' => 'img_download',
    'upload' => 'img_upload'
];

// ============================================================
// PROCESSAMENTO DOS FORMULÁRIOS
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // ====================================================
        // SALVAR APPNAME
        // ====================================================
        if (isset($_POST['salvar_appname'])) {

            $novoAppName = trim(
                (string)($_POST['AppName'] ?? 'ConnectPlus')
            );

            if ($novoAppName === '') {
                $novoAppName = 'ConnectPlus';
            }

            if (mb_strlen($novoAppName) > 100) {
                $novoAppName = mb_substr($novoAppName, 0, 100);
            }

            $novaVersao = incrementarVersao($versaoAtual);

            salvarTema($pdo, $userId, [
                'app_name' => $novoAppName,
                'version'  => $novaVersao
            ]);

            if (gerarThemeConfig(
                $pdo,
                $userId,
                $novoAppName,
                $novaVersao
            )) {
                logActivity($pdo, 'Atualizou tema', 'Tema', 'Tema do aplicativo atualizado.', currentUserId());
                $appName = $novoAppName;
                $versaoAtual = $novaVersao;

                $mensagem = 'AppName atualizado com sucesso! Versão: ' . $novaVersao;
                $tipo = 'success';
            } else {
                $mensagem = 'AppName salvo, mas não foi possível gerar o JSON.';
                $tipo = 'error';
            }
        }

// ====================================================
// SALVAR CORES
// ====================================================
        elseif (isset($_POST['salvar_cores'])) {

            $cores = [];

            foreach ($colorFields as $key => $label) {

                $valorPicker = $_POST[$key] ?? '';
                $valorTexto  = $_POST[$key . '_text'] ?? '';

                if (trim((string)$valorTexto) !== '') {
                    $valor = $valorTexto;
                } else {
                    $valor = $valorPicker;
                }

                $cores[$key] = normalizarCor(
                    $valor,
                    $key === 'color_dialogs'
                        ? '#ffffff'
                        : (
                            $key === 'color_buttons'
                                ? '#667eea'
                                : '#333333'
                        )
                );
            }

            $novaVersao = incrementarVersao($versaoAtual);

            salvarTema(
                $pdo,
                $userId,
                array_merge(
                    $cores,
                    [
                        'version' => $novaVersao,
                        'app_name' => $appName
                    ]
                )
            );

            if (gerarThemeConfig(
                $pdo,
                $userId,
                $appName,
                $novaVersao
            )) {
                $versaoAtual = $novaVersao;

                $mensagem = 'Cores atualizadas com sucesso! Versão: ' . $novaVersao;
                $tipo = 'success';
            } else {
                $mensagem = 'Cores salvas, mas erro ao gerar JSON.';
                $tipo = 'error';
            }
        }

        // ====================================================
        // SALVAR IMAGENS
        // ====================================================
        elseif (isset($_POST['salvar_imagens'])) {

            $imagens = [];

            foreach ($imageFields as $key => $label) {
                $imagens[$key] = trim(
                    (string)($_POST[$key] ?? '')
                );
            }

            $novaVersao = incrementarVersao($versaoAtual);

            salvarTema(
                $pdo,
                $userId,
                array_merge(
                    $imagens,
                    [
                        'version'  => $novaVersao,
                        'app_name' => $appName
                    ]
                )
            );

            if (gerarThemeConfig(
                $pdo,
                $userId,
                $appName,
                $novaVersao
            )) {
                $versaoAtual = $novaVersao;

                $mensagem = 'URLs das imagens atualizadas! Versão: ' . $novaVersao;
                $tipo = 'success';
            } else {
                $mensagem = 'Imagens salvas, mas erro ao gerar JSON.';
                $tipo = 'error';
            }
        }

        // ====================================================
        // SALVAR TUDO
        // ====================================================
        elseif (isset($_POST['salvar_tudo'])) {

            $novoAppName = trim(
                (string)($_POST['AppName'] ?? 'ConnectPlus')
            );

            if ($novoAppName === '') {
                $novoAppName = 'ConnectPlus';
            }

            $novaVersao = incrementarVersao($versaoAtual);

            $dados = [
                'app_name' => $novoAppName,
                'version'  => $novaVersao
            ];

            // -----------------------------------------------
            // CORES
            // -----------------------------------------------
            foreach ($colorFields as $key => $label) {

                $valorTexto = trim(
                    (string)($_POST[$key . '_text'] ?? '')
                );

                $valorPicker = trim(
                    (string)($_POST[$key] ?? '')
                );

                $valor = $valorTexto !== ''
                    ? $valorTexto
                    : $valorPicker;

                $padrao = '#333333';

                if ($key === 'color_dialogs') {
                    $padrao = '#ffffff';
                }

                if ($key === 'color_buttons') {
                    $padrao = '#667eea';
                }

                $dados[$key] = normalizarCor(
                    $valor,
                    $padrao
                );
            }

            // -----------------------------------------------
            // IMAGENS
            // -----------------------------------------------
            foreach ($imageFields as $key => $label) {

                $dados[$key] = trim(
                    (string)($_POST[$key] ?? '')
                );
            }

            salvarTema(
                $pdo,
                $userId,
                $dados
            );

            if (gerarThemeConfig(
                $pdo,
                $userId,
                $novoAppName,
                $novaVersao
            )) {
                $appName = $novoAppName;
                $versaoAtual = $novaVersao;

                $mensagem = 'Tudo salvo com sucesso! Nova versão: ' . $novaVersao;
                $tipo = 'success';
            } else {
                $mensagem = 'Dados salvos, mas erro ao gerar o JSON.';
                $tipo = 'error';
            }
        }

        // ====================================================
        // UPLOAD DE IMAGEM
        // ====================================================
        elseif (
            isset($_FILES['imagem']) &&
            isset($_FILES['imagem']['name'])
        ) {

            $tipoUpload = (string)(
                $_POST['tipo_upload'] ?? 'logo'
            );

            $chave = $chaveMap[$tipoUpload] ?? 'img_logo';

            [$uploadOk, $extOuErro] = validarImagemUpload(
                $_FILES['imagem']
            );

            if (!$uploadOk) {

                $mensagem = (string)$extOuErro;
                $tipo = 'error';

            } else {

                $ext = $extOuErro;

                $pastaUploads = getThemeDirectory() . '/uploads';

                if (
                    !is_dir($pastaUploads) &&
                    !mkdir($pastaUploads, 0755, true) &&
                    !is_dir($pastaUploads)
                ) {
                    throw new RuntimeException(
                        'Não foi possível criar a pasta de uploads.'
                    );
                }

                // -------------------------------------------
                // Nome seguro e único
                // -------------------------------------------
                $nomeBase = pathinfo(
                    $_FILES['imagem']['name'],
                    PATHINFO_FILENAME
                );

                $nomeBase = preg_replace(
                    '/[^a-zA-Z0-9_-]/',
                    '-',
                    $nomeBase
                );

                $nomeBase = trim(
                    (string)$nomeBase,
                    '-'
                );

                if ($nomeBase === '') {
                    $nomeBase = 'imagem';
                }

                $nomeArquivo =
                    $nomeBase .
                    '_' .
                    date('YmdHis') .
                    '_' .
                    bin2hex(random_bytes(3)) .
                    '.' .
                    $ext;

                $caminho = $pastaUploads . '/' . $nomeArquivo;

                // -------------------------------------------
                // URL pública
                // -------------------------------------------
                $url = getThemePublicUrl() .
                    '/uploads/' .
                    rawurlencode($nomeArquivo);

                if (
                    move_uploaded_file(
                        $_FILES['imagem']['tmp_name'],
                        $caminho
                    )
                ) {

                    $novaVersao = incrementarVersao(
                        $versaoAtual
                    );

                    salvarTema(
                        $pdo,
                        $userId,
                        [
                            $chave    => $url,
                            'version' => $novaVersao,
                            'app_name' => $appName
                        ]
                    );

                    if (gerarThemeConfig(
                        $pdo,
                        $userId,
                        $appName,
                        $novaVersao
                    )) {

                        $versaoAtual = $novaVersao;

                        $mensagem =
                            'Imagem enviada com sucesso! ' .
                            'Versão: ' .
                            $novaVersao;

                        $tipo = 'success';

                    } else {

                        $mensagem =
                            'Imagem enviada, mas houve erro ao gerar o JSON.';

                        $tipo = 'error';
                    }

                } else {

                    $mensagem =
                        'Não foi possível salvar a imagem no servidor.';

                    $tipo = 'error';
                }
            }
        }

    } catch (Throwable $e) {

        $mensagem =
            'Erro: ' . $e->getMessage();

        $tipo = 'error';
    }
}

// ============================================================
// GARANTE QUE O JSON EXISTA
// ============================================================
$themeFile = getThemeDirectory() . '/config.json';

if (
    !file_exists($themeFile) ||
    filesize($themeFile) === 0
) {
    gerarThemeConfig(
        $pdo,
        $userId,
        $appName,
        $versaoAtual
    );
}

// ============================================================
// RECARREGA DADOS
// ============================================================
$tema = carregarTema(
    $pdo,
    $userId
);

$appName = $tema['app_name'] ?? 'ConnectPlus';
$versaoAtual = $tema['version'] ?? '1';

$themeJsonUrl = getUserThemeJsonUrl($userId);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<link rel="stylesheet" href="<?= e(appUrl('/assets/css/style.css')) ?>?v=<?= time() ?>">
<style>
/* ==========================================================
   TEMA.PHP — VISUAL MODERNO / IDENTIDADE VISUAL
   ========================================================== */
:root{
    --tp-bg:#07101f;
    --tp-panel:#0d1a2d;
    --tp-panel-2:#101f35;
    --tp-input:#060e1b;
    --tp-border:#1b2d49;
    --tp-text:#f4f7fb;
    --tp-muted:#8493a8;
    --tp-primary:#6da0ff;
    --tp-primary-2:#4c7fe0;
    --tp-success:#46d39a;
}
body{background:var(--tp-bg);}
.tema-page{max-width:1180px;margin:0 auto;padding:4px 4px 105px;}
.tema-hero{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin:5px 0 28px;padding:8px 2px 22px;border-bottom:1px solid #16263d;}
.tema-kicker{font-size:12px;letter-spacing:2px;text-transform:uppercase;color:var(--tp-primary);font-weight:800;margin-bottom:13px;}
.tema-hero h1{margin:0;color:var(--tp-text);font-size:34px;line-height:1.1;letter-spacing:-.8px;}
.tema-hero p{margin:12px 0 0;color:var(--tp-muted);font-size:15px;max-width:650px;line-height:1.55;}
.tema-version{display:inline-flex;align-items:center;gap:7px;margin-top:14px;color:#b7c5d8;font-size:13px;}
.tema-version strong{color:#fff;background:#162943;border:1px solid #284264;border-radius:999px;padding:5px 10px;}
.tema-json{display:inline-flex;align-items:center;gap:8px;padding:11px 15px;border:1px solid #284264;border-radius:11px;background:#0c192b;color:#c8d8ee;text-decoration:none;font-weight:700;white-space:nowrap;}
.tema-json:hover{background:#13243b;color:#fff;text-decoration:none;}
.tema-card{background:linear-gradient(180deg,#0e1c31,#0b1728);border:1px solid var(--tp-border);border-radius:22px;margin-bottom:20px;overflow:hidden;box-shadow:0 12px 35px rgba(0,0,0,.16);}
.tema-card-head{padding:23px 26px 17px;border-bottom:1px solid #182a43;}
.tema-card-head h2{margin:0;color:#79a7ff;font-size:18px;letter-spacing:.2px;text-transform:uppercase;}
.tema-card-head p{margin:7px 0 0;color:var(--tp-muted);font-size:13px;line-height:1.5;}
.tema-card-body{padding:24px 26px 27px;}
.tema-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px 24px;}
.tema-grid.single{grid-template-columns:1fr;}
.tema-field{min-width:0;}
.tema-field.full{grid-column:1/-1;}
.tema-field label{display:block;margin:0 0 8px;color:#9caabd;font-size:11px;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;}
.tema-field input,.tema-field select,.tema-field textarea{width:100%;height:48px;padding:0 15px;background:var(--tp-input);border:1px solid #1c2d46;border-radius:13px;color:#e7edf5;outline:none;font-size:14px;transition:.2s;}
.tema-field input:focus,.tema-field select:focus,.tema-field textarea:focus{border-color:#507fca;box-shadow:0 0 0 3px rgba(80,127,202,.11);}
.tema-field small{display:block;margin-top:7px;color:#6f8098;font-size:11px;line-height:1.45;}
.tema-color{display:grid;grid-template-columns:52px minmax(0,1fr);gap:9px;align-items:center;}
.tema-color input[type=color]{width:52px;height:48px;padding:4px;cursor:pointer;background:var(--tp-input);border:1px solid #1c2d46;border-radius:13px;}
.tema-image{display:grid;grid-template-columns:minmax(0,1fr) 76px;gap:10px;align-items:center;}
.tema-preview{height:48px;width:76px;display:flex;align-items:center;justify-content:center;background:#07101d;border:1px solid #1b2d46;border-radius:13px;overflow:hidden;}
.tema-preview img{max-width:100%;max-height:100%;object-fit:contain;}
.tema-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:23px;padding-top:19px;border-top:1px solid #172941;}
.tema-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:44px;padding:0 17px;border-radius:12px;border:1px solid #2b4365;background:#14243a;color:#d9e5f5;font-weight:750;cursor:pointer;text-decoration:none;transition:.2s;}
.tema-btn:hover{transform:translateY(-1px);background:#1a2e4b;color:#fff;text-decoration:none;}
.tema-btn.primary{border-color:#5889e5;background:linear-gradient(180deg,#5f91ed,#456fc3);color:#fff;}
.tema-btn.primary:hover{background:linear-gradient(180deg,#6b9cf5,#4b78d0);}
.tema-btn.orange{background:#b95b1a;border-color:#d47a34;color:#fff;}
.tema-alert{padding:14px 17px;border-radius:13px;margin:0 0 20px;border:1px solid #244463;background:#10263b;color:#cfe0f4;font-size:14px;}
.tema-alert.success{border-color:rgba(70,211,154,.28);background:rgba(38,129,96,.13);color:#8be7c1;}
.tema-alert.error{border-color:rgba(255,90,90,.3);background:rgba(180,40,40,.12);color:#ff9d9d;}
.tema-userbar{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:18px;align-items:end;background:#0b182a;border:1px solid #172a44;border-radius:17px;padding:17px 20px;margin-bottom:20px;}
.tema-userbar .tema-field{max-width:620px;}
.tema-user-info{color:#72849c;font-size:12px;line-height:1.5;text-align:right;}
.tema-code{background:#06101d;border:1px solid #182b45;border-radius:14px;padding:15px;color:#aebed2;font-size:12px;line-height:1.55;overflow:auto;max-height:320px;white-space:pre-wrap;word-break:break-word;}
.tema-url{margin-bottom:18px;}
.tema-url input{font-size:12px;}
.tema-sticky{position:fixed;left:250px;right:0;bottom:0;z-index:900;padding:12px 24px;background:rgba(5,12,23,.92);border-top:1px solid #182b45;backdrop-filter:blur(12px);}
.tema-sticky-inner{max-width:1180px;margin:0 auto;display:flex;justify-content:flex-end;align-items:center;gap:12px;}
.tema-saved{font-size:12px;color:var(--tp-success);font-weight:750;}
.tema-sticky button{min-width:170px;}
@media(max-width:992px){.tema-sticky{left:0}.tema-page{padding-left:2px;padding-right:2px}.tema-hero h1{font-size:29px}.tema-grid{gap:17px 16px;}}
@media(max-width:768px){.tema-page{padding:0 0 96px}.tema-hero{display:block;margin-bottom:20px;padding-bottom:18px}.tema-hero h1{font-size:28px}.tema-hero p{font-size:14px}.tema-json{margin-top:15px}.tema-userbar{grid-template-columns:1fr}.tema-user-info{text-align:left}.tema-card{border-radius:18px}.tema-card-head{padding:19px 18px 15px}.tema-card-body{padding:20px 18px 22px}.tema-grid{grid-template-columns:1fr;gap:17px}.tema-field.full{grid-column:auto}.tema-image{grid-template-columns:minmax(0,1fr) 65px}.tema-preview{width:65px}.tema-actions{flex-direction:column}.tema-actions .tema-btn{width:100%}.tema-sticky{padding:10px 14px}.tema-sticky-inner{justify-content:stretch}.tema-saved{display:none}.tema-sticky button{width:100%;}}
</style>
</head>
<body>
<div class="dashboard">
<?php
$menuFile = __DIR__ . '/../includes/menu.php';
if (file_exists($menuFile)) include_once $menuFile;
?>
<div class="main-content">
<?php
$headerFile = __DIR__ . '/../includes/header.php';
if (file_exists($headerFile)) include_once $headerFile;
?>
<div class="tema-page">
    <section class="tema-hero">
        <div>
            <div class="tema-kicker">Identidade visual</div>
            <h1>Layout e Cores</h1>
            <p>Personalize links, imagens e cores do aplicativo. As alterações ficam vinculadas ao usuário selecionado.</p>
            <div class="tema-version">Versão do tema <strong><?= e($versaoAtual) ?></strong></div>
        </div>
        <a class="tema-json" href="<?= e($themeJsonUrl) ?>" target="_blank">📋 Ver JSON</a>
    </section>

    <?php if ($mensagem !== ''): ?>
        <div class="tema-alert <?= $tipo === 'success' ? 'success' : 'error' ?>"><?= e($mensagem) ?></div>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
    <section class="tema-userbar">
        <div class="tema-field">
            <label>Usuário do tema</label>
            <form method="GET" id="tema-user-form">
                <select name="user_id" onchange="this.form.submit()">
                    <?php
                    $usuariosTema = $pdo->query('SELECT id, username, role, status FROM users ORDER BY username ASC')->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($usuariosTema as $usuarioTema):
                    ?>
                    <option value="<?= (int)$usuarioTema['id'] ?>" <?= (int)$usuarioTema['id'] === (int)$userId ? 'selected' : '' ?>><?= e($usuarioTema['username']) ?> — <?= e($usuarioTema['role']) ?> — <?= e($usuarioTema['status']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="tema-user-info">Editando o tema do usuário ID <strong><?= (int)$userId ?></strong>.<br>As alterações ficam isoladas desse usuário.</div>
    </section>
    <?php endif; ?>

    <section class="tema-card">
        <div class="tema-card-head">
            <h2>🎨 Tema do aplicativo <?= e(strtoupper($appName)) ?></h2>
            <p>Estrutura compatível com o tema enviado para o ConnectPlus.</p>
        </div>
        <div class="tema-card-body">
            <form method="POST">
                <div class="tema-grid">
                    <div class="tema-field">
                        <label>Nome do aplicativo</label>
                        <input type="text" name="AppName" value="<?= e($appName) ?>" maxlength="100" placeholder="ConnectPlus" required>
                    </div>
                    <div class="tema-field">
                        <label>Versão do tema</label>
                        <input type="text" value="<?= e($versaoAtual) ?>" readonly>
                        <small>Editável automaticamente. Ao salvar, a versão sobe +1.</small>
                    </div>
                </div>
                <div class="tema-actions">
                    <button type="submit" name="salvar_appname" class="tema-btn primary">💾 Salvar nome</button>
                </div>
            </form>
        </div>
    </section>

    <section class="tema-card">
        <div class="tema-card-head">
            <h2>🖼️ Imagens do aplicativo</h2>
            <p>Informe as URLs das imagens usadas pelo APK ou use o upload abaixo.</p>
        </div>
        <div class="tema-card-body">
            <form method="POST">
                <div class="tema-grid">
                <?php foreach ($imageFields as $key => $label): $imagem = $tema[$key] ?? ''; ?>
                    <div class="tema-field">
                        <label><?= e($label) ?></label>
                        <div class="tema-image">
                            <input type="url" name="<?= e($key) ?>" value="<?= e($imagem) ?>" placeholder="https://...">
                            <div class="tema-preview">
                                <?php if ($imagem !== ''): ?><img src="<?= e($imagem) ?>" alt="<?= e($key) ?>" loading="lazy" onerror="this.style.display='none'"><?php else: ?><span style="color:#51627a;font-size:11px">sem imagem</span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <div class="tema-actions"><button type="submit" name="salvar_imagens" class="tema-btn primary">💾 Salvar imagens</button></div>
            </form>
        </div>
    </section>

    <section class="tema-card">
        <div class="tema-card-head">
            <h2>🎨 Cores do tema</h2>
            <p>Use o seletor de cor ou informe HEX, incluindo transparência quando necessário.</p>
        </div>
        <div class="tema-card-body">
            <form method="POST">
                <div class="tema-grid">
                <?php foreach ($colorFields as $key => $label):
                    $valor = $tema[$key] ?? ($key === 'color_dialogs' ? '#ffffff' : ($key === 'color_buttons' ? '#667eea' : '#333333'));
                    $pickerValor = corParaPicker($valor, $key === 'color_dialogs' ? '#ffffff' : ($key === 'color_buttons' ? '#667eea' : '#333333'));
                ?>
                    <div class="tema-field">
                        <label><?= e($label) ?></label>
                        <div class="tema-color">
                            <input type="color" name="<?= e($key) ?>" value="<?= e($pickerValor) ?>" aria-label="<?= e($label) ?>">
                            <input type="text" name="<?= e($key) ?>_text" value="<?= e($valor) ?>" placeholder="#333333">
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <div class="tema-actions"><button type="submit" name="salvar_cores" class="tema-btn primary">💾 Salvar cores</button></div>
            </form>
        </div>
    </section>

    <section class="tema-card">
        <div class="tema-card-head">
            <h2>📤 Upload de imagem</h2>
            <p>A imagem enviada será salva em <strong>/theme/uploads/</strong> e a URL será registrada automaticamente.</p>
        </div>
        <div class="tema-card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="tema-grid">
                    <div class="tema-field"><label>Imagem</label><input type="file" name="imagem" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml" required></div>
                    <div class="tema-field"><label>Tipo</label><select name="tipo_upload" required>
                        <option value="fundo">ImgFundo (Fundo)</option><option value="logo">ImgLogo (Logo)</option><option value="banner">ImgBanner (Banner)</option><option value="menu">ImgMenu (Menu)</option><option value="logs">ImgLogs (Logs)</option><option value="check">ImgCheck (Check)</option><option value="user">ImgUser (Usuário)</option><option value="pass">ImgPass (Senha)</option><option value="update">ImgUpdate (Atualização)</option>
                    </select></div>
                </div>
                <div class="tema-actions"><button type="submit" class="tema-btn orange">📤 Enviar imagem</button></div>
            </form>
        </div>
    </section>

    <section class="tema-card">
        <div class="tema-card-head"><h2>📋 API e JSON do tema</h2><p>Confira o endereço utilizado pelo aplicativo para buscar as configurações.</p></div>
        <div class="tema-card-body">
            <div class="tema-field tema-url"><label>URL da API do tema</label><input type="text" readonly value="<?= e(getClientApiUrlForDisplay($pdo, $userId, 'api/tema.php')) ?>"><small>O endereço é gerado automaticamente para o usuário selecionado.</small></div>
            <label style="display:block;margin:0 0 8px;color:#9caabd;font-size:11px;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;">theme/config.json</label>
            <div class="tema-code"><?php if (file_exists($themeFile) && filesize($themeFile) > 0) echo e((string)file_get_contents($themeFile)); else echo 'Arquivo ainda não gerado.'; ?></div>
            <div class="tema-actions"><a href="<?= e($themeJsonUrl) ?>" target="_blank" class="tema-btn">🔗 Ver JSON público</a></div>
        </div>
    </section>
</div>
</div>
</div>

<div class="tema-sticky">
    <div class="tema-sticky-inner">
        <span class="tema-saved">● <?= $mensagem !== '' && $tipo === 'success' ? 'Tudo salvo' : 'Pronto para salvar' ?></span>
        <form method="POST">
            <input type="hidden" name="AppName" value="<?= e($appName) ?>">
            <?php foreach ($colorFields as $key => $label): ?>
                <input type="hidden" name="<?= e($key) ?>" value="<?= e($tema[$key] ?? '') ?>">
                <input type="hidden" name="<?= e($key) ?>_text" value="<?= e($tema[$key] ?? '') ?>">
            <?php endforeach; ?>
            <?php foreach ($imageFields as $key => $label): ?><input type="hidden" name="<?= e($key) ?>" value="<?= e($tema[$key] ?? '') ?>"><?php endforeach; ?>
            <button type="submit" name="salvar_tudo" class="tema-btn primary">💾 Salvar configurações</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.tema-color').forEach(function(row){
        const picker=row.querySelector('input[type="color"]');
        const text=row.querySelector('input[type="text"]');
        if(!picker||!text)return;
        picker.addEventListener('input',function(){text.value=this.value.toUpperCase();});
        text.addEventListener('input',function(){
            const v=this.value.trim();
            if(/^#[0-9a-fA-F]{6}$/.test(v)) picker.value=v.toUpperCase();
            else if(/^#[0-9a-fA-F]{8}$/.test(v)) picker.value=('#'+v.substring(1,7)).toUpperCase();
        });
    });
});
</script>
</body>
</html>