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

	// Initialisation des variables pour la question
	$_SESSION['nbQuestion'] = $_SESSION['nbQuestion'] + 1;
	$nbQuestion = $_SESSION['nbQuestion'];
	

	// Génération d'une question aléatoire
	$nombre1 = rand(5000, 10000);
	$nombre2 = rand(5000, 10000);
	$operation = "$nombre1 + $nombre2";
	$reponse = $nombre1 + $nombre2;

	// Générer un ID de question pour la session
	$_SESSION['id_question'] = rand(1, 100);
	$type_utilisateur = $_SESSION['role']; // 'eleve', 'parent' ou 'professeur'


	if ($type_utilisateur === 'eleve'): 4;
		// Insérer la question dans la base de données
		try {
			$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "INSERT INTO question (reponse, correcte, type_question, intitule) VALUES (:reponse, NULL, 'addition', :intitule)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([
				'reponse' => $reponse,
				'intitule' => $operation
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
		<title>Question</title>
	</head>
	<body style="background-color:grey;">
		<center>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:1000px;height:430px;background-image:url('./images/NO.jpg');background-repeat:no-repeat;">
						<center>
							<h1>Question Numéro <?php echo $nbQuestion + 1; ?></h1><br />
							<h3>Combien fait le calcul suivant ?</h3>
							<h3><?php echo $operation . ' = ?'; ?></h3>
							<form action="correction.php" method="post">
								<label for="mot">Combien fait le calcul ci-dessus ? </label><br>
								<input type="text" id="mot" name="mot" autocomplete="off" autofocus><br /><br />
								<input type="submit" value="Valider">
							</form>
						</center>
					</td>
					<td style="width:280px;height:430px;background-image:url('./images/NE.jpg');background-repeat:no-repeat;"></td>
				</tr>
			</table>
		</center>
	</body>
</html>
