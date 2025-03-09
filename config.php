<?php
$host = 'localhost';
$dbname = 'projet_sae_maintenance_application';
$username = 'root'; // Change selon ton serveur
$password = ''; // Change selon ton serveur

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>