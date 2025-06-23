<?php
session_start();

// Inisialisasi array tugas
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = []; // Format: [['text' => ..., 'done' => false], ...]
}

// Perbaiki data lama yang mungkin masih string
foreach ($_SESSION['tasks'] as $i => $task) {
    if (!is_array($task)) {
        $_SESSION['tasks'][$i] = ['text' => $task, 'done' => false];
    }
}

// === CREATE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && !isset($_POST['edit_index'])) {
    $task = trim($_POST['task']);
    if (!empty($task)) {
        $_SESSION['tasks'][] = ['text' => $task, 'done' => false];
    }
    header("Location: index.php");
    exit;
}

// === UPDATE (Edit Text) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_index'])) {
    $index = intval($_POST['edit_index']);
    $task = trim($_POST['task']);
    if (isset($_SESSION['tasks'][$index]) && !empty($task)) {
        $_SESSION['tasks'][$index]['text'] = $task;
    }
    header("Location: index.php");
    exit;
}

// === DELETE ===
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($_SESSION['tasks'][$index])) {
        unset($_SESSION['tasks'][$index]);
        $_SESSION['tasks'] = array_values($_SESSION['tasks']); // reset index
    }
    header("Location: index.php");
    exit;
}

// === TOGGLE CHECKBOX ===
if (isset($_GET['toggle'])) {
    $index = intval($_GET['toggle']);
    if (isset($_SESSION['tasks'][$index])) {
        $_SESSION['tasks'][$index]['done'] = !$_SESSION['tasks'][$index]['done'];
    }
    header("Location: index.php");
    exit;
}

// Untuk edit form
$edit_mode = false;
$edit_index = null;
$edit_task = "";

if (isset($_GET['edit'])) {
    $edit_index = intval($_GET['edit']);
    if (isset($_SESSION['tasks'][$edit_index])) {
        $edit_mode = true;
        $edit_task = $_SESSION['tasks'][$edit_index]['text'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
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

    <!-- FORM TAMBAH / EDIT -->
    <form method="POST">
        <input type="text" name="task" placeholder="Tulis tugas..." value="<?= htmlspecialchars($edit_task) ?>" required>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_index" value="<?= $edit_index ?>">
            <button type="submit">Simpan Edit</button>
            <a href="index.php" style="padding:10px 15px;background:#ccc;border-radius:5px;text-decoration:none;">Batal</a>
        <?php else: ?>
            <button type="submit">Tambah</button>
        <?php endif; ?>
    </form>

    <!-- LIST TUGAS -->
    <ul>
        <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
            <li>
                <div class="left">
                    <input type="checkbox" onchange="window.location.href='?toggle=<?= $index ?>'" <?= $task['done'] ? 'checked' : '' ?>>
                    <span class="<?= $task['done'] ? 'done-text' : '' ?>">
                        <?= htmlspecialchars($task['text']) ?>
                    </span>
                </div>
                <div class="actions">
                    <a href="?edit=<?= $index ?>">✏️</a>
                    <a href="?delete=<?= $index ?>" onclick="return confirm('Yakin hapus?')" style="color:red">🗑️</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>