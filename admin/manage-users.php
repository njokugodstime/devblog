<?php
require_once __DIR__ . '/auth-check.php';

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $targetId = (int)$_POST['user_id'];
    $newRole = $_POST['role'] === 'admin' ? 'admin' : 'author';

    if ($targetId !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$newRole, $targetId]);
    }
    header('Location: manage-users.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($deleteId !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    header('Location: manage-users.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h1>Manage Users</h1>

<table class="cart-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <?php if ((int)$user['id'] === (int)$_SESSION['user_id']): ?>
                        <?= htmlspecialchars(ucfirst($user['role'])) ?> (you)
                    <?php else: ?>
                        <form method="POST" class="status-form">
                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                            <select name="role" onchange="this.form.submit()">
                                <option value="author" <?= $user['role'] === 'author' ? 'selected' : '' ?>>Author</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </form>
                    <?php endif; ?>
                </td>
                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                        <a href="manage-users.php?delete=<?= (int)$user['id'] ?>"
                           class="remove-link"
                           onclick="return confirm('Delete this user? Their posts will also be deleted.')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>