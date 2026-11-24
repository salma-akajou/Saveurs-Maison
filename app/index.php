<?php

/*
 * Point d'entrée unique de l'application.
 * La page demandée est choisie avec le paramètre "page" dans l'URL,
 * par exemple : index.php?page=recettes
 * Chaque page : on appelle son contrôleur, puis on affiche sa vue.
 */

$page = $_GET['page'] ?? 'accueil';

switch ($page) {

    case 'accueil':
        require __DIR__ . '/controleurs/AccueilControleur.php';

        $controleur = new AccueilControleur();
        $recettes = $controleur->afficher();

        require __DIR__ . '/vues/public/accueil.php';
        break;

    default:
        http_response_code(404);
        echo "Page introuvable.";
        break;
}
