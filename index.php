<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include 'header.php'; // Inclure le fichier d'en-tête
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
                <td style="width:1000px;height:430px;background-image:url('./images/NO.jpg');background-repeat:no-repeat; position: relative;">
                    <center>
                        <br>
                        <br>
                        <h1>Bonjour !</h1>
                        <h2>Que veux-tu faire ?</h2>
                        <table border="1" cellpadding="15" style="border-collapse:collapse;border: 15px solid #ff7700;background-color:#d6d6d6;">
                            <tr>
                                <td><center><a href="<?php echo isset($_SESSION['connected']) && $_SESSION['connected'] === true ? 'addition/index.php' : 'login.php'; ?>" style="color:black;font-weight:bold;text-decoration:none"><img src="./images/addition.png"><br />Addition</a></center></td>
                                <td><center><a href="<?php echo isset($_SESSION['connected']) && $_SESSION['connected'] === true ? 'soustraction/index.php' : 'login.php'; ?>" style="color:black;font-weight:bold;text-decoration:none"><img src="./images/soustraction.png"><br/>Soustraction</a></center></td>
                                <td><center><a href="<?php echo isset($_SESSION['connected']) && $_SESSION['connected'] === true ? 'multiplication/index.php' : 'login.php'; ?>" style="color:black;font-weight:bold;text-decoration:none"><img src="./images/multiplication.png"><br />Multiplication</a></center></td>
                            </tr>
                            <tr>
                                <td><center><a href="<?php echo isset($_SESSION['connected']) && $_SESSION['connected'] === true ? 'dictee/index.php' : 'login.php'; ?>" style="color:black;font-weight:bold;text-decoration:none"><img src="./images/dictee.png"><br />Dictée</a></center></td>
                                <td><center><a href="<?php echo isset($_SESSION['connected']) && $_SESSION['connected'] === true ? 'conjugaison_verbe/index.php' : 'login.php'; ?>" style="color:black;font-weight:bold;text-decoration:none"><img src="./images/conjugaison_verbe.png"><br />Conjugaison<br />de verbes</a></center></td>
                                <td><center><a href="<?php echo isset($_SESSION['connected']) && $_SESSION['connected'] === true ? 'conjugaison_phrase/index.php' : 'login.php'; ?>" style="color:black;font-weight:bold;text-decoration:none"><img src="./images/conjugaison_phrase.png"><br />Conjugaison<br />de phrases</a></center></td>
                            </tr>
                        </table>
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
