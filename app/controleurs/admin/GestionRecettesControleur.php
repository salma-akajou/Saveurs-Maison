<?php

require_once __DIR__ . '/../../modeles/Recette.php';

/*
 * Contrôleur de la page "Recettes" de l'administration.
 * Page réservée aux administrateurs (vérifié dans la vue).
 */
class GestionRecettesControleur
{
    public function afficher()
    {
        $idAdmin = (int) $_SESSION['utilisateur_id'];

        // Suppression d'une recette : réservée à ses propres recettes
        // (comme un membre sur "Mes recettes" — même règle pour tous).
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
            $idRecette = (int) ($_POST['id_recette'] ?? 0);

            Recette::supprimerSiProprietaire($idRecette, $idAdmin);

            header('Location: recettes.php?action=supprimee');
            exit;
        }

        $recherche = trim($_GET['recherche'] ?? '');
        $statut = $_GET['statut'] ?? '';

        $idCategorie = (isset($_GET['categorie']) && $_GET['categorie'] !== '')
            ? (int) $_GET['categorie']
            : null;

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $parPage = 10;
        $decalage = ($page - 1) * $parPage;

        $recettes = Recette::rechercherPourAdmin($recherche, $statut, $idCategorie, $parPage, $decalage);
        $total = Recette::compterPourAdmin($recherche, $statut, $idCategorie);
        $nombrePages = max(1, (int) ceil($total / $parPage));

        return [
            'recettes' => $recettes,
            'total' => $total,
            'page' => $page,
            'nombrePages' => $nombrePages,
            'debut' => $total > 0 ? $decalage + 1 : 0,
            'fin' => min($decalage + $parPage, $total),
            'recherche' => $recherche,
            'statut' => $statut,
            'idCategorie' => $idCategorie,
            'idAdmin' => $idAdmin,
        ];
    }
}
