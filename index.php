<?php
// // Koneksi ke database

$host = "localhost";
$user = "root";
$pass = "";
$db   = "todo_list";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// Tambah tugas baru

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task'])) {
    $task = trim($_POST['task']);
    if (!empty($task)) {
        $task = $conn->real_escape_string($task);
        $conn->query("INSERT INTO tasks (task) VALUES ('$task')");
    }
    header("Location: index.php");
    exit;
}

// Update status (checkbox)

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $checked = isset($_POST['is_done']) ? 1 : 0;
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
    <form method="POST" class="add-form">
        <input type="text" name="task" placeholder="Tulis tugas baru..." required>
        <button type="submit">Tambah</button>
    </form>

    <!-- Daftar tugas -->
    <ul>
        <?php
        $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
        ?>
            <li class="<?= $row['is_done'] ? 'done' : '' ?>">
                <!-- Checkbox -->
                <form method="POST" class="checkbox-form">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="checkbox" name="is_done" onchange="this.form.submit()" <?= $row['is_done'] ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars($row['task']) ?></span>
                </form>

                <!-- Tombol hapus dengan konfirmasi -->
                <a href="javascript:void(0);" onclick="confirmDelete('?delete=<?= $row['id'] ?>')" class="delete-link">✖</a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
</body>
</html>