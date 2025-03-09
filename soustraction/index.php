<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	include '../header.php'; // Inclure le fichier d'en-tête

	// Vérifie si l'utilisateur est connecté
	if (!isset($_SESSION['connected']) || !$_SESSION['connected']) {
		header("Location: ../login.php");  // Si l'utilisateur n'est pas connecté, redirige vers login.php
		exit();
	}

	// Initialisation des variables de session
	$_SESSION['nbMaxQuestions'] = $_SESSION['nbMaxQuestions'] ?? 10;
	$_SESSION['nbQuestion'] = 0;
	$_SESSION['nbBonneReponse'] = 0;
	$_SESSION['historique'] = "";
?>

<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Accueil</title>
	</head>
	<body style="background-color:grey;">
		<center>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:1000px;height:430px;background-image:url('./images/NO.jpg');background-repeat:no-repeat;">
						<center>

							<h1>Bonjour !</h1><br />
							<h2>Nous allons faire du calcul mental. Tu devras faire <?php echo ''.$_SESSION['nbMaxQuestions'].'' ?> calculs.</h2><br />
							<form action="./question.php" method="post">
								<input type="submit" value="Commencer">
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
				et Image par <a href="https://pixabay.com/fr/users/everesd_design-16482457/">everesd_design</a> de <a href="https://pixabay.com/fr/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=5213756">Pixabay</a> <br />
			</center>
		</footer>
	</body>
</html>
