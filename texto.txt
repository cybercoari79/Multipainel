<?php
declare(strict_types=1);
session_start();

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function decodeBlock(string $block): string {
    preg_match_all('/0x([0-9a-fA-F]{1,2})t\b/i', $block, $m);
    $raw = '';
    foreach ($m[1] as $hex) $raw .= chr(hexdec($hex));
    return $raw;
}
function bytesOf(string $s): array {
    $out = [];
    for ($i=0, $n=strlen($s); $i<$n; $i++) $out[] = ord($s[$i]);
    return $out;
}
function makeBlock(string $s): string {
    $lines = [];
    foreach (array_chunk(bytesOf($s), 8) as $chunk) {
        $parts = [];
        foreach ($chunk as $b) $parts[] = sprintf('0x%02xt', $b);
        $lines[] = '    '.implode(' ', $parts);
    }
    return '.array-data 1'."\n".implode("\n", $lines)."\n".'.end array-data';
}
function analyze(string $content): array {
    $out = [];
    preg_match_all('/(?<label>^[ \t]*:[A-Za-z0-9_.$-]+[ \t]*\R[ \t]*)(?<block>\.array-data\s+1\b.*?\.end\s+array-data\b)/ims', $content, $m, PREG_OFFSET_CAPTURE);
    foreach ($m['block'] as $i => $match) {
        $text = decodeBlock($match[0]);
        $out[] = [
            'index'=>$i+1, 'label'=>trim($m['label'][$i][0]),
            'line'=>substr_count(substr($content,0,$match[1]),"\n")+1,
            'text'=>$text, 'bytes'=>strlen($text), 'url'=>preg_match('~https?://~i',$text)===1,
            'start'=>$match[1], 'length'=>strlen($match[0])
        ];
    }
    return $out;
}
function findSizeInfo(string $content, string $label): array {
    if (!preg_match('/fill-array-data\s+([vp]\d+)\s*,\s*'.preg_quote($label,'/').'\b/', $content, $f, PREG_OFFSET_CAPTURE))
        throw new RuntimeException("Não encontrei fill-array-data para $label.");
    $arrayReg = $f[1][0];
    $before = substr($content, 0, $f[0][1]);
    $np = '/new-array\s+'.preg_quote($arrayReg,'/').'\s*,\s*([vp]\d+)\s*,\s*\[B\b/';
    if (!preg_match_all($np, $before, $n, PREG_OFFSET_CAPTURE) || !$n[0])
        throw new RuntimeException("Não encontrei o new-array associado a $arrayReg.");
    $last = count($n[0])-1;
    $sizeReg = $n[1][$last][0];
    $newPos = $n[0][$last][1];
    $preNew = substr($content,0,$newPos);
    $cp = '/\bconst(?:\/4|\/16)?\s+'.preg_quote($sizeReg,'/').'\s*,\s*(0x[0-9a-fA-F]+|\d+)\b/';
    if (!preg_match_all($cp,$preNew,$c,PREG_OFFSET_CAPTURE) || !$c[0])
        throw new RuntimeException("Não encontrei o const do tamanho do array ($sizeReg).");
    $ci = count($c[0])-1;
    $lit = $c[1][$ci][0];
    return ['array_reg'=>$arrayReg,'size_reg'=>$sizeReg,'size'=>preg_match('/^0x/i',$lit)?hexdec($lit):(int)$lit,'const_start'=>$c[0][$ci][1],'const_text'=>$c[0][$ci][0]];
}
function replaceBlocks(string $content, array $edits): array {
    $items = analyze($content);
    $chosen = [];
    foreach ($edits as $edit) {
        $idx = (int)($edit['index'] ?? 0);
        if (!isset($items[$idx-1])) throw new RuntimeException('Um bloco selecionado não foi encontrado. Analise novamente o Smali original.');
        $item = $items[$idx-1];
        $new = (string)($edit['new'] ?? '');
        if ($new === $item['text']) continue;
        if ($new === '') throw new RuntimeException("O novo texto do bloco #$idx está vazio.");
        if (strlen($new)>255) throw new RuntimeException("O texto do bloco #$idx ultrapassa 255 bytes.");
        $chosen[] = ['item'=>$item,'new'=>$new];
    }
    if (!$chosen) throw new RuntimeException('Nenhuma alteração detectada. Modifique pelo menos um campo.');
    usort($chosen, fn($a,$b)=>$b['item']['start'] <=> $a['item']['start']);
    $modified = $content; $changes=[];
    foreach ($chosen as $entry) {
        $item=$entry['item']; $label=preg_replace('/\s+/','',$item['label']); $new=$entry['new'];
        $info=findSizeInfo($modified,$label);
        $block=makeBlock($new);
        $modified=substr($modified,0,$item['start']).$block.substr($modified,$item['start']+$item['length']);
        $info=findSizeInfo($modified,$label);
        $replacement='const/16 '.$info['size_reg'].', '.strlen($new);
        $modified=substr($modified,0,$info['const_start']).$replacement.substr($modified,$info['const_start']+strlen($info['const_text']));
        $changes[]=['label'=>$label,'old'=>$item['text'],'new'=>$new,'old_bytes'=>$item['bytes'],'new_bytes'=>strlen($new)];
    }
    foreach ($changes as $ch) {
        $found=false;
        foreach (analyze($modified) as $item) if (trim($item['label'])===$ch['label'] && $item['text']===$ch['new']) {$found=true;break;}
        if (!$found) throw new RuntimeException('Validação do texto substituído falhou no bloco '.$ch['label'].'.');
        $info=findSizeInfo($modified,$ch['label']);
        if ($info['size']!==$ch['new_bytes']) throw new RuntimeException('O tamanho declarado não confere no bloco '.$ch['label'].'.');
    }
    return ['content'=>$modified,'changes'=>array_reverse($changes)];
}
function inputSmali(): array {
    if (isset($_FILES['smali']) && ($_FILES['smali']['error'] ?? UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE) {
        if ($_FILES['smali']['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Falha no envio do arquivo.');
        if ((int)$_FILES['smali']['size']>5*1024*1024) throw new RuntimeException('O arquivo ultrapassa 5 MB.');
        $s=file_get_contents($_FILES['smali']['tmp_name']);
        if ($s===false || $s==='') throw new RuntimeException('Arquivo vazio ou impossível de ler.');
        return [basename((string)$_FILES['smali']['name']),$s];
    }
    $s=(string)($_POST['smali_text']??'');
    if (trim($s)==='') throw new RuntimeException('Selecione um arquivo ou cole o Smali.');
    return ['Texto colado',$s];
}
if (isset($_GET['download'])) {
    $p=(string)($_SESSION['atx_out']??''); $n=(string)($_SESSION['atx_out_name']??'atx_editado.smali');
    if ($p===''||!is_file($p)) {http_response_code(404);exit('Arquivo não encontrado ou sessão expirada.');}
    header('Content-Type:text/plain; charset=utf-8'); header('X-Content-Type-Options:nosniff');
    header('Content-Disposition:attachment; filename="'.basename($n).'"'); readfile($p); exit;
}
if (isset($_GET['backup'])) {
    $p=(string)($_SESSION['atx_backup']??''); $n=(string)($_SESSION['atx_backup_name']??'atx_original.bak');
    if ($p===''||!is_file($p)) {http_response_code(404);exit('Backup não encontrado ou sessão expirada.');}
    header('Content-Type:application/octet-stream'); header('X-Content-Type-Options:nosniff');
    header('Content-Disposition:attachment; filename="'.basename($n).'"'); readfile($p); exit;
}
$error=''; $success=''; $report=[]; $sourceName=''; $source=''; $details='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        [$sourceName,$source]=inputSmali();
        if (($_POST['action']??'analyze')==='edit') {
            $indices=$_POST['edit_index']??[]; $values=$_POST['edit_new']??[];
            if (!is_array($indices)||!is_array($values)) throw new RuntimeException('Dados dos campos inválidos.');
            $edits=[];
            foreach ($indices as $k=>$v) $edits[]=['index'=>$v,'new'=>(string)($values[$k]??'')];
            $result=replaceBlocks($source,$edits);
            $stamp=date('Ymd_His'); $base=sys_get_temp_dir().'/atx3_'.session_id();
            if (!is_dir($base)&&!mkdir($base,0700,true)&&!is_dir($base)) throw new RuntimeException('Não foi possível criar a pasta temporária.');
            $stem=preg_replace('/[^A-Za-z0-9._-]/','_',pathinfo($sourceName,PATHINFO_FILENAME));
            $out=$base.'/'.$stem.'_editado_'.$stamp.'.smali'; $bak=$base.'/'.$stem.'_original_'.$stamp.'.bak';
            if (file_put_contents($bak,$source,LOCK_EX)===false) throw new RuntimeException('Não foi possível criar o backup.');
            if (file_put_contents($out,$result['content'],LOCK_EX)===false) { @unlink($bak); throw new RuntimeException('Não foi possível salvar o arquivo editado.'); }
            $_SESSION['atx_out']=$out; $_SESSION['atx_out_name']=basename($out);
            $_SESSION['atx_backup']=$bak; $_SESSION['atx_backup_name']=basename($bak);
            $lines=['Arquivo: '.$sourceName,'Blocos alterados: '.count($result['changes'])];
            foreach ($result['changes'] as $c) $lines[]=$c['label'].': '.$c['old'].' -> '.$c['new'].' ('.$c['old_bytes'].' -> '.$c['new_bytes'].' bytes)';
            $details=implode("\n",$lines)."\nValidação: OK\nBackup: criado";
            $success='Textos alterados e validação concluída.';
            $source=$result['content'];
        }
        $report=analyze($source);
    } catch (Throwable $e) { $error=$e->getMessage(); }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ATX 3.0 — Editor Smali</title>
<style>
*{box-sizing:border-box}body{margin:0;padding:16px;background:#0b1020;color:#eef2ff;font-family:Arial,sans-serif}.wrap{max-width:1000px;margin:auto}.card{background:#121a2b;border:1px solid #293754;border-radius:12px;padding:18px;margin-bottom:16px}h1{font-size:24px}h2{font-size:19px}p,.muted{color:#b7c4d9;line-height:1.5}label{display:block;font-weight:bold;margin:12px 0 7px}input[type=file],input[type=text],textarea{width:100%;padding:12px;border-radius:8px;border:1px solid #34425f;background:#080d18;color:#eef2ff}textarea{min-height:240px;font-family:monospace;color:#d9f99d}button,.dl{display:inline-block;margin:12px 8px 0 0;background:#2563eb;color:#fff;border:0;border-radius:8px;padding:12px 16px;text-decoration:none;font-size:15px}.secondary{background:#334155}.item{background:#0e1626;border:1px solid #293754;border-radius:9px;padding:13px;margin-top:10px}.url{border-color:#2563eb}.meta{font-size:12px;color:#9eacc4;margin-bottom:8px}.decoded{font-family:monospace;white-space:pre-wrap;overflow-wrap:anywhere;color:#d9f99d}.error{background:#450a0a;border:1px solid #7f1d1d;padding:13px;border-radius:8px;color:#fecaca}.success{background:#052e16;border:1px solid #166534;padding:13px;border-radius:8px;color:#bbf7d0}pre{white-space:pre-wrap;overflow-wrap:anywhere;background:#080d18;padding:12px;border-radius:8px}.badge{background:#1d4ed8;padding:3px 6px;border-radius:5px;margin-left:5px;font-size:11px}
</style></head><body><div class="wrap">
<div class="card"><h1>ATX 3.0 — Editor de textos Smali</h1><p>Envie ou cole o Smali e analise os blocos. Depois edite individualmente os textos decodificados e gere uma cópia modificada com backup do original.</p>
<form method="post" enctype="multipart/form-data">
<label>Arquivo .smali ou .txt</label><input type="file" name="smali" accept=".smali,.txt">
<label>Ou cole o Smali inteiro</label><textarea name="smali_text" placeholder="Cole aqui o Smali completo..."><?=h((string)($_POST['smali_text']??''))?></textarea>
<button class="secondary" name="action" value="analyze">Analisar sem alterar</button>
</form></div>
<?php if($error!==''): ?><div class="card error"><b>Erro:</b> <?=h($error)?></div><?php endif; ?>
<?php if($success!==''): ?><div class="card success"><b><?=h($success)?></b><pre><?=h($details)?></pre><a class="dl" href="?download=1">Baixar Smali editado</a><a class="dl" href="?backup=1">Baixar backup original</a></div><?php endif; ?>
<?php if($_SERVER['REQUEST_METHOD']==='POST'&&$error===''): ?>
<div class="card"><h2>Resultado da análise</h2><p>Fonte: <?=h($sourceName)?> · Blocos encontrados: <?=count($report)?></p>
<?php if(!$report): ?><p>Nenhum bloco .array-data 1 encontrado.</p><?php else: foreach($report as $it): ?><div class="item <?=$it['url']?'url':''?>"><div class="meta">Bloco #<?=$it['index']?> · <?=h($it['label'])?> · linha <?=$it['line']?> · <?=$it['bytes']?> bytes<?php if($it['url']): ?><span class="badge">POSSÍVEL URL</span><?php endif; ?></div><div class="decoded"><?=h($it['text'])?></div></div><?php endforeach; endif; ?></div>
<?php if($report): ?><div class="card"><h2>Editar os textos encontrados</h2><p>Altere apenas os campos desejados. Campos que permanecerem iguais serão ignorados. Para preservar o Smali, cada bloco alterado precisa ter uma estrutura compatível com fill-array-data e new-array.</p>
<form method="post"><textarea name="smali_text" style="display:none"><?=h($source)?></textarea>
<?php foreach($report as $it): ?><div class="item"><div class="meta">Bloco #<?=$it['index']?> · <?=h($it['label'])?> · <?=$it['bytes']?> bytes</div><label>Texto atual</label><div class="decoded"><?=h($it['text'])?></div><input type="hidden" name="edit_index[]" value="<?=$it['index']?>"><label for="new<?=$it['index']?>">Novo texto</label><input type="text" id="new<?=$it['index']?>" name="edit_new[]" value="<?=h($it['text'])?>"></div><?php endforeach; ?>
<button name="action" value="edit">Aplicar alterações e gerar Smali</button></form></div><?php endif; endif; ?>
</div></body></html>