<?php
require_once __DIR__ . '/auth-check.php';
require_once __DIR__ . '/../includes/header.php';

$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$publishedPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
?>

<h1>Admin Dashboard</h1>

<div class="admin-stats">
    <div class="stat-card">
        <h3>Total Posts</h3>
        <p><?= (int)$totalPosts ?></p>
    </div>
    <div class="stat-card">
        <h3>Published</h3>
        <p><?= (int)$publishedPosts ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Users</h3>
        <p><?= (int)$totalUsers ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Comments</h3>
        <p><?= (int)$totalComments ?></p>
    </div>
</div>

<div class="admin-links">
    <a href="manage-posts.php" class="admin-btn">Manage Posts</a>
    <a href="manage-users.php" class="admin-btn">Manage Users</a>
    <a href="manage-comments.php" class="admin-btn">Manage Comments</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>