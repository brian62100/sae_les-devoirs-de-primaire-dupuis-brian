<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include '../config.php';  // Inclure la configuration de la base de données
    include '../header.php'; // Inclure le fichier d'en-tête


    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['connected']) || !$_SESSION['connected']) {
        header("Location: ../login.php");
        exit();
    }

    $type_utilisateur = $_SESSION['role']; // 'eleve', 'parent' ou 'professeur'
    // initialization du nombre de question et incrementation
	$_SESSION['nbQuestion'] = $_SESSION['nbQuestion'] + 1;
	$numQuestion = $_SESSION['nbQuestion'];

    // Charger les questions depuis un fichier externe
    $fichier = file("listeQuestions.txt");
    $total = count($fichier);
    $alea = mt_rand(0, $total - 1);
    $ligneFichier = explode(';', $fichier[$alea]);

    // Sélection du pronom sujet
    $numPronom = mb_substr($ligneFichier[0], 0, 1);
    if ($numPronom == "*") {
        $numPronom = mt_rand(1, 6);
        $sujets = ["Je", "Tu", ["Il", "Elle", "On"], "Nous", "Vous", ["Ils", "Elles"]];
        $sujet = is_array($sujets[$numPronom - 1]) ? $sujets[$numPronom - 1][array_rand($sujets[$numPronom - 1])] : $sujets[$numPronom - 1];
    } else {
        $sujet = mb_substr($ligneFichier[0], 1);
    }

    function conjugaison($fichier, $numPronom) {
        if (!file_exists($fichier)) {
            return "Erreur : fichier introuvable";
        }

        $conjugaisons = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        return isset($conjugaisons[$numPronom - 1]) ? trim($conjugaisons[$numPronom - 1]) : "Erreur : pronom invalide";
    }

    function supprime_caracteres_speciaux($chaine) {
        return preg_replace('/[^a-zA-ZÀ-ÿ\- ]/', '', $chaine);
    }

    // Récupérer tous les fichiers de verbes dans le dossier "verbes/"
    $listeVerbes = glob("verbes/*_present.txt");

    if (!$listeVerbes) {
        die("Erreur : Aucun fichier de verbe trouvé !");
    }

    // Choisir un fichier aléatoirement
    $fichierVerbe = $listeVerbes[array_rand($listeVerbes)];

    // Extraire le nom du verbe
    $verbe = basename($fichierVerbe, "_present.txt");

    // Récupérer la conjugaison correcte
    $bonneReponse = conjugaison($fichierVerbe, $numPronom);
    $bonneReponsescs = supprime_caracteres_speciaux($bonneReponse);

    // Gestion du "Je" -> "J'" pour les voyelles
    if ($sujet == "Je" && preg_match("/^[aeiou]/i", $bonneReponse)) {
        $sujet = "J'";
    }

    $finDePhrase = ".";

    if ($type_utilisateur === 'eleve'): 4;
        // Connexion à la base de données et insertion de la question
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insérer la question
            $stmt = $conn->prepare("INSERT INTO question (reponse, correcte, type_question, intitule) VALUES (:reponse, null, :type_question, :intitule)");
            $stmt->execute([
                'reponse' => $bonneReponsescs,
                'type_question' => "verbe_phrase",
                'intitule' => "$sujet $verbe ..."
            ]);

            // Récupérer l'ID de la question insérée
            $_SESSION['id_question'] = $conn->lastInsertId();
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    endif;
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Question</title>
</head>
<body style="background-color:grey;">
    <center>
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:1000px;height:430px;background-image:url('./images/NO.jpg');background-repeat:no-repeat;">
                    <center>
                        <h1>Question Numéro <?php echo $_SESSION['nbQuestion'] + 1; ?></h1><br />
                        <h3>Conjugue le verbe <strong><?php echo $verbe; ?></strong> pour compléter cette phrase.</h3>
                        <form action="./correction.php" method="post">
                            <input type="hidden" name="sujet" value="<?php echo $sujet; ?>">
                            <input type="hidden" name="correction" value="<?php echo $bonneReponse; ?>">
                            <input type="hidden" name="finDePhrase" value="<?php echo $finDePhrase; ?>">
                            <label for="mot"> <?php echo $sujet; ?>&nbsp;</label>
                            <input type="text" id="mot" name="mot" autocomplete="off" autofocus>
                            <label for="finDePhrase"> <?php echo $finDePhrase; ?>&nbsp;</label>
                            <br /><br />
                            <input type="submit" value="Valider">
                        </form>
                    </center>
                </td>
                <td style="width:280px;height:430px;background-image:url('./images/NE.jpg');background-repeat:no-repeat;"></td>
            </tr>
            <tr>
                <td style="width:1000px;height:323px;background-image:url('./images/SO.jpg');background-repeat:no-repeat;"></td>
                <td style="width:280px;height:323px;background-image:url('./images/SE.jpg');background-repeat:no-repeat;"></td>
            </tr>
        </table>
    </center>
    <br />
    <footer style="background-color: #45a1ff;">
        <center>
            Rémi Synave<br />
            Contact : remi . synave @ univ - littoral [.fr]<br />
            Crédits image : Image par <a href="https://pixabay.com/fr/users/Mimzy-19397/">Mimzy</a> de <a href="https://pixabay.com/fr/">Pixabay</a><br />
            Crédits voix : Denise de <a href="https://azure.microsoft.com/fr-fr/services/cognitive-services/text-to-speech/">Microsoft Azure</a>
        </center>
    </footer>
</body>
</html>
