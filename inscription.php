<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $connected = isset($_SESSION['connected']) && $_SESSION['connected'];
    $nom_user = $connected ? $_SESSION['nom_user'] : '';
    include 'header.php'; // Inclure le fichier d'en-tête
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Accueil</title>
    <style>
        .top-bar {
            position: fixed;
            top: 10px;
            left: 50%;
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
</head>
<body style="background-color:grey;">


    <center>
        <br>
        <br>
        <h2>Inscription</h2>
        <form action="register.php" method="post">
            <label>Nom: <input type="text" name="nom" required></label><br>
            <label>Prénom: <input type="text" name="prenom" required></label><br>
            <label>Mot de passe: <input type="password" name="password" required></label><br>
            <label>Type de compte:
                <select name="role" id="role" required>
                    <option value="eleve">Elève</option>
                    <option value="parent">Parent</option>
                    <option value="professeur">Professeur</option>
                </select>
            </label><br>
            <div id="Enfants" style="display:none;">
                <label>Nom Prenom de Enfants (nom1 prenom1, nom2 prenom2): <input type="text" name="eleves"></label><br>
            </div>
            <div id="eleves" style="display:none;">
                <label>Nom Prenom de Elèves (nom1 prenom1, nom2 prenom2): <input type="text" name="eleves"></label><br>
            </div>
            <button type="submit">S'inscrire</button>
        </form>

        <script>
            document.getElementById('role').addEventListener('change', function() {
                var role = this.value;
                document.getElementById('Enfants').style.display = (role === 'parent') ? 'block' : 'none';
                document.getElementById('eleves').style.display = (role === 'professeur') ? 'block' : 'none';
            });
        </script>
    </center>
</body>
</html>
