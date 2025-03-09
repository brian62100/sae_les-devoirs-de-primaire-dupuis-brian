<?php
session_start();
include 'config.php';
include 'header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Accès non autorisé.");
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté
$role = $_SESSION['role']; // Rôle de l'utilisateur

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';

    if (!empty($nom) && !empty($prenom)) {
        // Rechercher l'élève dans la table 'eleve'
        $stmt = $pdo->prepare("SELECT user_id FROM eleve WHERE nom = ? AND prenom = ?");
        $stmt->execute([$nom, $prenom]);
        $eleveData = $stmt->fetch();

        if ($eleveData) {
            $eleve_id = $eleveData['user_id'];

            if ($role == "parent") {
                // Vérifier si la relation existe déjà
                $stmt = $pdo->prepare("SELECT * FROM parenter WHERE user_id = ? AND id_parents = ?");
                $stmt->execute([$eleve_id, $user_id]);
                if (!$stmt->fetch()) {
                    // Ajouter la relation parent-enfant
                    $stmt = $pdo->prepare("INSERT INTO parenter (user_id, id_parents) VALUES (?, ?)");
                    $stmt->execute([$eleve_id, $user_id]);
                    echo "L'élève a été associé avec succès en tant qu'enfant.";
                } else {
                    echo "Cette relation existe déjà.";
                }
            } elseif ($role == "professeur") {
                // Vérifier si la relation existe déjà
                $stmt = $pdo->prepare("SELECT * FROM enseignant WHERE user_id = ? AND id_prof = ?");
                $stmt->execute([$eleve_id, $user_id]);
                if (!$stmt->fetch()) {
                    // Ajouter la relation professeur-élève
                    $stmt = $pdo->prepare("INSERT INTO enseignant (user_id, id_prof) VALUES (?, ?)");
                    $stmt->execute([$eleve_id, $user_id]);
                    echo "L'élève a été associé avec succès en tant qu'élève.";
                } else {
                    echo "Cette relation existe déjà.";
                }
            }
        } else {
            echo "Élève non trouvé.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Association Élève</title>
</head>
<body>

<form method="post">
    <label>Nom: <input type="text" name="nom" required></label><br>
    <label>Prénom: <input type="text" name="prenom" required></label><br>
    <button type="submit">Associer</button>
</form>

</body>
</html>
