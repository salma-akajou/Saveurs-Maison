<?php

require_once __DIR__ . '/../modeles/Recette.php';

/*
 * Contrôleur de la page d'accueil.
 * Récupère les données nécessaires et les retourne à la page qui l'appelle.
 */
class AccueilControleur
{
    public function afficher()
    {
        return Recette::dernieresPubliees(6);
    }
}
