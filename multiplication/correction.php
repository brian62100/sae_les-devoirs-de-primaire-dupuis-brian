<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	include '../config.php';  // Inclure la configuration de la base de données
	include '../header.php'; // Inclure le fichier d'en-tête

	// Vérifier si l'ID de la question est dans la session
	$id_question = $_SESSION['id_question'] ?? null;
	if (!$id_question) {
		die("Erreur : Aucune question trouvée.");
	}

	$user_id = $_SESSION['user_id'] ?? null;
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
		$bonne_reponse = $question['reponse'];
	} catch (PDOException $e) {
		die("Erreur de base de données : " . $e->getMessage());
	}

	$reponse_utilisateur = $_POST['mot'] ?? '';  // Récupérer la réponse de l'utilisateur
	$reponse_utilisateur = str_replace(' ', '', $reponse_utilisateur);
	$bonne_reponse = str_replace(' ', '', $bonne_reponse);

	// Vérifier si la réponse est correcte
	if ((int) $reponse_utilisateur === (int) $bonne_reponse) {
		$correcte = 1;
	} else {
		$correcte = 0;
	}

	if ($type_utilisateur === 'eleve'): 4;
		// Mettre à jour la question avec le résultat correct ou incorrect
		try {
			$stmt = $conn->prepare("UPDATE question SET correcte = :correcte WHERE id_question = :id_question");
			$stmt->execute([
				'correcte' => $correcte,
				'id_question' => $id_question
			]);
		} catch (PDOException $e) {
			die("Erreur lors de la mise à jour de la question : " . $e->getMessage());
		}

		// Insérer la réponse de l'utilisateur dans la table 'faire'
		try {
			$stmt = $conn->prepare("INSERT INTO faire (user_id, id_question) VALUES (:user_id, :id_question)");
			$stmt->execute([
				'user_id' => $user_id,
				'id_question' => $id_question
			]);
		} catch (PDOException $e) {
			die("Erreur lors de l'enregistrement de la réponse de l'utilisateur : " . $e->getMessage());
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
								if($reponse_utilisateur == $_POST['correction']) {
									echo '<h1>Super '.$_SESSION['prenom'].' ! Bonne réponse.</h1>';
									$_SESSION['nbBonneReponse'] = $_SESSION['nbBonneReponse'] + 1;
									$_SESSION['historique'] = $_SESSION['historique'] . $_POST['operation'] . $_POST['correction'] . "\n";
								} else {
                                    echo '<h1>Oh non !</h1><br />';
									echo '<h2>La bonne réponse était : '.$_POST['operation'].$_POST['correction'].'.</h2>';
									$_SESSION['historique'] = $_SESSION['historique'] . '********' . $_POST['operation'] . $_POST['mot'] . ';' . $_POST['correction'] . "\n";
								}
								echo '<br />';
								if ($_SESSION['nbQuestion'] < $_SESSION['nbMaxQuestions']) {
									$questionMessage = $_SESSION['nbBonneReponse'] == 1 ? 'Tu as '.$_SESSION['nbBonneReponse'].' bonne réponse sur '.$_SESSION['nbQuestion'].' question.' : 'Tu as '.$_SESSION['nbBonneReponse'].' bonnes réponses sur '.$_SESSION['nbQuestion'].' questions.';
									echo $questionMessage;
								}
							?>
							<br /><br />
							<?php
								if ($_SESSION['nbQuestion'] < $_SESSION['nbMaxQuestions']) {
							?>
							<form action="./question.php" method="post">
								<input type="submit" value="Suite" autofocus>
							</form>
							<?php
								} else {
                            ?>
                                    <form action="./fin.php" method="post">
                                        <input type="submit" value="Suite" autofocus>
                                    </form>
                            <?php
                                }
							?>
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
				et Image par <a href="https://pixabay.com/fr/users/everesd_design-16482457/">everesd_design</a> de <a href="https://pixabay.com/fr/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=5213756">Pixabay</a> <br />
			</center>
		</footer>
	</body>
</html>
