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

	// Charger la liste des verbes à dicter
	$fichier = file("listeDeMots/listeComplete.txt");
	$total = count($fichier);
	$alea = mt_rand(0, $total - 1);
	$ligneFichier = explode(';', trim($fichier[$alea]));

	$verbeCorrect = $ligneFichier[0]; // Réponse correcte
	$fichierSon = $ligneFichier[1]; // Nom du fichier audio
	$prenom = $_SESSION['prenom']; // Récupération du prénom de l'utilisateur
    if ($type_utilisateur === 'eleve'): 4;
        // Insérer la question dans la base de données
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sujet = "Sujet";

            $stmt = $pdo->prepare("INSERT INTO question (reponse, correcte, type_question, intitule) VALUES (:reponse, null, :type_question, :intitule)");
            $stmt->execute([
                'reponse' => $verbeCorrect, 
                'type_question' => "dictee",
                'intitule' => "$sujet $verbeCorrect ..." 
            ]);
            
        } catch (PDOException $e) {
            die("Erreur lors de l'enregistrement de la question : " . $e->getMessage());
        }
    endif;
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Dictée de Verbes</title>
</head>
<body style="background-color:grey;">
    <center>
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:1000px;height:430px;background-image:url('./images/NO.jpg');background-repeat:no-repeat;">
                    <center>
                        <h1>Dictée Numéro <?php echo $_SESSION['nbQuestion']; ?></h1><br />
                        <audio autoplay controls>
                            <source src="./sons/<?php echo $fichierSon; ?>" type="audio/mpeg">
                            Votre navigateur ne supporte pas l'audio. Passez à Firefox !
                        </audio>
                        <form action="./correction.php" method="post">
                            <input type="hidden" name="correction" value="<?php echo $verbeCorrect; ?>">
                            <input type="hidden" name="nomFichierSon" value="<?php echo $fichierSon; ?>">
                            <br />
                            <label for="mot">Qu'as-tu entendu ?</label><br>
                            <input type="text" id="mot" name="mot" autocomplete="off" autofocus><br /><br /><br />
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
            Crédits voix : Denise de <a href="https://azure.microsoft.com/fr-fr/services/cognitive-services/text-to-speech/">Microsoft Azure</a>
        </center>
    </footer>
</body>
</html>
