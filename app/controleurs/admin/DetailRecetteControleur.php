<?php

require_once __DIR__ . '/../../modeles/Recette.php';
require_once __DIR__ . '/../../modeles/Ingredient.php';
require_once __DIR__ . '/../../modeles/Etape.php';

/*
 * Contrôleur de la page "Détail de la recette" (administration).
 * Permet de consulter n'importe quelle recette, et de la valider ou
 * la refuser si elle est en attente. Page réservée aux administrateurs
 * (vérifié dans la vue). Retourne null si la recette n'existe pas.
 */
class DetailRecetteControleur
{
    public function afficher()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $erreur = $this->traiterDecision($id);
        }

        $recette = Recette::trouverParIdPourAdmin($id);

        if ($recette === null) {
            return null;
        }

        return [
            'recette' => $recette,
            'ingredients' => Ingredient::listerPourRecette($id),
            'etapes' => Etape::listerParRecette($id),
            'erreur' => $erreur,
        ];
    }

    // Valide ou refuse la recette, puis redirige vers la même page
    // (évite qu'un rechargement renvoie deux fois la même décision).
    private function traiterDecision(int $idRecette)
    {
        $idAdmin = (int) $_SESSION['utilisateur_id'];
        $action = $_POST['action'] ?? '';

        if ($action === 'valider') {
            Recette::valider($idRecette, $idAdmin);
            header('Location: recettes.php?action=validee');
            exit;
        }

        if ($action === 'refuser') {
            $motifRefus = trim($_POST['motif_refus'] ?? '');

            if ($motifRefus === '') {
                return 'Le motif du refus est obligatoire.';
            }

            Recette::refuser($idRecette, $idAdmin, $motifRefus);
            header('Location: recettes.php?action=refusee');
            exit;
        }

        return null;
    }
}
