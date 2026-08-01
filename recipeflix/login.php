<?php 
include 'db.php';
$error = "";

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = mysqli_prepare($link, "SELECT id, password FROM users WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)) {
        if(password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            header("location: index.php");
            exit();
        }
    }
    $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign In | RecipeFlix</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">RECIPE<span>FLIX</span></a>
        </div>
    </nav>
    <div class="login-box">
        <h1>Sign In</h1>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Sign In</button>
        </form>
        <p style="color:#737373;margin-top:16px;">New to RecipeFlix? <a href="signup.php" style="color:#fff;text-decoration:none;">Sign up now</a>.</p>
        <p style="color:#737373;margin-top:20px;font-size:13px;">Demo login: admin / password</p>
    </div>
</body>
</html>