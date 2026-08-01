<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "recipe_netflix";

$link = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_errno()) {
    die("DB Connection failed: " . mysqli_connect_error());
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>