<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/atx.php';

requireLogin();


global $pdo;

$pageTitle = 'Credencial ATX';
$activePage = 'atx';

function atxCredentialEscape(mixed $value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Impede que páginas contendo informações de credenciais sejam armazenadas
// no cache compartilhado ou no cache normal do navegador.
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: no-referrer');
}

$sessionUserId = (int) currentUserId();
$admin = isAdmin();

if ($sessionUserId <= 0) {
    http_response_code(403);
    exit('Acesso negado.');
}

// Somente GET e POST são aceitos nesta página.
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    http_response_code(405);
    exit('Método não permitido.');
}

// Validação estrita: evita conversões permissivas como "12abc" para 12.
$rawSelectedId = $admin
    ? ($_POST['user_id'] ?? $_GET['user'] ?? $sessionUserId)
    : $sessionUserId;

$selectedId = filter_var(
    $rawSelectedId,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($selectedId === false) {
    http_response_code(400);
    exit('ID de usuário inválido.');
}

$selectedId = (int) $selectedId;

// O usuário comum nunca pode escolher outro ID.
if (!$admin && $selectedId !== $sessionUserId) {
    http_response_code(403);
    exit('Acesso negado.');
}

try {
    ensureAtxCredentialTable($pdo);

    $selectedUser = getUser($pdo, $selectedId);

    if (
        !$selectedUser
        || ($selectedUser['status'] ?? '') !== 'active'
    ) {
        http_response_code(403);
        exit('Usuário inválido ou inativo.');
    }

    // A lista de clientes é exibida somente para administradores.
    $users = $admin
        ? getUsers($pdo)
        : [$selectedUser];

} catch (Throwable $e) {
    error_log('Credencial ATX: falha ao carregar usuário ou tabela.');
    http_response_code(500);
    exit('Não foi possível carregar os dados da credencial.');
}

$credencial = '';
$erro = '';
$sucesso = false;

if ($method === 'POST') {
    try {
        // A validação CSRF deve ocorrer antes de qualquer alteração.
        requireValidCsrfToken();

        // Reconfirma o usuário imediatamente antes da operação.
        $freshUser = getUser($pdo, $selectedId);

        if (
            !$freshUser
            || ($freshUser['status'] ?? '') !== 'active'
            || (!$admin && $selectedId !== $sessionUserId)
        ) {
            http_response_code(403);
            exit('Usuário inválido ou acesso negado.');
        }

        $credencial = rotateAtxCredential($pdo, $selectedId);

        if (!is_string($credencial) || trim($credencial) === '') {
            throw new RuntimeException('Credencial vazia.');
        }

        // Não registre o token nem dados secretos nos logs.
        logActivity(
            $pdo,
            'Gerou credencial ATX',
            'ATX',
            'Credencial ATX renovada para usuário ID ' . $selectedId . '.',
            $sessionUserId
        );

        $sucesso = true;

    } catch (Throwable $e) {
        // Não exponha mensagens internas, SQL ou dados sensíveis.
        error_log(
            'Credencial ATX: falha ao gerar ou registrar a operação.'
        );

        // Não mantenha o token em memória para exibição após falha.
        $credencial = '';
        $sucesso = false;
        $erro = 'Não foi possível gerar a credencial ATX.';
    }
}

try {
    $stmt = $pdo->prepare(
        'SELECT credential_preview, updated_at
         FROM atx_credentials
         WHERE user_id = ?
         LIMIT 1'
    );

    $stmt->execute([$selectedId]);
    $credentialInfo = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

} catch (Throwable $e) {
    error_log('Credencial ATX: falha ao consultar os metadados.');
    http_response_code(500);
    exit('Não foi possível consultar os dados da credencial.');
}

$apiUrl = rtrim(SITE_URL, '/') . '/api/atx.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= atxCredentialEscape($pageTitle) ?></title>
    <link rel="stylesheet"
          href="<?= atxCredentialEscape(appUrl('/assets/css/style.css?v=1')) ?>">
</head>
<body>
<div class="dashboard">
    <?php require __DIR__ . '/../includes/menu.php'; ?>

    <main class="main-content">
        <?php require __DIR__ . '/../includes/header.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2><?= atxCredentialEscape($pageTitle) ?></h2>
                </div>

                <div class="card-body">
                    <p>
                        Gere uma credencial exclusiva para o usuário selecionado.
                        O token deve ser colocado em
                        <code>assets/config/credential.txt</code>
                        do APK ATX. A renovação pode invalidar a credencial anterior.
                    </p>

                    <?php if ($erro !== ''): ?>
                        <div class="error" role="alert">
                            <?= atxCredentialEscape($erro) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($sucesso): ?>
                        <p class="text-muted" role="status">
                            Credencial gerada com sucesso. Copie ou baixe o token
                            antes de sair desta página.
                        </p>
                    <?php endif; ?>

                    <form method="post">
                        <?= csrfField() ?>

                        <?php if ($admin): ?>
                            <div class="form-group">
                                <label for="user_id">Cliente</label>

                                <select id="user_id"
                                        name="user_id"
                                        class="form-control"
                                        onchange="location.href='?user='+encodeURIComponent(this.value)"
                                        required>
                                    <?php foreach ($users as $u): ?>
                                        <?php if (!$u) continue; ?>

                                        <option
                                            value="<?= (int) $u['id'] ?>"
                                            <?= $selectedId === (int) $u['id']
                                                ? 'selected'
                                                : '' ?>>
                                            <?= atxCredentialEscape(
                                                $u['username'] ?? ('ID ' . $u['id'])
                                            ) ?>
                                            (ID <?= (int) $u['id'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <p>
                                <strong>Cliente:</strong>
                                <?= atxCredentialEscape(
                                    $selectedUser['username'] ?? ''
                                ) ?>
                            </p>

                            <input type="hidden"
                                   name="user_id"
                                   value="<?= $selectedId ?>">
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary">
                            Gerar / renovar credencial ATX
                        </button>
                    </form>

                    <?php if ($credentialInfo): ?>
                        <hr>

                        <p>
                            <strong>Credencial cadastrada:</strong>
                            <?= atxCredentialEscape(
                                $credentialInfo['credential_preview'] ?? ''
                            ) ?>
                        </p>

                        <p>
                            <strong>Última alteração:</strong>
                            <?= atxCredentialEscape(
                                $credentialInfo['updated_at'] ?? ''
                            ) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-muted">
                            Este usuário ainda não possui uma credencial ATX.
                        </p>
                    <?php endif; ?>

                    <?php if ($sucesso && $credencial !== ''): ?>
                        <hr>

                        <div class="form-group">
                            <label for="resultado">
                                Conteúdo de credential.txt — copie ou baixe agora.
                                O token puro não deve ser armazenado em texto
                                simples no banco.
                            </label>

                            <textarea id="resultado"
                                      class="form-control"
                                      rows="3"
                                      readonly
                                      autocomplete="off"
                                      spellcheck="false"><?= atxCredentialEscape(
                                          $credencial . PHP_EOL
                                      ) ?></textarea>
                        </div>

                        <button type="button"
                                class="btn btn-secondary"
                                id="baixar-credential">
                            Baixar credential.txt
                        </button>

                        <button type="button"
                                class="btn btn-info"
                                id="copiar-credential">
                            Copiar credencial
                        </button>

                        <p id="mensagem-copia"
                           class="text-muted"
                           role="status"
                           aria-live="polite"></p>

                        <script>
                        (() => {
                            'use strict';

                            const campo = document.getElementById('resultado');
                            const mensagem = document.getElementById('mensagem-copia');
                            const baixar = document.getElementById('baixar-credential');
                            const copiar = document.getElementById('copiar-credential');

                            baixar.addEventListener('click', () => {
                                const blob = new Blob(
                                    [campo.value],
                                    { type: 'text/plain;charset=utf-8' }
                                );

                                const url = URL.createObjectURL(blob);
                                const link = document.createElement('a');

                                link.href = url;
                                link.download = 'credential.txt';
                                link.style.display = 'none';

                                document.body.appendChild(link);
                                link.click();
                                link.remove();

                                // Aguarda o navegador iniciar o download.
                                setTimeout(() => URL.revokeObjectURL(url), 1000);
                            });

                            copiar.addEventListener('click', async () => {
                                const valor = campo.value.trim();

                                try {
                                    if (
                                        navigator.clipboard
                                        && window.isSecureContext
                                    ) {
                                        await navigator.clipboard.writeText(valor);
                                    } else {
                                        campo.focus();
                                        campo.select();

                                        const copiado = document.execCommand('copy');

                                        if (!copiado) {
                                            throw new Error('Cópia não disponível');
                                        }
                                    }

                                    mensagem.textContent = 'Credencial copiada.';
                                } catch (erro) {
                                    mensagem.textContent =
                                        'Não foi possível copiar automaticamente. ' +
                                        'Selecione o texto e copie manualmente.';
                                }
                            });
                        })();
                        </script>
                    <?php endif; ?>

                    <hr>

                    <p>
                        <strong>Endpoint ATX:</strong>
                        <code><?= atxCredentialEscape($apiUrl) ?></code>
                    </p>

                    <p class="text-muted">
                        O APK envia a credencial pelo parâmetro legado
                        <code>?URL=</code>. A API deve validar o token e rejeitar
                        credenciais inválidas, sem utilizar usuário padrão como
                        alternativa.
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="<?= atxCredentialEscape(appUrl('/assets/js/script.js')) ?>"></script>
</body>
</html>
