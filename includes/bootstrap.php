<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/atx.php';
require_once __DIR__ . '/connectplus.php';
require_once __DIR__ . '/4g.php';
global $pdo;
if (!isset($pdo) || !($pdo instanceof PDO)) {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
    } catch (PDOException $e) { die('Erro na conexão com o banco de dados.'); }
}
function isLoggedIn(): bool {
    if (empty($_SESSION['v2_user_id']) || empty($_SESSION['v2_logged_in'])) return false;
    if (!empty($_SESSION['v2_last_activity']) && (time() - (int)$_SESSION['v2_last_activity']) > SESSION_TIMEOUT) { logout(); return false; }
    $_SESSION['v2_last_activity'] = time();
    return true;
}
function currentUserId(): int { return (int)($_SESSION['v2_user_id'] ?? 0); }
function currentUser(): ?array {
    global $pdo; $id=currentUserId(); if($id<=0)return null;
    $stmt=$pdo->prepare('SELECT * FROM users WHERE id=? LIMIT 1'); $stmt->execute([$id]); return $stmt->fetch() ?: null;
}
function isAdmin(): bool { return ($_SESSION['v2_role'] ?? '') === 'admin'; }
function requireLogin(): void { if (!isLoggedIn()) redirect('/admin/login.php'); }
function requireAdmin(): void { requireLogin(); if(!isAdmin()){http_response_code(403);exit('Acesso permitido somente ao administrador.');} }
function login(string $username, string $password): bool {
    global $pdo;
    try {
        $stmt=$pdo->prepare('SELECT id,username,password,role,status FROM users WHERE username=? LIMIT 1');
        $stmt->execute([$username]); $user=$stmt->fetch();
        if(!$user || $user['status']!=='active' || !password_verify($password,$user['password'])) return false;
        session_regenerate_id(true);
        $_SESSION['v2_user_id']=(int)$user['id']; $_SESSION['v2_username']=$user['username']; $_SESSION['v2_role']=$user['role'];
        $_SESSION['v2_logged_in']=true; $_SESSION['v2_last_activity']=time();
        logActivity($pdo, 'Login', 'Autenticação', 'Login realizado.');
        return true;
    } catch(PDOException $e){ return false; }
}
function logout(): void {
    global $pdo;
    $id = currentUserId();
    if ($id > 0) logActivity($pdo, 'Logout', 'Autenticação', 'Logout realizado.', $id);
    $_SESSION = [];
    session_destroy();
}
function appUrl(string $path = ''): string {
    $base = rtrim(SITE_URL, '/');
    $path = trim($path);
    if ($path === '') return $base . '/';
    if (preg_match('#^https?://#i', $path)) return $path;
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

function redirect(string $url): void {
    header('Location: ' . appUrl($url), true, 302);
    exit;
}
function sanitizeInput(string $input): string { return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8'); }
function getStatusBadge(string $status): string {
    $badges = [
        'active' => '<span class="badge badge-success">Ativo</span>',
        'inactive' => '<span class="badge badge-warning">Inativo</span>',
        'suspended' => '<span class="badge badge-danger">Suspenso</span>',
        'premium' => '<span class="badge badge-premium">Premium</span>',
        'free' => '<span class="badge badge-free">Free</span>',
        'teste' => '<span class="badge badge-test">Teste</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
}
?>