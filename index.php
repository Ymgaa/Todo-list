<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "todo_list";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah tugas
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task']) && !isset($_POST['edit_id'])) {
    $task = trim($_POST['task']);
    if (!empty($task)) {
        $task = $conn->real_escape_string($task);
        $conn->query("INSERT INTO tasks (task) VALUES ('$task')");
    }
    header("Location: index.php");
    exit;
}

// Update checkbox status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id']) && isset($_POST['is_done'])) {
    $id = intval($_POST['id']);
    $checked = $_POST['is_done'] == "on" ? 1 : 0;
    $conn->query("UPDATE tasks SET is_done = $checked WHERE id = $id");
    header("Location: index.php");
    exit;
}

// Hapus tugas
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tasks WHERE id = $id");
    header("Location: index.php");
    exit;
}

// Proses edit tugas
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
    $new_task = $conn->real_escape_string(trim($_POST['task']));
    $conn->query("UPDATE tasks SET task = '$new_task' WHERE id = $edit_id");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>To-Do List dengan Checkbox</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>To-Do List with Checkbox</title>
   <script>
        function confirmDelete(url) {
            if (confirm("Anda yakin akan menghapus data ini?")) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h2>📝 To-Do List</h2>

    <!-- Form tambah tugas -->
    <?php if (isset($_GET['edit'])): 
        $edit_id = intval($_GET['edit']);
        $result = $conn->query("SELECT * FROM tasks WHERE id = $edit_id");
        $edit_task = $result->fetch_assoc();
    ?>
        <!-- Form Edit -->
        <form method="POST" class="edit-form">
            <input type="hidden" name="edit_id" value="<?= $edit_task['id'] ?>">
            <input type="text" name="task" value="<?= htmlspecialchars($edit_task['task']) ?>" required>
            <button type="submit">Simpan</button>
            <a href="index.php" style="padding:12px 15px; background:#999; color:#000; border-radius:8px; text-decoration:none;">Batal</a>
        </form>
    <?php else: ?>
        <!-- Form Tambah -->
        <form method="POST" class="add-form">
            <input type="text" name="task" placeholder="Tulis tugas baru..." required>
            <button type="submit">Tambah</button>
        </form>
    <?php endif; ?>

    <!-- Daftar tugas -->
    <ul>
        <?php
        $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
        ?>
            <li class="<?= $row['is_done'] ? 'done' : '' ?>">
                <form method="POST" class="checkbox-form">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="checkbox" name="is_done" onchange="this.form.submit()" <?= $row['is_done'] ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars($row['task']) ?></span>
                </form>
                <div class="actions">
                    <a href="?edit=<?= $row['id'] ?>">✏️</a>
                    <a href="javascript:void(0);" onclick="confirmDelete('?delete=<?= $row['id'] ?>')">✖</a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
</body>
</html>