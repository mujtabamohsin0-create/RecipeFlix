<?php 
include 'db.php';

// Admin check
if(!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    die('Access denied');
}

// Handle single recipe deletion
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $recipe_id = mysqli_real_escape_string($link, $_GET['delete']);
    
    // Get image path before deleting
    $img_query = "SELECT image_url FROM recipes WHERE id = '$recipe_id'";
    $img_result = mysqli_query($link, $img_query);
    $row = mysqli_fetch_assoc($img_result);
    
    // Delete the recipe
    $query = "DELETE FROM recipes WHERE id = '$recipe_id'";
    
    if(mysqli_query($link, $query)) {
        // Delete image file if it's not the placeholder
        if($row['image_url'] != 'placeholder.jpg' && !empty($row['image_url'])) {
            if(file_exists($row['image_url'])) {
                unlink($row['image_url']);
            }
        }
        $success = "Recipe deleted successfully!";
    } else {
        $error = "Error deleting recipe: " . mysqli_error($link);
    }
}

// Handle multiple deletion
if(isset($_POST['delete_multiple']) && isset($_POST['recipe_ids'])) {
    $ids = array_map('intval', $_POST['recipe_ids']);
    $ids_string = implode(',', $ids);
    
    // Get images before deletion
    $img_query = "SELECT image_url FROM recipes WHERE id IN ($ids_string)";
    $img_result = mysqli_query($link, $img_query);
    while($row = mysqli_fetch_assoc($img_result)) {
        if($row['image_url'] != 'placeholder.jpg' && !empty($row['image_url'])) {
            if(file_exists($row['image_url'])) {
                unlink($row['image_url']);
            }
        }
    }
    
    $query = "DELETE FROM recipes WHERE id IN ($ids_string)";
    if(mysqli_query($link, $query)) {
        $success = count($ids) . " recipes deleted successfully!";
    } else {
        $error = "Error deleting recipes: " . mysqli_error($link);
    }
}

// Get all recipes
$query = "SELECT * FROM recipes ORDER BY id DESC";
$result = mysqli_query($link, $query);
$recipes = [];
while($row = mysqli_fetch_assoc($result)) {
    $recipes[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Remove Recipes | RecipeFlix</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .remove-container {
            padding-top: 90px;
            padding-bottom: 50px;
            min-height: 100vh;
            background: #141414;
        }
        .remove-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            border-bottom: 1px solid #333;
        }
        .remove-header h1 {
            font-family: 'Bebas Neue', cursive;
            font-size: 40px;
            color: #e50914;
        }
        .remove-header .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .remove-header .actions a {
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-add {
            background: #e50914;
            color: #fff;
        }
        .btn-add:hover { background: #f40612; }
        .btn-back {
            background: #333;
            color: #fff;
        }
        .btn-back:hover { background: #444; }
        
        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 30px 50px;
        }
        .recipe-card {
            background: #1f1f1f;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            border: 1px solid #333;
        }
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            border-color: #e50914;
        }
        .recipe-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #333;
        }
        .recipe-card .card-body {
            padding: 15px;
        }
        .recipe-card .card-body h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #fff;
        }
        .recipe-card .card-body .meta {
            color: #8c8c8c;
            font-size: 13px;
            margin: 4px 0;
        }
        .recipe-card .card-body .meta span {
            color: #46d369;
        }
        .recipe-card .card-actions {
            display: flex;
            gap: 10px;
            padding: 12px 15px;
            border-top: 1px solid #333;
            background: #1a1a1a;
            align-items: center;
        }
        .recipe-card .card-actions .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #8c8c8c;
            font-size: 13px;
        }
        .recipe-card .card-actions .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #e50914;
        }
        .delete-btn {
            background: #e50914;
            color: #fff;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
            margin-left: auto;
            text-decoration: none;
        }
        .delete-btn:hover {
            background: #b20710;
        }
        
        .bulk-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 15px 50px;
            background: #1a1a1a;
            border-bottom: 1px solid #333;
        }
        .bulk-actions .btn-select {
            background: #333;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }
        .bulk-actions .btn-select:hover { background: #444; }
        .bulk-actions .btn-delete-selected {
            background: #e50914;
            color: #fff;
            border: none;
            padding: 8px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }
        .bulk-actions .btn-delete-selected:hover { background: #b20710; }
        .bulk-actions .btn-delete-selected:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .bulk-actions .selected-count {
            color: #8c8c8c;
            font-size: 14px;
        }
        
        .search-box {
            padding: 8px 15px;
            background: #333;
            border: 1px solid #444;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            width: 250px;
        }
        .search-box::placeholder { color: #8c8c8c; }
        .search-box:focus { outline: none; border-color: #e50914; }
        
        .alert {
            padding: 15px 25px;
            margin: 20px 50px;
            border-radius: 4px;
            font-weight: 600;
        }
        .alert-success {
            background: #1a3a2a;
            color: #46d369;
            border-left: 4px solid #46d369;
        }
        .alert-error {
            background: #3a1a1a;
            color: #e87c03;
            border-left: 4px solid #e87c03;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #8c8c8c;
        }
        .empty-state h2 { color: #fff; margin-bottom: 15px; }
        .empty-state .btn-add {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            background: #e50914;
            color: #fff;
            font-weight: 600;
            margin-top: 15px;
        }
        .empty-state .btn-add:hover { background: #f40612; }
        
        .filter-bar {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-bar select {
            padding: 8px 15px;
            background: #333;
            border: 1px solid #444;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }
        .filter-bar select:focus { outline: none; border-color: #e50914; }
        
        @media (max-width: 768px) {
            .remove-header { flex-direction: column; align-items: flex-start; gap: 15px; padding: 15px 20px; }
            .remove-header .actions { width: 100%; flex-wrap: wrap; }
            .recipe-grid { padding: 20px; grid-template-columns: 1fr; }
            .bulk-actions { flex-wrap: wrap; padding: 15px 20px; gap: 10px; }
            .search-box { width: 100%; }
            .filter-bar { width: 100%; }
            .filter-bar select { flex: 1; }
            .alert { margin: 15px 20px; }
        }
    </style>
</head>
<body>
    <!-- Custom navbar (not using navbar.php for admin context) -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">RECIPE<span>FLIX</span></a>
            <a href="index.php">Home</a>
            <a href="add_recipe.php">Add Recipe</a>
            <a href="remove_recipe.php" style="color:#e50914;">Remove Recipes</a>
        </div>
        <div class="nav-right">
            <span style="color:#e5e5e5;">Hi, Admin</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="remove-container">
        <!-- Header -->
        <div class="remove-header">
            <h1>🗑️ Manage Recipes</h1>
            <div class="actions">
                <a href="add_recipe.php" class="btn-add">+ Add New</a>
                <a href="index.php" class="btn-back">← Back to Site</a>
            </div>
        </div>

        <!-- Alerts -->
        <?php if(isset($success)): ?>
            <div class="alert alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(count($recipes) > 0): ?>
            <!-- Bulk Actions Bar -->
            <div class="bulk-actions">
                <div class="filter-bar">
                    <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search recipes..." onkeyup="filterRecipes()">
                    <select id="categoryFilter" onchange="filterRecipes()">
                        <option value="">All Categories</option>
                        <?php 
                        $cats = mysqli_query($link, "SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category != '' ORDER BY category");
                        if($cats) {
                            while($c = mysqli_fetch_assoc($cats)): 
                            ?>
                            <option value="<?= htmlspecialchars($c['category']) ?>">
                                <?= htmlspecialchars($c['category']) ?>
                            </option>
                            <?php endwhile; 
                        }
                        ?>
                    </select>
                    <select id="difficultyFilter" onchange="filterRecipes()">
                        <option value="">All Difficulties</option>
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <button class="btn-select" onclick="selectAll()">Select All</button>
                    <button class="btn-select" onclick="deselectAll()">Deselect All</button>
                    <span class="selected-count" id="selectedCount">0 selected</span>
                    <form method="POST" style="margin:0;" onsubmit="return confirmBulkDelete();">
                        <input type="hidden" name="delete_multiple" value="1">
                        <div id="selectedIds"></div>
                        <button type="submit" class="btn-delete-selected" id="bulkDeleteBtn" disabled>
                            Delete Selected
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recipe Grid -->
            <div class="recipe-grid" id="recipeGrid">
                <?php foreach($recipes as $recipe): ?>
                    <div class="recipe-card" 
                         data-title="<?php echo strtolower($recipe['title']); ?>"
                         data-category="<?php echo strtolower($recipe['category']); ?>"
                         data-difficulty="<?php echo strtolower($recipe['difficulty']); ?>">
                        <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($recipe['title']); ?>" 
                             onerror="this.src='img/placeholder.jpg'">
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <div class="meta">🍽 <span><?php echo htmlspecialchars($recipe['category']); ?></span></div>
                            <div class="meta">⏱ <span><?php echo htmlspecialchars($recipe['cooking_time']); ?></span></div>
                            <div class="meta">📊 <span><?php echo htmlspecialchars($recipe['difficulty']); ?></span></div>
                        </div>
                        <div class="card-actions">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" class="recipe-checkbox" value="<?php echo $recipe['id']; ?>" 
                                       onchange="updateSelected()">
                                <label>Select</label>
                            </div>
                            <a href="?delete=<?php echo $recipe['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete "<?php echo addslashes($recipe['title']); ?>"?')">
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <h2>No Recipes Found</h2>
                <p>You haven't added any recipes yet.</p>
                <a href="add_recipe.php" class="btn-add">Add Your First Recipe</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateSelected() {
            const checkboxes = document.querySelectorAll('.recipe-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            const input = document.getElementById('selectedIds');
            input.innerHTML = ids.map(id => `<input type="hidden" name="recipe_ids[]" value="${id}">`).join('');
            
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count + ' selected';
            document.getElementById('bulkDeleteBtn').disabled = count === 0;
        }

        function selectAll() {
            document.querySelectorAll('.recipe-checkbox').forEach(cb => cb.checked = true);
            updateSelected();
        }

        function deselectAll() {
            document.querySelectorAll('.recipe-checkbox').forEach(cb => cb.checked = false);
            updateSelected();
        }

        function confirmBulkDelete() {
            const count = document.querySelectorAll('.recipe-checkbox:checked').length;
            if(count === 0) return false;
            return confirm(`Are you sure you want to delete ${count} selected recipe(s)? This cannot be undone!`);
        }

        function filterRecipes() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const difficulty = document.getElementById('difficultyFilter').value.toLowerCase();
            
            const cards = document.querySelectorAll('.recipe-card');
            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                const cat = card.getAttribute('data-category');
                const diff = card.getAttribute('data-difficulty');
                
                let show = true;
                if(search && !title.includes(search)) show = false;
                if(category && !cat.includes(category)) show = false;
                if(difficulty && !diff.includes(difficulty)) show = false;
                
                card.style.display = show ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>