<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function addTask($task) {
    global $conn, $user_id;
    if (!empty($task)) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $task);
        $stmt->execute();
        $stmt->close();
    }
}

function removeTask($task) {
    global $conn, $user_id;
    if (!empty($task)) {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE user_id = ? AND task = ?");
        $stmt->bind_param("is", $user_id, $task);
        $stmt->execute();
        $stmt->close();
    }
}

function toggleTask($task) {
    global $conn, $user_id;
    if (!empty($task)) {
        $stmt = $conn->prepare("UPDATE tasks SET completed = NOT completed WHERE user_id = ? AND task = ?");
        $stmt->bind_param("is", $user_id, $task);
        $stmt->execute();
        $stmt->close();
    }
}

function displayTasks() {
    global $conn, $user_id;
    $stmt = $conn->prepare("SELECT task, completed FROM tasks WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $task = htmlspecialchars($row['task'], ENT_QUOTES);
        $checked = $row['completed'] ? 'checked' : '';
        $completedClass = $row['completed'] ? 'completed' : '';
        echo "<li class='task-container'>
            <div class='task-content'>
                <form method='POST' style='display:flex; align-items:center;'>
                    <input type='hidden' name='action' value='toggle'>
                    <input type='hidden' name='task' value='$task'>
                    <input type='checkbox' onchange='this.form.submit()' $checked>
                </form>
                <span class='$completedClass'>$task</span>
            </div>
            <div class='remove-content'>
                <form method='POST' style='display:flex; align-items:center;'>
                    <input type='hidden' name='action' value='remove'>
                    <input type='hidden' name='task' value='$task'>
                    <button class='button' type='submit'>Remove</button>
                </form>
            </div>
        </li>";
    }
    echo "</ul>";
    $stmt->close();
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $task = $_POST['task'] ?? '';

    switch ($action) {
        case 'add':
            if (!empty($task)) {
                addTask($task);
            }
            break;

        case 'remove':
            if (!empty($task)) {
                removeTask($task);
            }
            break;

        case 'toggle':
            if (!empty($task)) {
                toggleTask($task);
            }
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>To Do List</title>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>To Do List</h1>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input class="input-text" type="text" name="task" id="task" placeholder="Enter task..." required>
            <button class="add-button button" type="submit">+</button>
        </form>
        <div class="list-container">
            <?php displayTasks(); ?>
        </div>
    </div>
    <form action="logout.php" method="post">
        <button class="logout-button"  type="submit">Logout</button>
        
    </form>
</div>
</body>
</html>
