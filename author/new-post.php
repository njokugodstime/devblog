<?php
require_once __DIR__ . '/../includes/auth-check.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] === 'published' ? 'published' : 'draft';
    $imageName = '';

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
            $imageName = uniqid('post_') . '.' . $ext;
            $uploadDir = __DIR__ . '/../public/images/posts/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $errors[] = "Failed to upload image. Please try again.";
                $imageName = '';
            }
        }
    }

    if (empty($errors)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $title), '-'));

        $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }

        $insert = $pdo->prepare("INSERT INTO posts (user_id, category_id, title, slug, content, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$_SESSION['user_id'], $categoryId ?: null, $title, $slug, $content, $imageName, $status]);

        $success = true;
    }
}

require_once __DIR__ . '/../includes/header.php';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<h1>Write a New Post</h1>

<?php if ($success): ?>
    <div class="success-box">
        <p>Post saved successfully!</p>
        <a href="new-post.php">Write another</a> | <a href="my-posts.php">View my posts</a>
    </div>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>

        <label for="category_id">Category</label>
        <select name="category_id" id="category_id">
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="content">Content</label>
        <textarea name="content" id="content" rows="10"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>

        <label for="image">Featured Image</label>
        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp">

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="draft">Save as Draft</option>
            <option value="published">Publish Now</option>
        </select>

        <button type="submit">Save Post</button>
    </form>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>