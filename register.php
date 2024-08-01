<?php
include 'db.php';

$error_message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error_message = "Username already taken.";
        } else {
            $stmt->close();

            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password_hash);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit(); 
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Register now</h1>
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="text" class="username" name="username" placeholder="Username" required><br>
                <input type="password" class="password" name="password" placeholder="Password" required><br>
                <button class="button" type="submit">Register</button>
            </form>
            <p>Already have an account? Sign in <a href="login.php">here</a></p>
        </div>
    </div>
</body>
</html>
