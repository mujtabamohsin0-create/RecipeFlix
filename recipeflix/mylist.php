<?php
include 'db.php';

if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get user_id from session or look it up from username
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $stmt = mysqli_prepare($link, "SELECT id FROM users WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $u = mysqli_fetch_assoc($r);
    $user_id = $u['id'];
    $_SESSION['user_id'] = $user_id;
}

// Add to list
if(isset($_POST['add'])) {
    $recipe_id = (int)$_POST['recipe_id'];
    $stmt = mysqli_prepare($link, "INSERT IGNORE INTO mylist (user_id, recipe_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $recipe_id);
    mysqli_stmt_execute($stmt);
    header("Location: mylist.php");
    exit();
}

// Remove from list
if(isset($_POST['remove'])) {
    $recipe_id = (int)$_POST['recipe_id'];
    $stmt = mysqli_prepare($link, "DELETE FROM mylist WHERE user_id=? AND recipe_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $recipe_id);
    mysqli_stmt_execute($stmt);
    header("Location: mylist.php");
    exit();
}

// Fetch saved recipes
$stmt = mysqli_prepare($link, "SELECT r.* FROM recipes r 
                                JOIN mylist m ON r.id = m.recipe_id 
                                WHERE m.user_id = ? 
                                ORDER BY m.saved_at DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My List | RecipeFlix</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .mylist-hero {
            position: relative;
            height: 70vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 0 50px;
        }
        .mylist-hero video {
            position: absolute;
            top: -20%;
            left: -5%;
            width: 110%;
            height: 140%;
            object-fit: cover;
            will-change: transform;
            transition: transform 0.08s ease-out;
        }
        .mylist-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(77deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 60%, transparent 100%);
            z-index: 1;
        }
        .mylist-hero-content {
            position: relative;
            z-index: 2;
            padding-top: 70px;
        }
        .mylist-hero-content h1 {
            font-size: 52px;
            font-family: 'Bebas Neue', cursive;
            letter-spacing: 2px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
        }
        .mylist-hero-content p {
            color: #b3b3b3;
            font-size: 16px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- 3D Parallax Hero -->
    <div class="mylist-hero" id="mylistHero">
        <video autoplay muted loop playsinline id="mylistVideo">
            <source src="img/hero-bg.mp4" type="video/mp4">
        </video>
        <div class="mylist-hero-content">
            <h1>My List</h1>
            <p>Your saved recipes, all in one place</p>
        </div>
    </div>

    <div class="rows" style="padding-top: 30px;">

        <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="row-posters">
            <?php while($row = mysqli_fetch_assoc($result)): 
                $img = !empty($row['image_url']) ? $row['image_url'] : 'placeholder.jpg';
                $img_path = (strpos($img, 'img/') === 0) ? $img : 'img/' . $img;
            ?>
            <div class="poster">
                <a href="recipe.php?id=<?= (int)$row['id'] ?>">
                    <img src="<?= htmlspecialchars($img_path) ?>"
                         onerror="this.onerror=null;this.src='img/placeholder.jpg'"
                         alt="<?= htmlspecialchars($row['title']) ?>">
                    <div class="poster-title"><?= htmlspecialchars($row['title']) ?></div>
                </a>
                <form method="POST" style="text-align:center;margin-top:6px;">
                    <input type="hidden" name="recipe_id" value="<?= (int)$row['id'] ?>">
                    <button type="submit" name="remove" class="btn-remove" onclick="event.stopPropagation()">✕ Remove</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
            <p style="color:#737373;margin-top:20px;">
                You haven't saved any recipes yet. 
                <a href="index.php" style="color:#fff;">Browse recipes</a>
            </p>
        <?php endif; ?>
    </div>

<script>
// 3D Parallax mouse effect on hero video
const hero = document.getElementById('mylistHero');
const video = document.getElementById('mylistVideo');

hero.addEventListener('mousemove', (e) => {
    const rect = hero.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;  // -0.5 to 0.5
    const y = (e.clientY - rect.top) / rect.height - 0.5;

    video.style.transform = `translate(${x * 20}px, ${y * 15}px) scale(1.08)`;
});

hero.addEventListener('mouseleave', () => {
    video.style.transform = 'translate(0, 0) scale(1.08)';
});

// Scroll parallax
window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    if(scrolled < hero.offsetHeight) {
        video.style.transform = `translateY(${scrolled * 0.4}px) scale(1.08)`;
    }
});
</script>

</body>
</html>