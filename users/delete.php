<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // Guarda o diretório antes de apagar o usuário.
    $dir = getClientUpdateDirectory($pdo, $id);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
    if (is_dir($dir)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($dir);
    }
}
logActivity($pdo, 'Excluiu usuário', 'Usuários', 'Usuário ID ' . $id . ' excluído.', currentUserId());
redirect('/../users/index.php');
