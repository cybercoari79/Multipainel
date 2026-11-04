<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = 'INSERT INTO servers (client_id,name,flag,server_id,info,user_name,password,id_ordem,corct,server_ip,server_port,payload,proxy_ip,proxy_port,ssl_port,sni,check_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $pdo->prepare($sql)->execute([
        (int)$_POST['client_id'], trim($_POST['name']), trim($_POST['flag']), trim($_POST['server_id']),
        trim($_POST['info']), trim($_POST['user_name']), trim($_POST['password']), (int)$_POST['id_ordem'],
        trim($_POST['corct']), trim($_POST['server_ip']), trim($_POST['server_port']), trim($_POST['payload']),
        trim($_POST['proxy_ip']), trim($_POST['proxy_port']), trim($_POST['ssl_port']), trim($_POST['sni']), trim($_POST['check_user'])
    ]);
}
$clients = $pdo->query('SELECT id, username FROM clients WHERE status="active" ORDER BY username')->fetchAll();
$rows = $pdo->query('SELECT s.*, c.username client_name FROM servers s JOIN clients c ON c.id=s.client_id ORDER BY s.id DESC')->fetchAll();
?>
<?php $pageTitle='Servidores — ATX Panel'; require __DIR__.'/includes/header.php'; ?>
<div class="wrap"><a href="index.php">← Dashboard</a><h1>Servidores</h1>
<form method="post"><?php foreach([['client_id','Cliente'],['name','Name'],['flag','FLAG'],['server_id','id'],['info','Info'],['user_name','USER'],['password','PASS'],['id_ordem','id_ordem'],['corct','corct'],['server_ip','ServerIP'],['server_port','ServerPort'],['payload','Payload'],['proxy_ip','ProxyIP'],['proxy_port','ProxyPort'],['ssl_port','SSLPort'],['sni','SNI'],['check_user','CheckUser']] as $f): if($f[0]==='client_id'): ?><select name="client_id" required><option value="">Cliente</option><?php foreach($clients as $c): ?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['username'])?></option><?php endforeach; ?></select><?php else: ?><input name="<?=$f[0]?>" placeholder="<?=$f[1]?>"><?php endif; endforeach; ?><button>Adicionar servidor</button></form>
<table><tr><th>Cliente</th><th>Name</th><th>ServerIP</th><th>ServerPort</th><th>SSLPort</th><th>Info</th></tr><?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['client_name'])?></td><td><?=htmlspecialchars($r['name'])?></td><td><?=htmlspecialchars($r['server_ip'])?></td><td><?=htmlspecialchars($r['server_port'])?></td><td><?=htmlspecialchars($r['ssl_port'])?></td><td><?=htmlspecialchars($r['info'])?></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__.'/includes/footer.php'; ?>
