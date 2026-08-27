<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/header.php';

$userId = $_SESSION['user_id'];

$totalPosts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$totalPosts->execute([$userId]);
$totalPosts = $totalPosts->fetchColumn();

$publishedPosts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'published'");
$publishedPosts->execute([$userId]);
$publishedPosts = $publishedPosts->fetchColumn();

$draftPosts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'draft'");
$draftPosts->execute([$userId]);
$draftPosts = $draftPosts->fetchColumn();
?>

<h1>Author Dashboard</h1>
<p>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>

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
        <h3>Drafts</h3>
        <p><?= (int)$draftPosts ?></p>
    </div>
</div>

<div class="admin-links">
    <a href="new-post.php" class="admin-btn">+ New Post</a>
    <a href="my-posts.php" class="admin-btn">My Posts</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>