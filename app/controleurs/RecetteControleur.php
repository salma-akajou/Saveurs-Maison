<?php

require_once __DIR__ . '/../modeles/Recette.php';
require_once __DIR__ . '/../modeles/Ingredient.php';
require_once __DIR__ . '/../modeles/Etape.php';
require_once __DIR__ . '/../modeles/Avis.php';
require_once __DIR__ . '/../modeles/Favori.php';

/*
 * Contrôleur de la page de détail d'une recette.
 * Lit l'id dans l'URL (?id=...) et rassemble tout ce dont la vue a
 * besoin. Retourne null si la recette n'existe pas ou n'est pas publiée.
 */
class RecetteControleur
{
    public function afficher()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $recette = Recette::trouverPublieeParId($id);

        if ($recette === null) {
            return null;
        }

        $estFavori = false;

        if (isset($_SESSION['utilisateur_id'])) {
            $estFavori = Favori::estFavori((int) $_SESSION['utilisateur_id'], $id);
        }

        return [
            'recette' => $recette,
            'ingredients' => Ingredient::listerPourRecette($id),
            'etapes' => Etape::listerParRecette($id),
            'avis' => Avis::listerParRecette($id),
            'estFavori' => $estFavori,
        ];
    }
}
