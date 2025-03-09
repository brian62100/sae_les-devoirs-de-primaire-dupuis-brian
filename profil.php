<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';  // Inclure la configuration de la base de données
include 'header.php'; // Inclure le fichier d'en-tête

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['connected']) || !$_SESSION['connected']) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$prenom = $_SESSION['prenom'];
$nom = $_SESSION['nom'];
$type_utilisateur = $_SESSION['role']; // 'eleve', 'parent' ou 'professeur'

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($type_utilisateur === 'eleve') {
        // Récupérer les moyennes par type d'exercice de l'élève
        $sql = "SELECT Q.type_question, AVG(Q.correcte) AS moyenne 
                FROM question Q
                JOIN faire F ON Q.id_question = F.id_question
                WHERE F.user_id = :user_id
                GROUP BY Q.type_question";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $moyennes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les derniers exercices réalisés de l'élève
        $sql = "SELECT Q.intitule, Q.reponse, Q.correcte, Q.type_question 
                FROM question Q
                JOIN faire F ON Q.id_question = F.id_question
                WHERE F.user_id = :user_id
                ORDER BY F.id_question DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($type_utilisateur === 'parent') {
        // Vérifie si un enfant est sélectionné dans l'URL
        if (isset($_GET['prenom']) && isset($_GET['nom'])) {
            $enfant_prenom = $_GET['prenom'];
            $enfant_nom = $_GET['nom'];

            // Récupérer les informations de l'enfant
            $sql = "SELECT * FROM eleve WHERE prenom = :prenom AND nom = :nom LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':prenom' => $enfant_prenom, ':nom' => $enfant_nom]);
            $enfant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($enfant) {
                // Afficher le profil de l'enfant
                $prenom_enfant = $enfant['prenom'];
                $nom_enfant = $enfant['nom'];
                $user_id_enfant = $enfant['user_id'];

                // Récupérer les moyennes de l'enfant
                $sql = "SELECT Q.type_question, AVG(Q.correcte) AS moyenne 
                        FROM question Q
                        JOIN faire F ON Q.id_question = F.id_question
                        WHERE F.user_id = :user_id_enfant
                        GROUP BY Q.type_question";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id_enfant' => $user_id_enfant]);
                $moyennes_enfant = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Récupérer les derniers exercices réalisés de l'enfant
                $sql = "SELECT Q.intitule, Q.reponse, Q.correcte, Q.type_question 
                        FROM question Q
                        JOIN faire F ON Q.id_question = F.id_question
                        WHERE F.user_id = :user_id_enfant
                        ORDER BY F.id_question DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id_enfant' => $user_id_enfant]);
                $reponses_enfant = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                echo "Enfant non trouvé.";
                exit();
            }

        } else {
            // Récupérer les enfants du parent
            $sql = "SELECT E.prenom, E.nom 
                    FROM eleve E
                    JOIN parenter P ON E.user_id = P.user_id
                    WHERE P.id_parents = :id_parents";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id_parents' => $user_id]);
            $enfants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    } elseif ($type_utilisateur === 'professeur') {

        if (isset($_GET['prenom']) && isset($_GET['nom'])) {
            $enfant_prenom = $_GET['prenom'];
            $enfant_nom = $_GET['nom'];

            // Récupérer les informations de l'enfant
            $sql = "SELECT * FROM eleve WHERE prenom = :prenom AND nom = :nom LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':prenom' => $enfant_prenom, ':nom' => $enfant_nom]);
            $enfant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($enfant) {
                // Afficher le profil de l'enfant
                $prenom_enfant = $enfant['prenom'];
                $nom_enfant = $enfant['nom'];
                $user_id_enfant = $enfant['user_id'];

                // Récupérer les moyennes de l'enfant
                $sql = "SELECT Q.type_question, AVG(Q.correcte) AS moyenne 
                        FROM question Q
                        JOIN faire F ON Q.id_question = F.id_question
                        WHERE F.user_id = :user_id_enfant
                        GROUP BY Q.type_question";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id_enfant' => $user_id_enfant]);
                $moyennes_enfant = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Récupérer les derniers exercices réalisés de l'enfant
                $sql = "SELECT Q.intitule, Q.reponse, Q.correcte, Q.type_question 
                        FROM question Q
                        JOIN faire F ON Q.id_question = F.id_question
                        WHERE F.user_id = :user_id_enfant
                        ORDER BY F.id_question DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id_enfant' => $user_id_enfant]);
                $reponses_enfant = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                echo "Enfant non trouvé.";
                exit();
            }

        } else {
            // Récupérer les élèves du professeur
            $sql = "SELECT EL.prenom, EL.nom 
                    FROM eleve EL
                    JOIN enseignant EN ON EL.user_id = EN.id_prof
                    WHERE EN.id_prof = :id_prof";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id_prof' => $user_id]);
            // Vérifiez si des élèves ont été récupérés
            if ($stmt->rowCount() > 0) {
                $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $eleves = [];  // Si aucun élève n'est trouvé, initialisez la variable comme un tableau vide.
            }

        }
    }
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
</head>
<body>
    <br>
    <br>
    <h1>Profil de <?php echo $prenom . ' ' . $nom; ?></h1>

    <?php if ($type_utilisateur === 'eleve'): ?>
        <h2>📊 Moyennes par type d'exercice</h2>
        <ul>
            <?php foreach ($moyennes as $moyenne): ?>
                <li><?php echo ucfirst($moyenne['type_question']) . " : " . round($moyenne['moyenne'] * 100, 2) . "% de réussite"; ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>📜 Derniers exercices réalisés</h2>
        <table border="1">
            <tr>
                <th>Intitulé</th>
                <th>Réponse</th>
                <th>Résultat</th>
                <th>Type de question</th>
            </tr>
            <?php foreach ($reponses as $reponse): ?>
                <tr>
                    <td><?php echo $reponse['intitule']; ?></td>
                    <td><?php echo $reponse['reponse']; ?></td>
                    <td><?php echo ($reponse['correcte'] == 1) ? "✅ Bon" : "❌ Faux"; ?></td>
                    <td><?php echo ucfirst($reponse['type_question']); ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php elseif ($type_utilisateur === 'parent' && !isset($enfant)): ?>
        <h2>👨‍👩‍👧‍👦 Mes enfants</h2>
        <ul>
            <?php foreach ($enfants as $enfant): ?>
                <a href="ajout_eleve.php">ajouter des enfants</a>
                <li><a href="profil.php?prenom=<?php echo urlencode($enfant['prenom']); ?>&nom=<?php echo urlencode($enfant['nom']); ?>"><?php echo $enfant['prenom'] . " " . $enfant['nom']; ?></a></li>
            <?php endforeach; ?>
        </ul>

    <?php elseif ($type_utilisateur === 'parent' && isset($enfant)): ?>
        <h2>📚 Profil de l'enfant :</h2>
        <p><strong>Nom Prenom :</strong> <?php echo $prenom_enfant . ' ' . $nom_enfant; ?></p>

        <h2>📊 Moyennes de l'enfant</h2>
        <ul>
            <?php foreach ($moyennes_enfant as $moyenne): ?>
                <li><?php echo ucfirst($moyenne['type_question']) . " : " . round($moyenne['moyenne'] * 100, 2) . "% de réussite"; ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>📜 Derniers exercices réalisés par l'enfant</h2>
        <table border="1">
            <tr>
                <th>Intitulé</th>
                <th>Réponse</th>
                <th>Résultat</th>
                <th>Type de question</th>
            </tr>
            <?php foreach ($reponses_enfant as $reponse): ?>
                <tr>
                    <td><?php echo $reponse['intitule']; ?></td>
                    <td><?php echo $reponse['reponse']; ?></td>
                    <td><?php echo ($reponse['correcte'] == 1) ? "✅ Bon" : "❌ Faux"; ?></td>
                    <td><?php echo ucfirst($reponse['type_question']); ?></td>
                </tr>
            <?php endforeach; ?>

        </table>



        <?php elseif ($type_utilisateur === 'professeur'): ?>
            <h2>🎓 Profil du professeur : <?php echo $prenom . ' ' . $nom; ?></h2>
            <p><strong>Type d'utilisateur :</strong> Professeur</p>
            
            <!-- Affichage des élèves du professeur -->
            <h2>👨‍👩‍👧‍👦 Mes élèves</h2>
            <ul>
            <a href="ajout_eleve.php">ajouter des eleves</a>
                <?php foreach ($eleves as $eleve): ?>
                    <li><a href="profil.php?prenom=<?php echo urlencode($eleve['prenom']); ?>&nom=<?php echo urlencode($eleve['nom']); ?>"><?php echo $eleve['prenom'] . " " . $eleve['nom']; ?></a></li>
                <?php endforeach; ?>
            </ul>

        <!-- Affichage d'un profil détaillé d'un élève (si un prénom et nom d'élève sont passés dans l'URL) -->
        <?php if (isset($enfant)): ?>
            <h2>📚 Profil de l'élève :</h2>
            <p><strong>Nom Prenom :</strong> <?php echo $prenom_enfant . ' ' . $nom_enfant; ?></p> <!-- Vous devrez peut-être récupérer cette info en fonction de votre base de données -->

            <h2>📊 Moyennes de l'élève</h2>
            <ul>
                <?php foreach ($moyennes_enfant as $moyenne): ?>
                    <li><?php echo ucfirst($moyenne['type_question']) . " : " . round($moyenne['moyenne'] * 100, 2) . "% de réussite"; ?></li>
                <?php endforeach; ?>
            </ul>

            <h2>📜 Derniers exercices réalisés par l'élève</h2>
            <table border="1">
                <tr>
                    <th>Intitulé</th>
                    <th>Réponse</th>
                    <th>Résultat</th>
                    <th>Type de question</th>
                </tr>
                <?php foreach ($reponses_enfant as $reponse): ?>
                    <tr>
                        <td><?php echo $reponse['intitule']; ?></td>
                        <td><?php echo $reponse['reponse']; ?></td>
                        <td><?php echo ($reponse['correcte'] == 1) ? "✅ Bon" : "❌ Faux"; ?></td>
                        <td><?php echo ucfirst($reponse['type_question']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>


    <?php endif; ?>
</body>
</html>
