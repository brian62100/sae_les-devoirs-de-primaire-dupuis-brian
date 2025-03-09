<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include 'config.php';
    include 'header.php'; // Inclure le fichier d'en-tête

$message = ""; // Variable pour stocker les messages d'erreur ou de succès

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nu = isset($_POST['name']) ? trim($_POST['name']) : '';
    $mdp = isset($_POST['mdp']) ? trim($_POST['mdp']) : '';

    if (empty($nu) || empty($mdp)) {
        $message = "<p class='error'>Veuillez remplir tous les champs.</p>";
    } else {
        // Vérifier dans la table eleve
        $stmt = $pdo->prepare("SELECT * FROM eleve WHERE nom = :nom LIMIT 1");
        $stmt->execute(['nom' => $nu]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['password']) && password_verify($mdp, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['connected'] = true;
            $_SESSION['nom'] = $user['nom']; 
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = 'eleve';  // Définit le rôle

            header("Location: index.php");  // Redirige vers la page d'accueil
            exit(); 
            
        }

        // Vérifier dans la table professeurs
        $stmt = $pdo->prepare("SELECT * FROM professeurs WHERE nom = :nom LIMIT 1");
        $stmt->execute(['nom' => $nu]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['password']) && password_verify($mdp, $user['password'])) {
            $_SESSION['user_id'] = $user['id_prof'];
            $_SESSION['connected'] = true;
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = 'professeur'; 

            header("Location: index.php");
            exit();
        }

        // Vérifier dans la table parents
        $stmt = $pdo->prepare("SELECT * FROM parents WHERE nom = :nom LIMIT 1");
        $stmt->execute(['nom' => $nu]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['password']) && password_verify($mdp, $user['password'])) {
            $_SESSION['user_id'] = $user['id_parents'];
            $_SESSION['connected'] = true;
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = 'parent'; 

            header("Location: index.php");
            exit();
        }

        // Si aucune correspondance
        $message = "<p class='error'>Nom ou mot de passe incorrect.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <br>
    <div class="container">
        <h2>Connexion</h2>
        <?= $message; ?>
        <form action="" method="POST">
            <input type="text" name="name" placeholder="Nom de famille" required>
            <input type="password" name="mdp" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p><a href="inscription.php">Créer un compte</a></p>
    </div>
</body>
</html>
