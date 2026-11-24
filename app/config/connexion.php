<?php

/*
 * Connexion à la base de données avec PDO.
 * Ce fichier est inclus par les modèles qui ont besoin de la base.
 * Il crée une seule variable $pdo, réutilisée partout.
 */

$hote = "localhost";
$nomBase = "saveurs_maison";
$utilisateur = "root";
$motDePasse = "";

$dsn = "mysql:host=$hote;dbname=$nomBase;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $utilisateur, $motDePasse);

    // En mode exception : toute erreur SQL lève une PDOException,
    // au lieu d'échouer silencieusement.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
