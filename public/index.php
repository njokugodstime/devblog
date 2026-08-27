<?php require_once __DIR__ . '/../includes/header.php'; ?>

<h1>Latest Posts</h1>

<div class="post-grid">
    <?php
    $stmt = $pdo->query("
        SELECT posts.*, users.name AS author_name, categories.name AS category_name
        FROM posts
        JOIN users ON posts.user_id = users.id
        LEFT JOIN categories ON posts.category_id = categories.id
        WHERE posts.status = 'published'
        ORDER BY posts.created_at DESC
    ");
    $posts = $stmt->fetchAll();

    if (empty($posts)):
    ?>
        <p>No posts published yet.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <?php if ($post['image']): ?>
                    <img src="images/posts/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-thumb">
                <?php endif; ?>
                <span class="post-category"><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></span>
                <h2><a href="post.php?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                <p class="post-meta">By <?= htmlspecialchars($post['author_name']) ?> on <?= date('M j, Y', strtotime($post['created_at'])) ?></p>
                <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                <a href="post.php?slug=<?= urlencode($post['slug']) ?>">Read more &rarr;</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>