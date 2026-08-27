<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBlog</title>
    <link rel="stylesheet" href="/devblog/public/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="logo"><a href="/devblog/public/index.php">DevBlog</a></div>
        <nav>
            <a href="/devblog/public/index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="/devblog/admin/dashboard.php">Admin</a>
                <?php else: ?>
                    <a href="/devblog/author/dashboard.php">My Dashboard</a>
                <?php endif; ?>
                <span>Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="/devblog/public/logout.php">Logout</a>
            <?php else: ?>
                <a href="/devblog/public/login.php">Login</a>
                <a href="/devblog/public/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>