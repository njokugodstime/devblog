<?php
require_once __DIR__ . '/../includes/auth-check.php';

if (!isset($_GET['id'])) {
    header('Location: my-posts.php');
    exit;
}

$postId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$postId, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: my-posts.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] === 'published' ? 'published' : 'draft';
    $imageName = $post['image'];

    if ($title === '') $errors[] = "Title is required.";
    if ($content === '') $errors[] = "Content is required.";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Image must be a JPG, PNG, or WEBP file.";
        } elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
            $errors[] = "Image must be under 3MB.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newImageName = uniqid('post_') . '.' . $ext;
            $uploadDir = __DIR__ . '/../public/images/posts/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImageName)) {
                if ($imageName && file_exists($uploadDir . $imageName)) {
                    unlink($uploadDir . $imageName);
                }
                $imageName = $newImageName;
            } else {
                $errors[] = "Failed to upload new image. Please try again.";
            }
        }
    }

    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE posts SET category_id = ?, title = ?, content = ?, image = ?, status = ? WHERE id = ? AND user_id = ?");
        $update->execute([$categoryId ?: null, $title, $content, $imageName, $status, $postId, $_SESSION['user_id']]);

        $success = true;

        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();
    }
}

require_once __DIR__ . '/../includes/header.php';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<h1>Edit Post</h1>

<?php if ($success): ?>
    <div class="success-box"><p>Post updated successfully!</p></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($post['image']): ?>
    <img src="/devblog/public/images/posts/<?= htmlspecialchars($post['image']) ?>" alt="Current image" class="current-product-image">
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>

    <label for="category_id">Category</label>
    <select name="category_id" id="category_id">
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= $post['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="content">Content</label>
    <textarea name="content" id="content" rows="10"><?= htmlspecialchars($post['content']) ?></textarea>

    <label for="image">Replace Image (optional)</label>
    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp">

    <label for="status">Status</label>
    <select name="status" id="status">
        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
    </select>

    <button type="submit">Save Changes</button>
</form>

<a href="my-posts.php" class="back-link" style="margin-top: 15px; display: inline-block;">&larr; Back to My Posts</a>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>