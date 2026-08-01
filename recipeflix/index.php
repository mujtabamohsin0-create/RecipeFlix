<?php 
include 'db.php'; // session_start() is already handled in db.php

// Hero is locked to Lava Cake - change 'Lava Cake' to match your exact title
$featured = mysqli_query($link, "SELECT * FROM recipes WHERE title LIKE '%Lava %' LIMIT 1");
$hero = mysqli_fetch_assoc($featured);

// Get category filter from URL if set
$cat_filter = isset($_GET['cat']) ? mysqli_real_escape_string($link, $_GET['cat']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>RecipeFlix - Watch Recipes</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">RECIPE<span>FLIX</span></a>
            <input type="text" id="searchInput" placeholder="Search recipes..." onkeyup="searchRecipes()">
            <select id="categoryFilter" onchange="filterByCategory()">
                <option value="">All Categories</option>
                <?php 
                $cats = mysqli_query($link, "SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category != '' ORDER BY category");
                if($cats) {
                    while($c = mysqli_fetch_assoc($cats)): 
                    ?>
                    <option value="<?= htmlspecialchars($c['category']) ?>" <?= $cat_filter == $c['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category']) ?>
                    </option>
                    <?php endwhile; 
                }
                ?>
            </select>
        </div>
        <div class="nav-right">
            <?php if(isset($_SESSION['username'])): ?>
                <?php if($_SESSION['username'] == 'admin'): ?>
                    <a href="add_recipe.php">Add Recipe</a>
                    <a href="remove_recipe.php">Manage Recipes</a>
                <?php endif; ?>
                <a href="mylist.php">My List</a>
                <span style="color:#e5e5e5;margin:0 10px;">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Banner with Video -->
    <div class="hero">
        <video autoplay muted loop playsinline id="heroVideo">
            <source src="img/hero-bg.mp4" type="video/mp4">
        </video>
        <div class="hero-content">
            <?php if($hero): ?>
                <h1><?= htmlspecialchars($hero['title']) ?></h1>
                <p><?= htmlspecialchars(substr($hero['description'], 0, 150)) ?>...</p>
                <div class="hero-buttons">
                    <a href="recipe.php?id=<?= (int)$hero['id'] ?>" class="btn-play">▶ Cook Now</a>
                    <a href="recipe.php?id=<?= (int)$hero['id'] ?>" class="btn-info">ⓘ More Info</a>
                </div>
            <?php else: ?>
                <h1>Chocolate Lava Cake</h1>
                <p>Decadent molten chocolate cake with a gooey center...</p>
                <a href="#recipes" class="btn-play">▶ Browse Recipes</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recipe Rows - WRAPPED WITH trending-section -->
    <div class="trending-section">
        <div class="rows" id="recipes">
            <?php
            // Build query with category filter
            $query = "SELECT * FROM recipes";
            if($cat_filter) {
                $query .= " WHERE category='".mysqli_real_escape_string($link, $cat_filter)."'";
            }
            $query .= " ORDER BY id DESC";
            $result = mysqli_query($link, $query);
            
            if($result && mysqli_num_rows($result) > 0) {
                echo "<h2>" . ($cat_filter ? htmlspecialchars($cat_filter) : "Trending Now") . "</h2><div class='row-posters'>";
                while($row = mysqli_fetch_assoc($result)) {
                    // Handle image path: DB may have 'img/file.jpg' or just 'file.jpg'
                    $img = $row['image_url'] ?? $row['image'] ?? 'placeholder.jpg';
                    $img_path = (strpos($img, 'img/') === 0) ? $img : 'img/' . $img;
                    
                    echo "<div class='poster'>
                            <a href='recipe.php?id=".(int)$row['id']."'>
                                <img src='img/placeholder.jpg' 
                                     data-src='" . htmlspecialchars($img_path) . "' 
                                     class='lazy' 
                                     alt='" . htmlspecialchars($row['title']) . "'
                                     loading='lazy'>
                                <div class='poster-title'>" . htmlspecialchars($row['title']) . "</div>
                            </a>
                          </div>";
                }
                echo "</div>";
            } else {
                echo "<h2>No recipes found</h2>";
            }
            ?>
        </div>
    </div>

<script>
// 1. Lazy Load Images with error fallback
document.addEventListener("DOMContentLoaded", function() {
  const lazyImages = document.querySelectorAll("img.lazy");
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.onerror = function() {
              this.src = 'img/placeholder.jpg';
          };
          img.classList.remove("lazy");
          observer.unobserve(img);
        }
      });
    });
    lazyImages.forEach(img => imageObserver.observe(img));
  } else {
    // Fallback for old browsers
    lazyImages.forEach(img => img.src = img.dataset.src);
  }
});

// 2. Navbar scroll effect
window.addEventListener('scroll', () => {
    const nav = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        nav.style.background = '#141414';
    } else {
        nav.style.background = 'linear-gradient(180deg, rgba(0,0,0,0.7) 10%, transparent)';
    }
});

// 3. Frontend search filter
function searchRecipes() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const posters = document.querySelectorAll('.poster');
    
    posters.forEach(poster => {
        const title = poster.querySelector('.poster-title').textContent.toLowerCase();
        if (title.includes(search)) {
            poster.style.display = 'block';
        } else {
            poster.style.display = 'none';
        }
    });
}

// 4. Category filter redirect
function filterByCategory() {
    const cat = document.getElementById('categoryFilter').value;
    if(cat) {
        window.location.href = 'index.php?cat=' + encodeURIComponent(cat);
    } else {
        window.location.href = 'index.php';
    }
}

// 5. Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) target.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>

</body>
</html>