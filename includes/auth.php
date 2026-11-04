<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function requireAdmin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}
