<?php
include 'db.php'; // uses your existing db connection + session_start()

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim(mysqli_real_escape_string($link, $_POST['username']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($password) || empty($confirm)) {

        $error = "Please fill in all fields";

    } elseif (strlen($username) < 3) {

        $error = "Username must be at least 3 characters";

    } elseif (strlen($password) < 4) {

        $error = "Password must be at least 4 characters";

    } elseif ($password !== $confirm) {

        $error = "Passwords do not match";

    } else {

        // Check if username already exists
        $checkUser = mysqli_query($link, "SELECT id FROM users WHERE username='$username'");

        if (mysqli_num_rows($checkUser) > 0) {

            $error = "Username already taken";

        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert = mysqli_query(
                $link,
                "INSERT INTO users (username, password) 
                 VALUES ('$username', '$hashedPassword')"
            );

            if ($insert) {

                $success = "Registration successful";

            } else {

                $error = "Something went wrong: " . mysqli_error($link);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <style>
        body{
            font-family: Arial;
            background:#f4f4f4;
        }

        .box{
            width:350px;
            margin:80px auto;
            background:white;
            padding:25px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        input{
            width:100%;
            padding:10px;
            margin-top:10px;
            box-sizing:border-box;
        }

        button{
            width:100%;
            padding:10px;
            margin-top:15px;
            background:#007bff;
            border:none;
            color:white;
            cursor:pointer;
        }

        button:hover{
            background:#0056b3;
        }

        .error{
            color:red;
            margin-top:10px;
        }

        .success{
            color:green;
            margin-top:10px;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Register</h2>

    <form method="POST">

        <input type="text" name="username" placeholder="Enter Username">

        <input type="password" name="password" placeholder="Enter Password">

        <input type="password" name="confirm_password" placeholder="Confirm Password">

        <button type="submit">Register</button>

    </form>

    <?php
        if($error){
            echo "<p class='error'>$error</p>";
        }

        if($success){
            echo "<p class='success'>$success</p>";
        }
    ?>

</div>

</body>
</html>