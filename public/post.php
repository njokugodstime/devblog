<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
if (!isset($_GET['slug']) || $_GET['slug'] === '') {
    echo "<p>Post not found.</p>";
} else {
    $stmt = $pdo->prepare("
        SELECT posts.*, users.name AS author_name, categories.name AS category_name
        FROM posts
        JOIN users ON posts.user_id = users.id
        LEFT JOIN categories ON posts.category_id = categories.id
        WHERE posts.slug = ? AND posts.status = 'published'
    ");
    $stmt->execute([$_GET['slug']]);
    $post = $stmt->fetch();

    if (!$post) {
        echo "<p>Post not found.</p>";
    } else {

        $commentErrors = [];
        $commentSuccess = false;

        // Handle new comment submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $commentText = trim($_POST['comment'] ?? '');

            if ($name === '') $commentErrors[] = "Name is required.";
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $commentErrors[] = "A valid email is required.";
            if ($commentText === '') $commentErrors[] = "Comment cannot be empty.";

            if (empty($commentErrors)) {
                $insert = $pdo->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)");
                $insert->execute([$post['id'], $name, $email, $commentText]);
                $commentSuccess = true;
            }
        }

        // Fetch comments for this post
        $commentStmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC");
        $commentStmt->execute([$post['id']]);
        $comments = $commentStmt->fetchAll();
?>

    <article class="single-post">
        <?php if ($post['image']): ?>
            <img src="images/posts/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="single-post-image">
        <?php endif; ?>

        <span class="post-category"><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></span>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p class="post-meta">By <?= htmlspecialchars($post['author_name']) ?> on <?= date('M j, Y', strtotime($post['created_at'])) ?></p>

        <div class="post-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
    </article>

    <section class="comments-section">
        <h2>Comments (<?= count($comments) ?>)</h2>

        <?php if ($commentSuccess): ?>
            <div class="success-box"><p>Comment posted successfully!</p></div>
        <?php endif; ?>

        <?php if (!empty($commentErrors)): ?>
            <div class="error-box">
                <?php foreach ($commentErrors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($comments)): ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p class="comment-author"><?= htmlspecialchars($comment['name']) ?> <span class="comment-date"><?= date('M j, Y g:ia', strtotime($comment['created_at'])) ?></span></p>
                    <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h3>Leave a Comment</h3>
        <form method="POST" class="comment-form">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

            <label for="email">Email (won't be published)</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label for="comment">Comment</label>
            <textarea name="comment" id="comment" rows="4" required><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>

            <button type="submit">Post Comment</button>
        </form>
    </section>

<?php
    }
}
?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>