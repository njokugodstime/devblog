<?php
require_once __DIR__ . '/auth-check.php';

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$deleteId]);
    header('Location: manage-comments.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$comments = $pdo->query("
    SELECT comments.*, posts.title AS post_title, posts.slug AS post_slug
    FROM comments
    JOIN posts ON comments.post_id = posts.id
    ORDER BY comments.created_at DESC
")->fetchAll();
?>

<h1>Manage Comments</h1>

<?php if (empty($comments)): ?>
    <p>No comments yet.</p>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Comment</th>
                <th>By</th>
                <th>On Post</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($comment['comment'], 0, 80)) ?><?= strlen($comment['comment']) > 80 ? '...' : '' ?></td>
                    <td><?= htmlspecialchars($comment['name']) ?><br><small><?= htmlspecialchars($comment['email']) ?></small></td>
                    <td><a href="/devblog/public/post.php?slug=<?= urlencode($comment['post_slug']) ?>" target="_blank"><?= htmlspecialchars($comment['post_title']) ?></a></td>
                    <td><?= date('M j, Y', strtotime($comment['created_at'])) ?></td>
                    <td>
                        <a href="manage-comments.php?delete=<?= (int)$comment['id'] ?>"
                           class="remove-link"
                           onclick="return confirm('Delete this comment?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>