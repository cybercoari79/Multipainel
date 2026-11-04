<?php
declare(strict_types=1);

function ensure4gTables(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS 4g_config (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT UNSIGNED NOT NULL,
        version VARCHAR(50) NOT NULL DEFAULT '0',
        app_name VARCHAR(100) NOT NULL DEFAULT 'CYBERCOARI',
        release_notes TEXT NULL,
        update_url TEXT NULL,
        sms_url TEXT NULL,
        logoonline TEXT NULL,
        fundoonline TEXT NULL,
        logonline TEXT NULL,
        rodapeonline TEXT NULL,
        email_feedback VARCHAR(255) DEFAULT '',
        contato TEXT NULL,
        termos TEXT NULL,
        check_user VARCHAR(10) DEFAULT 'true',
        cor_borda_campo VARCHAR(20) DEFAULT '#FF0000',
        cor_icone_usuario VARCHAR(20) DEFAULT '#FF0000',
        cor_icone_senha VARCHAR(20) DEFAULT '#0000FF',
        cor_botao_start VARCHAR(20) DEFAULT '#0000FF',
        cor_borda_botao_start VARCHAR(20) DEFAULT '#FF0000',
        cor_fonte_botao_start VARCHAR(20) DEFAULT '#00FFFF',
        cor_titulo_app VARCHAR(20) DEFAULT '#FF00BAFF',
        cor_status VARCHAR(20) DEFAULT '#FFFFFFFF',
        cor_texto_versao VARCHAR(20) DEFAULT '#FFFFFFFF',
        cor_numero_versao VARCHAR(20) DEFAULT '#FF00FF00',
        cor_ip VARCHAR(20) DEFAULT '#FF00FF00',
        cor_operadora VARCHAR(20) DEFAULT '#FF00FF00',
        cor_icone_atualizar VARCHAR(20) DEFAULT '#FF00DE0C',
        espessura_borda_campo INT DEFAULT 0,
        udp_ports VARCHAR(255) DEFAULT '7300;7400;7500',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id), UNIQUE KEY uq_4g_user(user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS 4g_servers LIKE servers");
    $pdo->exec("CREATE TABLE IF NOT EXISTS 4g_networks LIKE networks");
    $pdo->exec("CREATE TABLE IF NOT EXISTS 4g_payloads LIKE payloads");
}

function get4gConfig(PDO $pdo, int $userId): array {
    ensure4gTables($pdo);
    $s=$pdo->prepare('SELECT * FROM 4g_config WHERE user_id=? LIMIT 1'); $s->execute([$userId]);
    $r=$s->fetch();
    if($r) return $r;
    return [
        'version'=>'12','app_name'=>'CYBERCOARI','release_notes'=>'Ola novas credenciais do APK...',
        'update_url'=>'https://painel.cybercoari.com.br/api/config.php','sms_url'=>'https://painel.cybercoari.com.br/api/sms.php',
        'logoonline'=>'https://cybercoari.com.br/cyber/imagens/logoonline.png','fundoonline'=>'https://cybercoari.com.br/cyber/imagens/fundoonline.png',
        'logonline'=>'https://cybercoari.com.br/cyber/imagens/logonline.png','rodapeonline'=>'https://cybercoari.com.br/cyber/imagens/rodapeonline.png',
        'email_feedback'=>'admin@cybercoari.com.br','contato'=>'','termos'=>'','check_user'=>'true',
        'cor_borda_campo'=>'#FF0000','cor_icone_usuario'=>'#FF0000','cor_icone_senha'=>'#0000FF','cor_botao_start'=>'#0000FF',
        'cor_borda_botao_start'=>'#FF0000','cor_fonte_botao_start'=>'#00FFFF','cor_titulo_app'=>'#FF00BAFF','cor_status'=>'#FFFFFFFF',
        'cor_texto_versao'=>'#FFFFFFFF','cor_numero_versao'=>'#FF00FF00','cor_ip'=>'#FF00FF00','cor_operadora'=>'#FF00FF00',
        'cor_icone_atualizar'=>'#FF00DE0C','espessura_borda_campo'=>0,'udp_ports'=>'7300;7400;7500'
    ];
}
function save4gConfig(PDO $pdo,int $uid,array $d):bool {
    ensure4gTables($pdo);
    $cols=['version','app_name','release_notes','update_url','sms_url','logoonline','fundoonline','logonline','rodapeonline','email_feedback','contato','termos','check_user','cor_borda_campo','cor_icone_usuario','cor_icone_senha','cor_botao_start','cor_borda_botao_start','cor_fonte_botao_start','cor_titulo_app','cor_status','cor_texto_versao','cor_numero_versao','cor_ip','cor_operadora','cor_icone_atualizar','espessura_borda_campo','udp_ports'];
    $vals=[]; foreach($cols as $c)$vals[]=$d[$c]??'';
    $vals[]=$uid;
    $sql='INSERT INTO 4g_config ('.implode(',',$cols).',user_id) VALUES ('.rtrim(str_repeat('?,',count($cols)),',').',?) ON DUPLICATE KEY UPDATE '.implode(',',array_map(fn($c)=>"$c=VALUES($c)",$cols));
    return $pdo->prepare($sql)->execute($vals);
}
function get4gServers(PDO $pdo,int $uid):array { ensure4gTables($pdo); $s=$pdo->prepare('SELECT * FROM 4g_servers WHERE user_id=? ORDER BY id ASC');$s->execute([$uid]);return $s->fetchAll(); }
function get4gNetworks(PDO $pdo,int $uid):array { ensure4gTables($pdo); $s=$pdo->prepare('SELECT * FROM 4g_networks WHERE user_id=? ORDER BY id ASC');$s->execute([$uid]);return $s->fetchAll(); }
function build4gConfig(PDO $pdo,int $uid):array {
    $c=get4gConfig($pdo,$uid); $servers=get4gServers($pdo,$uid); $networks=get4gNetworks($pdo,$uid);
    $udp=[]; foreach(preg_split('/[;,#,\s]+/',trim((string)$c['udp_ports']),-1,PREG_SPLIT_NO_EMPTY)?:[] as $p){if(ctype_digit($p)&&$p>=1&&$p<=65535)$udp[]=['Porta'=>(string)(int)$p];}
    $sl=[]; foreach($servers as $s)$sl[]=['Name'=>(string)$s['name'],'TYPE'=>(string)$s['type'],'FLAG'=>absoluteUrl((string)$s['flag_url']),'ServerIP'=>(string)$s['server_ip'],'CheckUser'=>(string)$s['check_user'],'ServerPort'=>(string)$s['server_port'],'SSLPort'=>(string)$s['ssl_port'],'USER'=>(string)$s['username'],'PASS'=>(string)$s['password']];
    $nl=[]; foreach($networks as $n){$payload=''; if(!empty($n['payload_file'])){$q=$pdo->prepare('SELECT content FROM 4g_payloads WHERE network_id=? AND user_id=? ORDER BY id DESC LIMIT 1');$q->execute([(int)$n['id'],$uid]);$payload=(string)($q->fetchColumn()?:'');} $nl[]=['Name'=>(string)$n['name'],'FLAG'=>absoluteUrl((string)$n['flag_url']),'Payload'=>$payload,'SNI'=>(string)$n['sni'],'TlsIP'=>(string)$n['tls_ip'],'ProxyIP'=>(string)$n['proxy_ip'],'ProxyPort'=>(string)$n['proxy_port'],'Info'=>(string)$n['info']];}
    return ['Version'=>(string)$c['version'],'AppName'=>(string)$c['app_name'],'ReleaseNotes'=>$c['release_notes']===null?null:(string)$c['release_notes'],'UrlUpdate'=>(string)$c['update_url'],'Sms'=>(string)$c['sms_url'],'logoonline'=>absoluteUrl((string)$c['logoonline']),'fundoonline'=>absoluteUrl((string)$c['fundoonline']),'logonline'=>absoluteUrl((string)$c['logonline']),'rodapeonline'=>absoluteUrl((string)$c['rodapeonline']),'EmailFeedback'=>(string)$c['email_feedback'],'UrlContato'=>(string)$c['contato'],'UrlTermos'=>(string)$c['termos'],'CheckUser'=>(string)$c['check_user'],'CorBordaCampo'=>(string)$c['cor_borda_campo'],'CorIconeUsuario'=>(string)$c['cor_icone_usuario'],'CorIconeSenha'=>(string)$c['cor_icone_senha'],'CorBotaoStart'=>(string)$c['cor_botao_start'],'CorBordaBotaoStart'=>(string)$c['cor_borda_botao_start'],'CorFonteBotaoStart'=>(string)$c['cor_fonte_botao_start'],'CorTituloApp'=>(string)$c['cor_titulo_app'],'CorStatus'=>(string)$c['cor_status'],'CorTextoVersao'=>(string)$c['cor_texto_versao'],'CorNumeroVersao'=>(string)$c['cor_numero_versao'],'CorIP'=>(string)$c['cor_ip'],'CorOperadora'=>(string)$c['cor_operadora'],'CorIconeAtualizar'=>(string)$c['cor_icone_atualizar'],'EspessuraBordaCampo'=>(int)$c['espessura_borda_campo'],'Udp'=>$udp,'UdpPort'=>(string)$c['udp_ports'],'Servers'=>$sl,'Networks'=>$nl];
}
