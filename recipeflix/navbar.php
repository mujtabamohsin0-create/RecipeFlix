<nav class="navbar">
    <div class="nav-left">
        <a href="index.php" class="logo">RECIPE<span>FLIX</span></a>
        <a href="index.php">Home</a>
        <a href="index.php?cat=Italian">Italian</a>
        <a href="index.php?cat=Indian">Indian</a>
        <a href="index.php?cat=Japanese">Japanese</a>
        <a href="index.php?cat=Dessert">Desserts</a>
        <?php if(isset($_SESSION['username']) && $_SESSION['username'] == 'admin'): ?>
            <a href="add_recipe.php" style="color:#46d369;">+ Add Recipe</a>
            <a href="remove_recipe.php" style="color:#e50914;">🗑 Manage</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <?php if(isset($_SESSION['username'])): ?>
            <span>Hi, <?php echo $_SESSION['username']; ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>
    </div>
</nav>