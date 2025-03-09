<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include 'config.php'; // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Sécurisation du mot de passe
    $role = $_POST['role'];
    $eleves = isset($_POST['eleves']) ? trim($_POST['eleves']) : '';

    try {
        if ($role == "eleve") {
            // Insérer l'élève dans la base de données
            $stmt = $pdo->prepare("INSERT INTO eleve (nom, prenom, password) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $prenom, $password]);

        } elseif ($role == "parent") {
            // Insérer le parent dans la base de données
            $stmt = $pdo->prepare("INSERT INTO parents (nom, prenom, password) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $prenom, $password]);
            $parent_id = $pdo->lastInsertId(); // Récupérer l'ID du parent

            if (!empty($eleves)) {
                // Séparer les élèves par une virgule
                $eleveNames = explode(",", $eleves);
                foreach ($eleveNames as $eleve) {
                    $eleve = trim($eleve);

                    // Chercher l'élève dans la table 'eleve' avec la concaténation du nom et prénom
                    $stmt = $pdo->prepare("SELECT user_id FROM eleve WHERE CONCAT(nom, ' ', prenom) = ?");
                    $stmt->execute([$eleve]);
                    $eleveData = $stmt->fetch();

                    if ($eleveData) {
                        // Lier l'élève avec le parent
                        $stmt = $pdo->prepare("INSERT INTO parenter (user_id, id_parents) VALUES (?, ?)");
                        $stmt->execute([$eleveData['user_id'], $parent_id]);
                    }
                }
            }

        } elseif ($role == "professeur") {
            // Insérer le professeur dans la base de données
            $stmt = $pdo->prepare("INSERT INTO professeurs (nom, prenom, password) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $prenom, $password]);
            $prof_id = $pdo->lastInsertId(); // Récupérer l'ID du professeur

            if (!empty($eleves)) {
                // Séparer les élèves par une virgule
                $eleveNames = explode(",", $eleves);
                foreach ($eleveNames as $eleve) {
                    $eleve = trim($eleve);

                    // Chercher l'élève dans la table 'eleve' avec la concaténation du nom et prénom
                    $stmt = $pdo->prepare("SELECT user_id FROM eleve WHERE CONCAT(nom, ' ', prenom) = ?");
                    $stmt->execute([$eleve]);
                    $eleveData = $stmt->fetch();

                    if ($eleveData) {
                        // Lier l'élève avec le professeur
                        $stmt = $pdo->prepare("INSERT INTO enseignant (user_id, id_prof) VALUES (?, ?)");
                        $stmt->execute([$eleveData['user_id'], $prof_id]);
                    }
                }
            }
        }

        // Message de succès
        $_SESSION['message'] = "Inscription réussie ! Connectez-vous.";
        header("Location: Login.php");
        exit();

    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}
?>
