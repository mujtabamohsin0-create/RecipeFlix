<?php
session_start(); 
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid recipe ID");
}

$id = (int)$_GET['id'];

// Get recipe details
$stmt = mysqli_prepare($link, "SELECT * FROM recipes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$recipe = mysqli_fetch_assoc($result);

if (!$recipe) { 
    die("Recipe not found"); 
}

// Check if this recipe is already in user's list
$is_saved = false;
if (isset($_SESSION['user_id'])) {
    $check_stmt = mysqli_prepare($link, "SELECT id FROM mylist WHERE user_id = ? AND recipe_id = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $_SESSION['user_id'], $id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $is_saved = mysqli_num_rows($check_result) > 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($recipe['title']); ?> | RecipeFlix</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .recipe-page { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .recipe-hero { 
            height: 400px; 
            background-size: cover; 
            background-position: center; 
            display: flex; 
            align-items: flex-end; 
            padding: 40px;
            border-radius: 8px;
            position: relative;
        }
        .recipe-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            border-radius: 8px;
        }
        .recipe-hero h1 { 
            color: #fff; 
            font-family: 'Bebas Neue', sans-serif; 
            font-size: 48px; 
            position: relative;
            z-index: 2;
            margin: 0;
        }
        .recipe-content { 
            background: #181818; 
            color: #fff; 
            padding: 30px; 
            border-radius: 8px; 
            margin-top: 20px; 
        }
        .recipe-meta { 
            display: flex; 
            gap: 30px; 
            margin-bottom: 20px; 
            font-weight: 600; 
            color: #b3b3b3;
        }
        .desc { font-size: 18px; line-height: 1.6; margin-bottom: 30px; }
        pre { 
            white-space: pre-wrap; 
            font-family: 'Montserrat', sans-serif; 
            background: #222; 
            padding: 20px; 
            border-radius: 5px; 
            line-height: 1.8;
        }
        .list-btn {
            background: #E50914;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px 0;
            transition: 0.2s;
        }
        .list-btn:hover { background: #f40612; }
        .list-btn.saved { background: #46d369; cursor: default; }
        .list-btn.saved:hover { background: #46d369; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="recipe-page">
        <div class="recipe-hero" style="background-image: url('<?php echo htmlspecialchars($recipe['image_url']); ?>')">
            <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        </div>
        
        <div class="recipe-content">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="mylist.php" method="POST">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    <?php if ($is_saved): ?>
                        <button type="submit" name="remove" class="list-btn saved">✓ Saved to My List</button>
                    <?php else: ?>
                        <button type="submit" name="add" class="list-btn">+ Add to My List</button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <a href="login.php"><button class="list-btn">Login to Save Recipe</button></a>
            <?php endif; ?>

            <div class="recipe-meta">
                 <span>⏱ <?php echo htmlspecialchars($recipe['cooking_time']); ?></span>
    <span>📊 <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
    <span>🍽 <?php echo htmlspecialchars($recipe['category']); ?></span>
            </div>
            
            <p class="desc"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
            
            <h3>Ingredients</h3>
            <pre><?php echo htmlspecialchars($recipe['ingredients']); ?></pre>
            
            <h3>Instructions</h3>
            <pre><?php echo htmlspecialchars($recipe['instructions']); ?></pre>
        </div>
    </div>
</body>
</html>