<?php 
include 'db.php';
// Simple admin check - change 'admin' to your username
if(!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    die('Access denied');
}

if(isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $desc = mysqli_real_escape_string($link, $_POST['description']);
    $ing = mysqli_real_escape_string($link, $_POST['ingredients']);
    $inst = mysqli_real_escape_string($link, $_POST['instructions']);
    $cat = mysqli_real_escape_string($link, $_POST['category']);
    $time = mysqli_real_escape_string($link, $_POST['cooking_time']);
    $diff = mysqli_real_escape_string($link, $_POST['difficulty']);
    
    // Handle image upload
    $img_name = 'placeholder.jpg';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $img_name = 'img/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $img_name);
    }
    
    $query = "INSERT INTO recipes (title, description, ingredients, instructions, category, image_url, cooking_time, difficulty) 
              VALUES ('$title', '$desc', '$ing', '$inst', '$cat', '$img_name', '$time', '$diff')";
    
    if(mysqli_query($link, $query)) {
        echo "Recipe added! <a href='index.php'>View site</a>";
    } else {
        echo "Error: " . mysqli_error($link);
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Recipe</title><link rel="stylesheet" href="style.css"></head>
<body class="login-body">
    <div class="login-box">
        <h1>Add New Recipe</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Recipe Title" required>
            <input type="text" name="description" placeholder="Short Description" required>
            <textarea name="ingredients" placeholder="Ingredients - one per line" required></textarea>
            <textarea name="instructions" placeholder="Instructions - one per line" required></textarea>
            <input type="text" name="category" placeholder="Category: Italian, Breakfast, etc" required>
            <input type="text" name="cooking_time" placeholder="Cooking Time: 20 mins">
            <select name="difficulty" required>
                <option value="">Difficulty</option>
                <option>Easy</option><option>Medium</option><option>Hard</option>
            </select>
            <input type="file" name="image" accept="image/*">
            <button type="submit" name="submit">Add Recipe</button>
        </form>
    </div>
</body>
</html>