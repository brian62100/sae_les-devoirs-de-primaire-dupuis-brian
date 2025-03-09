<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	include '../config.php';
	include '../header.php';

	if (!isset($_SESSION['connected']) || !$_SESSION['connected']) {
		header("Location: ../login.php");
		exit();
	}

	$type_utilisateur = $_SESSION['role']; // 'eleve', 'parent' ou 'professeur'
	// initialization du nombre de question et incrementation
	$_SESSION['nbQuestion'] = $_SESSION['nbQuestion'] + 1;
	$numQuestion = $_SESSION['nbQuestion'];

	$nbGauche = mt_rand(1000, 10000);
	$nbDroite = mt_rand(11, 99);
	$operation = $nbGauche . ' x ' . $nbDroite;
	$reponse = $nbGauche * $nbDroite;

	if ($type_utilisateur === 'eleve'): 4;
		try {
			$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$stmt = $conn->prepare("INSERT INTO question (reponse, correcte, type_question, intitule) VALUES (:reponse, NULL, :type_question, :intitule)");
			$stmt->execute([
				'reponse' => $reponse,           
				'type_question' => "multiplication", 
				'intitule' => $operation
			]);

		} catch (PDOException $e) {
			die("Error inserting question: " . $e->getMessage());
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
							<h1>Question Numéro <?php echo $numQuestion; ?></h1><br />
							<h3>Combien fait le calcul suivant ?</h3>
							<h3><?php echo $operation . ' = ?'; ?></h3>
							<form action="./correction.php" method="post">
								<input type="hidden" name="operation" value="<?php echo $operation . ' = ' ?>">
								<input type="hidden" name="correction" value="<?php echo $reponse; ?>">
								<br />
								<label for="fname">Combien fait le calcul ci-dessus ? </label><br>
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
				Contact : remi.synave@univ-littoral.fr<br />
				Crédits image : Image par <a href="https://pixabay.com/fr/users/Mimzy-19397/">Mimzy</a> de <a href="https://pixabay.com/fr/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=1576791">Pixabay</a> <br />
				Crédits voix : Denise de <a href="https://azure.microsoft.com/fr-fr/services/cognitive-services/text-to-speech/">Microsoft Azure</a>
			</center>
		</footer>
	</body>
</html>
