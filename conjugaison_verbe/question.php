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


    // Sélection aléatoire du temps (présent, futur, imparfait)
    $tempsList = ['present', 'futur', 'imparfait'];
    $temps = $tempsList[array_rand($tempsList)];

    // Chargement de la liste des verbes pour le temps choisi
    $fichierVerbes = "verbes/" . $temps . ".txt";

    if (!file_exists($fichierVerbes)) {
        die("Erreur : Le fichier des verbes ($fichierVerbes) est introuvable.");
    }

    $verbes = file($fichierVerbes, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $verbe = trim($verbes[array_rand($verbes)]); // Sélection aléatoire d'un verbe

    // Suppression des accents pour le nom du fichier
    $verbeSansAccent = strtr($verbe, [
        "à" => "a", "â" => "a", "é" => "e", "è" => "e", "ë" => "e", "ê" => "e",
        "î" => "i", "ï" => "i", "ô" => "o", "ö" => "o", "ù" => "u", "û" => "u",
        "ü" => "u", "ÿ" => "y", "ç" => "c"
    ]);

    // Génération du nom du fichier de conjugaison
    $nomFichier = "verbes/" . $verbeSansAccent . "_" . $temps . ".txt";

    // Vérification de l'existence du fichier
    if (!file_exists($nomFichier)) {
        die("Erreur : Le fichier de conjugaison pour '$verbe' au '$temps' ($nomFichier) est introuvable.");
    }

    // Chargement des conjugaisons
    $fichierVerbe = file($nomFichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($fichierVerbe === false || count($fichierVerbe) < 6) {
        die("Erreur : Impossible de charger les conjugaisons depuis '$nomFichier'.");
    }

    $responses = array_map('trim', $fichierVerbe);

    if ($type_utilisateur === 'eleve'): 4;
        // Connexion à la base de données et insertion de la question
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO question (reponse, correcte, type_question, intitule) VALUES (:reponse, null, :type_question, :intitule)");
            $stmt->execute([
                'reponse' => $responses[0],  // On enregistre la première conjugaison comme bonne réponse
                'type_question' => "verbe",
                'intitule' => "Conjugue le verbe $verbe au $temps"
            ]);

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
                        <h1>Question Numéro <?php echo $_SESSION['nbQuestion']; ?></h1><br />
                        <h3>Conjugue le verbe <strong><u><?php echo $verbe; ?></u></strong> au <?php echo ucfirst($temps); ?> :</h3>
                        <form action="./correction.php" method="post">
                            <input type="hidden" name="correction1" value="<?php echo $responses[0]; ?>">
                            <input type="hidden" name="correction2" value="<?php echo $responses[1]; ?>">
                            <input type="hidden" name="correction3" value="<?php echo $responses[2]; ?>">
                            <input type="hidden" name="correction4" value="<?php echo $responses[3]; ?>">
                            <input type="hidden" name="correction5" value="<?php echo $responses[4]; ?>">
                            <input type="hidden" name="correction6" value="<?php echo $responses[5]; ?>">

                            <table>
                                <tbody>
                                    <tr><td><label for="fname">Je/J' </label></td><td><input type="text" id="mot1" name="mot1" autocomplete="off" autofocus></td></tr>
                                    <tr><td><label for="fname">Tu </label></td><td><input type="text" id="mot2" name="mot2" autocomplete="off"></td></tr>
                                    <tr><td><label for="fname">Il/Elle/On&nbsp;&nbsp;</label></td><td><input type="text" id="mot3" name="mot3" autocomplete="off"></td></tr>
                                    <tr><td><label for="fname">Nous </label></td><td><input type="text" id="mot4" name="mot4" autocomplete="off"></td></tr>
                                    <tr><td><label for="fname">Vous </label></td><td><input type="text" id="mot5" name="mot5" autocomplete="off"></td></tr>
                                    <tr><td><label for="fname">Ils </label></td><td><input type="text" id="mot6" name="mot6" autocomplete="off"></td></tr>
                                </tbody>
                            </table>

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
            Crédits image : Image par <a href="https://pixabay.com/fr/users/Mimzy-19397/">Mimzy</a> de <a href="https://pixabay.com/fr/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=1576791">Pixabay</a><br />
            Crédits voix : Denise de <a href="https://azure.microsoft.com/fr-fr/services/cognitive-services/text-to-speech/">Microsoft Azure</a>
        </center>
    </footer>
</body>
</html>
