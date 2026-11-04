<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();

$users = getUsers($pdo);
$ok = 0;
$fail = 0;
foreach ($users as $user) {
    if (generateClientUpdateFiles($pdo, (int)$user['id'])) $ok++; else $fail++;
}

$_SESSION['flash_success'] = "Arquivos atualizados: {$ok}" . ($fail ? " | Falhas: {$fail}" : '');
redirect('/users/index.php');
