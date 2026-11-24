<?php

/*
 * Ajoute ou retire une recette des favoris du membre connecté,
 * puis revient sur la page d'où le clic est parti.
 * Cette page n'affiche rien : elle traite la demande et redirige.
 */

session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

require_once __DIR__ . '/../../modeles/Favori.php';

$idUtilisateur = (int) $_SESSION['utilisateur_id'];
$idRecette = (int) ($_POST['id_recette'] ?? 0);
$retour = $_POST['retour'] ?? '';

// On n'accepte comme retour qu'une adresse du site (commence par "/" et
// pas par "//"), pour éviter d'être redirigé vers un site extérieur.
if ($retour === '' || strpos($retour, '/') !== 0 || strpos($retour, '//') === 0) {
    $retour = '../public/accueil.php';
}

if ($idRecette > 0) {
    if (Favori::estFavori($idUtilisateur, $idRecette)) {
        Favori::retirer($idUtilisateur, $idRecette);
    } else {
        Favori::ajouter($idUtilisateur, $idRecette);
    }
}

header('Location: ' . $retour);
exit;
