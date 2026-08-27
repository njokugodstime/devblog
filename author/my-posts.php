<?php
require_once __DIR__ . '/../includes/auth-check.php';

// Handle delete (only own posts)
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$deleteId, $_SESSION['user_id']]);
    header('Location: my-posts.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare("
    SELECT posts.*, categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.user_id = ?
    ORDER BY posts.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();
?>

<h1>My Posts</h1>

<a href="new-post.php" class="admin-btn" style="margin-bottom: 15px; display: inline-block;">+ New Post</a>

<?php if (empty($posts)): ?>
    <p>You haven't written any posts yet.</p>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post['title']) ?></td>
                    <td><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></td>
                    <td><?= htmlspecialchars(ucfirst($post['status'])) ?></td>
                    <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                    <td>
                        <a href="edit-post.php?id=<?= (int)$post['id'] ?>" class="edit-link">Edit</a>
                        <a href="my-posts.php?delete=<?= (int)$post['id'] ?>"
                           class="remove-link"
                           onclick="return confirm('Delete this post? This cannot be undone.')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>