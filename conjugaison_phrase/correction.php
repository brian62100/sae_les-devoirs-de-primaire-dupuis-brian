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

	$type_utilisateur = $_SESSION['role']; // 'eleve', 'parent' ou 'professeur'
	$user_id = $_SESSION['user_id'];

	if ($type_utilisateur === 'eleve'): 4;
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
	endif;

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
									echo "<h2>Bravo ! Votre réponse est correcte.</h2>";
								} else {
									echo "<h2>Dommage ! La bonne réponse était : <strong>$bonne_reponse</strong></h2>";
								}

								// Lien pour la question suivante
								echo '<br><a href="question.php">Nouvelle Question</a>';
							?>
							<form action="./question.php" method="post">
								<input type="submit" value="Suite" autofocus>
							</form>
						</center>
					</td>
					<td style="width:280px;height:430px;background-image:url('./images/NE.jpg');background-repeat:no-repeat;"></td>
				</tr>
			</table>
		</center>
	</body>
</html>
