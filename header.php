
<style>
        .top-bar {
            position: fixed;
            top: 10px;
            left: 25%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            background-color: rgba(255, 119, 0, 0.9);
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
        }
        .top-bar a {
            background-color: #ff7700;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
        }
        .user-greeting {
            color: white;
            font-weight: bold;
        }
</style>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<div class="top-bar">
    <?php if (isset($_SESSION['connected']) && $_SESSION['connected']): ?>
        <span class="user-greeting">Bonjour, <?php echo htmlspecialchars($_SESSION['nom']); ?> !</span>
        <a href="../logout.php">Déconnexion</a>
    <?php else: ?>
        <a href="../login.php">Login</a>
    <?php endif; ?>
    <a href="../profil.php">profil</a>
    <a href="../index.php">accueil</a>
</div>


