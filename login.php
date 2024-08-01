<?php
session_start(); 

include 'db.php';

$error_message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 0) {
            $error_message = "Invalid username or password.";
        } else {
            $stmt->bind_result($id, $password_hash);
            $stmt->fetch();
            
            if (password_verify($password, $password_hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;

                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Login</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" class="username"  name="username" placeholder="Username" required><br>
            <input type="password" class="password" name="password" placeholder="Password" required><br>
            <button class="button" type="submit">Login</button>
        </form>
        <p>New user? Sign up <a href="register.php">here</a></p>
    </div>
</div>
</body>
</html>
