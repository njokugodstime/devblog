<?php
require_once __DIR__ . '/auth-check.php';

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$deleteId]);
    header('Location: manage-posts.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$posts = $pdo->query("
    SELECT posts.*, users.name AS author_name, categories.name AS category_name
    FROM posts
    JOIN users ON posts.user_id = users.id
    LEFT JOIN categories ON posts.category_id = categories.id
    ORDER BY posts.created_at DESC
")->fetchAll();
?>

<h1>Manage All Posts</h1>

<?php if (empty($posts)): ?>
    <p>No posts yet.</p>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
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
                    <td><?= htmlspecialchars($post['author_name']) ?></td>
                    <td><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></td>
                    <td><?= htmlspecialchars(ucfirst($post['status'])) ?></td>
                    <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                    <td>
                        <a href="manage-posts.php?delete=<?= (int)$post['id'] ?>"
                           class="remove-link"
                           onclick="return confirm('Delete this post? This cannot be undone.')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>