<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireLogin();

$activePage = 'servers';
$pageTitle  = 'Editar Servidor';
$userId     = currentUserId();
$id         = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$types = ['SSL'=>'SSL','DIRECT'=>'Direct','PROXY'=>'Proxy','TLSWS'=>'Tlsws','XHTTP'=>'XHTTP'];

$stmt = $pdo->prepare('SELECT * FROM connectplus_servers WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, $userId]);
$server = $stmt->fetch();
if (!$server) redirect('/connectplus/servers.php');

$error   = '';
$payload = (string)($server['payload'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = strtoupper(trim((string)($_POST['type'] ?? $server['type'] ?? 'SSL')));
    if (!isset($types[$type])) $type = 'SSL';

    if (isset($_POST['payload_b64']) && $_POST['payload_b64'] !== '') {
        $decoded = base64_decode((string)$_POST['payload_b64'], true);
        if ($decoded === false) {
            $error = 'Payload inválido.';
        } else {
            $payload = $decoded;
        }
    } elseif (isset($_POST['payload'])) {
        $payload = (string)$_POST['payload'];
    }

    $proxyIp   = trim((string)($_POST['proxy_ip'] ?? ''));
    $proxyPort = trim((string)($_POST['proxy_port'] ?? ''));
    $sni       = trim((string)($_POST['sni'] ?? ''));
    $path      = trim((string)($_POST['path'] ?? ''));

    switch ($type) {
        case 'SSL':    $payload=''; $proxyIp=''; $proxyPort=''; $path=''; break;
        case 'DIRECT': $proxyIp=''; $proxyPort=''; $sni=''; $path=''; break;
        case 'PROXY':  $sni=''; $path=''; break;
        case 'TLSWS':  $path=''; break;
        case 'XHTTP':  $payload=''; $proxyPort=''; break;
    }

    $data = [
        trim((string)($_POST['name'] ?? '')),
        trim((string)($_POST['color_name'] ?? '#ffffff')),
        trim((string)($_POST['description'] ?? '')),
        trim((string)($_POST['color_description'] ?? '#ffffff')),
        $type,
        trim((string)($_POST['flag_url'] ?? '')),
        trim((string)($_POST['server_ip'] ?? '')),
        trim((string)($_POST['check_user'] ?? '')),
        trim((string)($_POST['server_port'] ?? '443')),
        trim((string)($_POST['ssl_port'] ?? '443')),
        trim((string)($_POST['username'] ?? '')),
        (string)($_POST['password'] ?? ''),
        $payload,
        $proxyIp,
        $proxyPort,
        $sni,
        $path,
        trim((string)($_POST['color'] ?? '#0000ff')),
        $types[$type],
    ];

    if ($error !== '') {
        // erro do payload
    } elseif ($data[0] === '') {
        $error = 'Informe o nome do servidor.';
    } elseif ($data[6] === '') {
        $error = 'Informe o IP / Host do servidor.';
    } else {
        try {
            if ($data[11] === '') $data[11] = (string)$server['password'];
            $stmt = $pdo->prepare('UPDATE connectplus_servers SET name=?,color_name=?,description=?,color_description=?,type=?,flag_url=?,server_ip=?,check_user=?,server_port=?,ssl_port=?,username=?,password=?,payload=?,proxy_ip=?,proxy_port=?,sni=?,path=?,color=?,info=? WHERE id=? AND user_id=?');
            $stmt->execute(array_merge($data, [$id, $userId]));
            
            
            if (!generateClientUpdateFiles($pdo, $userId)) {
                throw new RuntimeException('Servidor atualizado, mas não foi possível atualizar o connectplus.json.');
            }

            logActivity($pdo, 'Editou servidor', 'Servidores', 'Servidor ID ' . $id . ' atualizado.', $userId);
            redirect('/connectplus/servers.php');
        } catch (Throwable $e) {
            $error = 'Não foi possível salvar o servidor.';
        }
    }
}

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$v = fn($key, $default='') => h($_POST[$key] ?? $server[$key] ?? $default);
$currentType = strtoupper((string)($_POST['type'] ?? $server['type'] ?? 'SSL'));
if (!isset($types[$currentType])) $currentType = 'SSL';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Editar Servidor - ConnectPlus</title>
<link rel="stylesheet" href="<?= htmlspecialchars(appUrl('/assets/css/style.css'), ENT_QUOTES, 'UTF-8') ?>">

<style>
/* ConnectPlus - formulário de servidor */
.server-modal-backdrop{
    width:100%;
    box-sizing:border-box;
    padding:18px 14px 30px;
}
.server-modal{
    width:100%;
    max-width:980px;
    margin:0 auto;
    overflow:hidden;
    border:1px solid rgba(148,163,184,.16);
    border-radius:18px;
    background:#0b1220;
    box-shadow:0 18px 50px rgba(0,0,0,.35);
}
.server-modal-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:16px 20px;
    color:#fff;
    font-size:21px;
    font-weight:700;
    background:#111a2a;
    border-bottom:1px solid rgba(148,163,184,.14);
}
.server-close{
    width:36px;
    height:36px;
    border:0;
    border-radius:10px;
    background:#263247;
    color:#fff;
    font-size:24px;
    line-height:1;
    cursor:pointer;
}
.server-close:hover{background:#334155}
.server-modal-body{padding:20px}
.mode-box{
    margin-bottom:20px;
    padding:16px;
    border:1px solid rgba(148,163,184,.14);
    border-radius:14px;
    background:#101a2b;
}
.field-label{
    display:block;
    margin:0 0 8px;
    color:#aebbd0;
    font-size:14px;
    font-weight:600;
}
.mode-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}
.mode-tab{
    appearance:none;
    border:1px solid #334155;
    border-radius:9px;
    padding:9px 16px;
    background:#172235;
    color:#dbe5f2;
    font:600 14px/1.2 inherit;
    cursor:pointer;
    transition:.15s ease;
}
.mode-tab:hover{background:#22314a;border-color:#4b6383}
.mode-tab.active{
    background:#2563eb;
    border-color:#3b82f6;
    color:#fff;
    box-shadow:0 4px 12px rgba(37,99,235,.25);
}
.mode-help{
    margin-top:11px;
    color:#9aa9bd;
    font-size:14px;
    line-height:1.45;
}
.server-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:16px;
}
.field{min-width:0}
.field.full{grid-column:1/-1}
.field input,
.field textarea,
.field select{
    width:100%;
    box-sizing:border-box;
    min-height:48px;
    padding:12px 14px;
    border:1px solid #233249;
    border-radius:12px;
    outline:none;
    background:#0e1726;
    color:#f8fafc;
    font:400 15px/1.35 inherit;
    transition:border-color .15s,box-shadow .15s;
}
.field textarea{min-height:120px;resize:vertical}
.field input::placeholder,.field textarea::placeholder{color:#65748a}
.field input:focus,.field textarea:focus,.field select:focus{
    border-color:#3b82f6;
    box-shadow:0 0 0 3px rgba(59,130,246,.14);
}
.color-row{
    display:grid;
    grid-template-columns:52px 1fr;
    gap:8px;
}
.color-picker{
    width:52px!important;
    min-height:48px!important;
    padding:4px!important;
    cursor:pointer;
}
.field-help{
    margin-top:6px;
    color:#7f8da1;
    font-size:12px;
    line-height:1.4;
}
.server-alert{
    margin-bottom:16px;
    padding:12px 14px;
    border-radius:10px;
    background:rgba(239,68,68,.12);
    border:1px solid rgba(239,68,68,.3);
    color:#fecaca;
}
.server-footer{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    padding:16px 20px;
    border-top:1px solid rgba(148,163,184,.14);
    background:#0f1828;
}
.server-btn{
    appearance:none;
    border:1px solid transparent;
    min-height:44px;
    padding:10px 22px;
    border-radius:10px;
    font:700 14px/1 inherit;
    cursor:pointer;
    transition:.15s ease;
}
.server-btn.cancel{
    background:#263247;
    border-color:#334155;
    color:#e2e8f0;
}
.server-btn.cancel:hover{background:#334155}
.server-btn.save{
    background:#2563eb;
    border-color:#3b82f6;
    color:#fff;
    box-shadow:0 5px 14px rgba(37,99,235,.2);
}
.server-btn.save:hover{background:#1d4ed8}
@media(max-width:720px){
    .server-modal-backdrop{padding:10px 8px 24px}
    .server-modal-head{padding:14px 16px;font-size:18px}
    .server-modal-body{padding:14px}
    .server-grid{grid-template-columns:1fr;gap:14px}
    .field.full{grid-column:auto}
    .server-footer{padding:14px;position:sticky;bottom:0}
    .server-btn{flex:1}
    .mode-tab{flex:1 1 auto;text-align:center;padding:9px 12px}
}
</style>

</head>
<body>
<div class="dashboard">
<?php include_once __DIR__.'/../includes/menu.php'; ?>
<div class="main-content">
<?php include_once __DIR__.'/../includes/header.php'; ?>
<div class="server-modal-backdrop">
<div class="server-modal">
<div class="server-modal-head">
    <span>Configuração de Servidor</span>
    <button class="server-close" type="button" onclick="location.href='<?=h(appUrl('/connectplus/servers.php'))?>'">×</button>
</div>
<form method="POST" id="serverForm">
<input type="hidden" name="id" value="<?=h($id)?>">
<div class="server-modal-body">
<?php if($error):?><div class="server-alert"><?=h($error)?></div><?php endif;?>
<div class="mode-box">
<label class="field-label">Modo de conexão</label>
<div class="mode-tabs"><?php foreach($types as $value=>$label):?><button type="button" class="mode-tab<?= $currentType===$value?' active':''?>" data-type="<?=h($value)?>"><?=h($label)?></button><?php endforeach;?></div>
<input type="hidden" name="type" id="connectionType" value="<?=h($currentType)?>">
<div class="mode-help" id="modeHelp"></div>
</div>
<div class="server-grid">
<div class="field"><label class="field-label">Nome</label><input name="name" value="<?=$v('name')?>" required></div>
<div class="field"><label class="field-label">Descrição</label><input name="description" value="<?=$v('description')?>"></div>
<div class="field"><label class="field-label">IP / Host do servidor</label><input name="server_ip" value="<?=$v('server_ip')?>" required></div>
<div class="field"><label class="field-label">Porta do servidor</label><input name="server_port" value="<?=$v('server_port','443')?>"></div>
<div class="field"><label class="field-label">Flag (URL)</label><input name="flag_url" value="<?=$v('flag_url')?>" placeholder="https://.../bandeira.png"></div>
<div class="field"><label class="field-label">URL Check User</label><input name="check_user" value="<?=$v('check_user')?>" placeholder="http://.../check?user={user}"></div>
<div class="field"><label class="field-label">Usuário</label><input name="username" value="<?=$v('username')?>"></div>
<div class="field"><label class="field-label">Senha</label><input type="password" name="password" value="" placeholder="Deixe em branco para manter a atual"></div>
<div class="field full" data-field="payload"><label class="field-label">Payload</label><input type="hidden" name="payload_b64" id="payloadB64" value=""><textarea name="payload"><?=h($payload)?></textarea><div class="field-help">Use [lf] para quebra de linha. Para múltiplos payloads, separe com # exatamente como o APK espera.</div></div>
<div class="field" data-field="proxy_ip"><label class="field-label">ProxyIP / Host</label><input name="proxy_ip" value="<?=$v('proxy_ip')?>" placeholder="IP ou host; use # para múltiplos"><div class="field-help">Multi-proxy: separe os IPs/hosts com #.</div></div>
<div class="field" data-field="proxy_port"><label class="field-label">ProxyPort (opcional)</label><input name="proxy_port" value="<?=$v('proxy_port','443')?>"></div>
<div class="field full" data-field="sni"><label class="field-label">SNI</label><input name="sni" value="<?=$v('sni')?>"><div class="field-help">Usado pelos modos que trabalham com SNI.</div></div>
<div class="field full" data-field="path"><label class="field-label">Path</label><input name="path" value="<?=$v('path')?>" placeholder="/ssh"><div class="field-help">No XHTTP, informe o caminho configurado pelo servidor.</div></div>
<div class="field"><label class="field-label">Cor do nome</label><div class="color-row"><input type="color" name="color_name" class="color-picker" value="<?=$v('color_name','#ffffff')?>"><input name="color_name_text" value="<?=$v('color_name','#ffffff')?>" data-color-text="color_name"></div></div>
<div class="field"><label class="field-label">Cor da descrição</label><div class="color-row"><input type="color" name="color_description" class="color-picker" value="<?=$v('color_description','#ffffff')?>"><input name="color_description_text" value="<?=$v('color_description','#ffffff')?>" data-color-text="color_description"></div></div>
<div class="field"><label class="field-label">Cor</label><div class="color-row"><input type="color" name="color" class="color-picker" value="<?=$v('color','#0000ff')?>"><input name="color_text" value="<?=$v('color','#0000ff')?>" data-color-text="color"></div></div>
<div class="field"><label class="field-label">Versão TLS / Info</label><select id="tlsDisplay"><option>TLSv1.2</option><option>TLSv1.3</option></select><input type="hidden" name="info" id="info" value="<?=h($types[$currentType])?>"></div>
</div>
</div>
<div class="server-footer">
<button type="button" class="server-btn cancel" onclick="location.href='<?=h(appUrl('/connectplus/servers.php'))?>'">Cancelar</button>
<button class="server-btn save" type="submit">Concluir</button>
</div>
</form>
</div></div></div></div>
<script>
const modeHelp={SSL:'SSL direto: o app usa ServerIP, ServerPort e SNI, sem proxy personalizada.',DIRECT:'Direct: conexão direta; o app usa ServerIP, ServerPort e Payload.',PROXY:'Proxy: o app usa ServerIP, ServerPort, ProxyIP, ProxyPort e Payload.',TLSWS:'Tlsws: o app usa servidor, porta, proxy, payload e SNI.',XHTTP:'XHTTP: o app usa servidor, porta, proxy, SNI e Path.'};
const fields={payload:document.querySelector('[data-field="payload"]'),proxy_ip:document.querySelector('[data-field="proxy_ip"]'),proxy_port:document.querySelector('[data-field="proxy_port"]'),sni:document.querySelector('[data-field="sni"]'),path:document.querySelector('[data-field="path"]')};
function setType(type){document.getElementById('connectionType').value=type;document.getElementById('info').value={SSL:'SSL',DIRECT:'Direct',PROXY:'Proxy',TLSWS:'Tlsws',XHTTP:'XHTTP'}[type]||type;document.getElementById('modeHelp').textContent=modeHelp[type]||'';document.querySelectorAll('.mode-tab').forEach(b=>b.classList.toggle('active',b.dataset.type===type));Object.values(fields).forEach(x=>x.style.display='none');if(type==='DIRECT'||type==='PROXY'||type==='TLSWS')fields.payload.style.display='block';if(type==='PROXY'||type==='TLSWS'||type==='XHTTP')fields.proxy_ip.style.display='block';
if(type==='PROXY'||type==='TLSWS')fields.proxy_port.style.display='block';if(type==='SSL'||type==='TLSWS'||type==='XHTTP')fields.sni.style.display='block';if(type==='XHTTP')fields.path.style.display='block';}
document.querySelectorAll('.mode-tab').forEach(b=>b.addEventListener('click',()=>setType(b.dataset.type)));document.querySelectorAll('[data-color-text]').forEach(t=>{const n=t.dataset.colorText,c=document.querySelector('input[name="'+n+'"]');t.addEventListener('input',()=>{if(/^#[0-9a-fA-F]{6}$/.test(t.value))c.value=t.value;});c.addEventListener('input',()=>t.value=c.value);});
document.getElementById('serverForm').addEventListener('submit',function(){const p=this.querySelector('textarea[name="payload"]');const h=document.getElementById('payloadB64');if(p&&h){const bytes=new TextEncoder().encode(p.value);let bin='';const chunk=0x8000;for(let i=0;i<bytes.length;i+=chunk){bin+=String.fromCharCode(...bytes.subarray(i,i+chunk));}h.value=btoa(bin);p.disabled=true;}});setType('<?=h($currentType)?>');
</script>
</body>
</html>