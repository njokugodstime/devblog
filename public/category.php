<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
if (!isset($_GET['slug']) || $_GET['slug'] === '') {
    echo "<p>Category not found.</p>";
} else {
    $catStmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $catStmt->execute([$_GET['slug']]);
    $category = $catStmt->fetch();

    if (!$category) {
        echo "<p>Category not found.</p>";
    } else {
?>
        <h1><?= htmlspecialchars($category['name']) ?></h1>

        <div class="post-grid">
            <?php
            $stmt = $pdo->prepare("
                SELECT posts.*, users.name AS author_name
                FROM posts
                JOIN users ON posts.user_id = users.id
                WHERE posts.category_id = ? AND posts.status = 'published'
                ORDER BY posts.created_at DESC
            ");
            $stmt->execute([$category['id']]);
            $posts = $stmt->fetchAll();

            if (empty($posts)):
            ?>
                <p>No posts in this category yet.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if ($post['image']): ?>
                            <img src="images/posts/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-thumb">
                        <?php endif; ?>
                        <h2><a href="post.php?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                        <p class="post-meta">By <?= htmlspecialchars($post['author_name']) ?> on <?= date('M j, Y', strtotime($post['created_at'])) ?></p>
                        <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                        <a href="post.php?slug=<?= urlencode($post['slug']) ?>">Read more &rarr;</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
<?php
    }
}
?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>