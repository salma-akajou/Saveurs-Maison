<?php

require_once __DIR__ . '/../modeles/Recette.php';

/*
 * Contrôleur de la page d'accueil.
 * Récupère les données nécessaires puis affiche la vue.
 */
class AccueilControleur
{
    public function afficher()
    {
        $recettes = Recette::dernieresPubliees(6);

        require __DIR__ . '/../vues/public/accueil.php';
    }
}
