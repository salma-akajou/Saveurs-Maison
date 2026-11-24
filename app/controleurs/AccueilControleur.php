<?php

require_once __DIR__ . '/../modeles/Recette.php';
require_once __DIR__ . '/../modeles/Favori.php';

/*
 * Contrôleur de la page d'accueil.
 * Récupère les données nécessaires et les retourne à la page qui l'appelle.
 */
class AccueilControleur
{
    public function afficher()
    {
        $idsFavoris = [];

        if (isset($_SESSION['utilisateur_id'])) {
            $idsFavoris = Favori::listerIdsParUtilisateur((int) $_SESSION['utilisateur_id']);
        }

        return [
            'recettes' => Recette::dernieresPubliees(3),
            'idsFavoris' => $idsFavoris,
        ];
    }
}
