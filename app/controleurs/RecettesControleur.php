<?php

require_once __DIR__ . '/../modeles/Recette.php';

/*
 * Contrôleur du catalogue des recettes.
 * Lit les filtres envoyés dans l'URL (recherche, catégorie, difficulté,
 * page), interroge le modèle, et retourne tout ce dont la vue a besoin
 * pour afficher les résultats.
 */
class RecettesControleur
{
    public function afficher()
    {
        $recherche = trim($_GET['recherche'] ?? '');
        $difficulte = $_GET['difficulte'] ?? '';

        // La catégorie vaut null si aucune n'est choisie ("Toutes").
        $idCategorie = (isset($_GET['categorie']) && $_GET['categorie'] !== '')
            ? (int) $_GET['categorie']
            : null;

        // Le numéro de page ne peut pas être inférieur à 1.
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = max(1, $page);

        $parPage = 6;
        $decalage = ($page - 1) * $parPage;

        $recettes = Recette::rechercherPubliees(
            $recherche,
            $idCategorie,
            $difficulte,
            $parPage,
            $decalage
        );

        $total = Recette::compterPubliees($recherche, $idCategorie, $difficulte);
        $nombrePages = max(1, (int) ceil($total / $parPage));

        return [
            'recettes' => $recettes,
            'total' => $total,
            'page' => $page,
            'nombrePages' => $nombrePages,
            'debut' => $decalage + 1,
            'fin' => min($decalage + $parPage, $total),
            'recherche' => $recherche,
            'idCategorie' => $idCategorie,
            'difficulte' => $difficulte,
        ];
    }
}
