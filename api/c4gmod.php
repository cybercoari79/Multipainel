<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Access-Control-Allow-Origin: *');

try {
    // O endpoint de configuração é público, pois o APK consulta a URL sem parâmetros.
    // Se um token for enviado, ele precisa ser válido; isso mantém compatibilidade
    // com instalações que ainda utilizem a URL com ?token=...
    $token = trim((string)($_GET['token'] ?? ''));
    if ($token !== '') {
        $st = $pdo->prepare('SELECT id FROM c4gmod_api_tokens WHERE token_hash = ? AND active = 1 LIMIT 1');
        $st->execute([hash('sha256', $token)]);
        $tokenRow = $st->fetch();
        if (!$tokenRow) {
            http_response_code(401);
            echo json_encode(['error' => 'unauthorized'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
        $pdo->prepare('UPDATE c4gmod_api_tokens SET last_used_at = NOW() WHERE id = ?')->execute([(int)$tokenRow['id']]);
    }

    $app = $pdo->query('SELECT * FROM c4gmod_app_config WHERE id = 1 LIMIT 1')->fetch();
    if (!$app) {
        throw new RuntimeException('C4GMOD não configurado. Importe c4gmod/install.sql.');
    }

    $servers = [];
    $q = $pdo->query('SELECT * FROM c4gmod_servers WHERE active = 1 ORDER BY sort_order, id');
    foreach ($q as $r) {
        $servers[] = [
            'Name' => (string)$r['name'],
            'TYPE' => (string)$r['type'],
            'FLAG' => (string)$r['flag'],
            'ServerIP' => (string)$r['server_ip'],
            'CheckUser' => (string)$r['check_user'],
            'ServerPort' => (string)$r['server_port'],
            'SSLPort' => (string)$r['ssl_port'],
            'USER' => (string)$r['username'],
            'PASS' => (string)$r['password'],
        ];
    }

    $networks = [];
    $q = $pdo->query('SELECT * FROM c4gmod_networks WHERE active = 1 ORDER BY sort_order, id');
    foreach ($q as $r) {
        $networks[] = [
            'Name' => (string)$r['name'],
            'FLAG' => (string)$r['flag'],
            'Payload' => (string)$r['payload'],
            'SNI' => (string)$r['sni'],
            'TlsIP' => (string)$r['tls_ip'],
            'ProxyIP' => (string)$r['proxy_ip'],
            'ProxyPort' => (string)$r['proxy_port'],
            'Info' => (string)$r['info'],
        ];
    }

    $udp = [];
    $q = $pdo->query('SELECT porta FROM c4gmod_udp WHERE active = 1 ORDER BY sort_order, id');
    foreach ($q as $r) {
        $udp[] = ['Porta' => (string)$r['porta']];
    }

    // Manter exatamente os nomes e a estrutura usados pelo JSON oficial do APK.
    $out = [
        'Version' => (string)$app['version'],
        'AppName' => (string)$app['app_name'],
        'ReleaseNotes' => (string)$app['release_notes'],
        'UrlUpdate' => (string)$app['url_update'],
        'Sms' => (string)$app['sms'],
        'EmailFeedback' => (string)$app['email_feedback'],
        'UrlContato' => (string)$app['url_contato'],
        'UrlTermos' => (string)$app['url_termos'],
        'logoonline' => (string)$app['logoonline'],
        'fundoonline' => (string)$app['fundoonline'],
        'fundoDoLogOnline' => (string)$app['fundo_do_log_online'],
        'CheckUser' => (string)$app['check_user'],
        'CorQuadro' => (string)$app['cor_quadro'],
        'CorBordaQuadro' => (string)$app['cor_borda_quadro'],
        'EspessuraBordaQuadro' => (string)$app['espessura_borda_quadro'],
        'RaioQuadro' => (string)$app['raio_quadro'],
        'CorSpinner' => (string)$app['cor_spinner'],
        'CorBordaSpinner' => (string)$app['cor_borda_spinner'],
        'EspessuraBordaSpinner' => (string)$app['espessura_borda_spinner'],
        'RaioSpinner' => (string)$app['raio_spinner'],
        'CorTextoSpinner' => (string)$app['cor_texto_spinner'],
        'CorCampo' => (string)$app['cor_campo'],
        'CorBordaCampo' => (string)$app['cor_borda_campo'],
        'EspessuraBordaCampo' => (string)$app['espessura_borda_campo'],
        'RaioCampo' => (string)$app['raio_campo'],
        'CorTextoCampo' => (string)$app['cor_texto_campo'],
        'CorBotaoConectar' => (string)$app['cor_botao_conectar'],
        'CorBordaBotaoConectar' => (string)$app['cor_borda_botao_conectar'],
        'EspessuraBordaBotaoConectar' => (string)$app['espessura_borda_botao_conectar'],
        'RaioBotaoConectar' => (string)$app['raio_botao_conectar'],
        'CorTextoBotaoConectar' => (string)$app['cor_texto_botao_conectar'],
        'CorStatusConectado' => (string)$app['cor_status_conectado'],
        'CorStatusDesconectado' => (string)$app['cor_status_desconectado'],
        'Udp' => $udp,
        'Servers' => $servers,
        'Networks' => $networks,
    ];

    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    http_response_code(500);
    // Evitar enviar detalhes internos da base de dados para clientes externos.
    echo json_encode(['error' => 'server_error'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
