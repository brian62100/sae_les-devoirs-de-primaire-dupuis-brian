<?php
    @ob_start();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include '../config.php';  // Inclure la configuration de la base de données
    include '../header.php';   // Inclure le fichier d'en-tête


    // Vérifier si l'ID de la question est dans la session
    $id_question = $_SESSION['id_question'] ?? null;
    if (!$id_question) {
        die("Erreur : Aucune question trouvée.");
    }

    $user_id = $_SESSION['user_id'];
    $type_utilisateur = $_SESSION['role']; // 'eleve', 'parent' ou 'professeur'

    // Récupérer la dernière question ajoutée à la base de données
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->query("SELECT id_question, reponse FROM question ORDER BY id_question DESC LIMIT 1");
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$question) {
            die("Erreur : Aucune question trouvée dans la base de données.");
        }

        $id_question = $question['id_question'];
        $bonne_reponse = trim(strtolower($question['reponse'])); // Mise en minuscule pour la comparaison
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }

    $reponse_utilisateur = $_POST['mot'] ?? '';  // Récupérer la réponse de l'utilisateur
    $reponse_utilisateur = trim(strtolower($reponse_utilisateur)); // Mise en minuscule

    // Vérifier si la réponse est correcte
    $correcte = ($reponse_utilisateur === $bonne_reponse) ? 1 : 0;
    if ($type_utilisateur === 'eleve'): 4;
        // Mettre à jour la question avec le résultat correct ou incorrect
        try {
            $stmt = $conn->prepare("UPDATE question SET correcte = :correcte WHERE id_question = :id_question");
            $stmt->execute([
                'correcte' => $correcte,
                'id_question' => $id_question
            ]);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }

        // Insérer la réponse de l'utilisateur dans la table 'faire'
        try {
            $stmt = $conn->prepare("INSERT INTO faire (user_id, id_question) VALUES (:user_id, :id_question)");
            $stmt->execute([
                'user_id' => $user_id,
                'id_question' => $id_question
            ]);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    endif;
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Correction</title>
    </head>
    <body style="background-color:grey;">
        <center>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:1000px;height:430px;background-image:url('./images/NO.jpg');background-repeat:no-repeat;">
                        <center>
                            <?php
                                // Message de retour à l'utilisateur
                                if ($correcte) {
                                    echo "<h1>Super ".$_SESSION['prenom']." ! Bonne réponse.</h1>";
                                    $_SESSION['nbBonneReponse'] = $_SESSION['nbBonneReponse'] + 1;
                                    $_SESSION['historique'] = $_SESSION['historique'].''.$_POST['mot']."\n";
                                } else {
                                    echo '<h1>Oh non !</h1><br /><h2>Tu as écrit '.$_POST['mot'].'.</h2><h2>La bonne réponse était : '.$_POST['correction'].'.</h2>';
                                    $_SESSION['historique'] = $_SESSION['historique'].'********'.$_POST['mot'].';'.$_POST['correction']."\n";
                                }
                                echo '<br />';
                                
                                // Affichage des statistiques sur les réponses
                                if ($_SESSION['nbQuestion'] < $_SESSION['nbMaxQuestions']) {
                                    if ($_SESSION['nbQuestion'] == 1)
                                        echo 'Tu as '.$_SESSION['nbBonneReponse'].' bonne réponse sur '.$_SESSION['nbQuestion'].' question.';
                                    else {
                                        if ($_SESSION['nbBonneReponse'] > 1)
                                            echo 'Tu as '.$_SESSION['nbBonneReponse'].' bonnes réponses sur '.$_SESSION['nbQuestion'].' questions.';
                                        else
                                            echo 'Tu as '.$_SESSION['nbBonneReponse'].' bonne réponse sur '.$_SESSION['nbQuestion'].' questions.';
                                    }
                                }
                            ?>
                            <br /><br />
                            <?php
                                if ($_POST['mot'] == $_POST['correction']) {
                                    if ($_SESSION['nbQuestion'] < $_SESSION['nbMaxQuestions']) {
                            ?>
                            <!-- Cas où la réponse est correcte mais ce n'était pas la dernière question -->
                            <form action="./question.php" method="post">
                                <input type="submit" value="Suite" autofocus>
                            </form>
                            <?php
                                    } else {
                            ?>
                            <!-- Cas où la réponse est correcte et c'était la dernière question -->
                            <form action="./fin.php" method="post">
                                <input type="submit" value="Suite" autofocus>
                            </form>
                            <?php
                                    }
                                } else {
                            ?>
                            <!-- Cas où la réponse n'était pas correcte -->
                            <form action="./recopie.php" method="post">
                                <input type="hidden" name="recopie" value=""></input>
                                <input type="hidden" name="correction" value="<?php echo "".$_POST['correction']."" ?>"></input>
                                <input type="submit" value="Suite" autofocus>
                            </form>
                            <?php
                                }
                            ?>
                            <br /><br />
                            <form action="./raz.php" method="post">
                                <input type="submit" value="Tout recommencer">
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
                Crédits image : Image par <a href="https://pixabay.com/fr/users/Mimzy-19397/">Mimzy</a> de <a href="https://pixabay.com/fr/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=1576791">Pixabay</a> <br />
                Crédits voix : Denise de <a href="https://azure.microsoft.com/fr-fr/services/cognitive-services/text-to-speech/">Microsoft Azure</a>
            </center>
        </footer>
    </body>
</html>
