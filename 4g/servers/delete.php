<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();
$id=(int)($_GET['id']??0);
$userId=currentUserId();
if ($id > 0 && deleteServer($pdo,$id,$userId)) {
    
}
redirect('/4g/servers.php');
