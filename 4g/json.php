<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php'; requireLogin();
$uid=isAdmin()?(int)($_GET['user']??currentUserId()):currentUserId(); $u=getUser($pdo,$uid);
if(!$u||($u['status']??'')!=='active'||(!isAdmin()&&$uid!==currentUserId())){http_response_code(403);exit('Usuário inválido ou acesso negado.');}
$json=build4gConfig($pdo,$uid); $text=json_encode($json,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
function h4($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
?><!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Config.json — 4G</title><link rel="stylesheet" href="<?=h4(appUrl('/assets/css/style.css'))?>"></head><body><div class="dashboard"><?php require __DIR__.'/../includes/menu.php';?><main class="main-content"><h1>Config.json <span>4G</span></h1><p class="subtitle">Modelo oficial do aplicativo 4G.</p><button class="btn btn-save" onclick="navigator.clipboard.writeText(document.getElementById('j').textContent)">📋 Copiar JSON</button><pre id="j" style="background:#0b1220;color:#e5e7eb;padding:18px;border-radius:12px;overflow:auto;margin-top:18px"><?=h4($text)?></pre></main></div></body></html>
