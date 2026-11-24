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

        // Envoi du formulaire d'avis : seulement possible si connecté.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur_id'])) {
            $this->enregistrerAvis($id);
        }

        $recette = Recette::trouverPublieeParId($id);

        if ($recette === null) {
            return null;
        }

        $estFavori = false;
        $dejaDonneAvis = false;

        if (isset($_SESSION['utilisateur_id'])) {
            $idUtilisateur = (int) $_SESSION['utilisateur_id'];
            $estFavori = Favori::estFavori($idUtilisateur, $id);
            $dejaDonneAvis = Avis::aDejaDonneAvis($idUtilisateur, $id);
        }

        return [
            'recette' => $recette,
            'ingredients' => Ingredient::listerPourRecette($id),
            'etapes' => Etape::listerParRecette($id),
            'avis' => Avis::listerParRecette($id),
            'estFavori' => $estFavori,
            'dejaDonneAvis' => $dejaDonneAvis,
        ];
    }

    // Enregistre l'avis envoyé par le formulaire, puis redirige vers la
    // même page (évite qu'un rechargement renvoie le même avis deux fois).
    private function enregistrerAvis(int $idRecette)
    {
        $idUtilisateur = (int) $_SESSION['utilisateur_id'];
        $note = (int) ($_POST['note'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        $estValide = $note >= 1 && $note <= 5 && $commentaire !== '';

        if ($estValide && !Avis::aDejaDonneAvis($idUtilisateur, $idRecette)) {
            Avis::ajouter($idUtilisateur, $idRecette, $note, $commentaire);
        }

        header('Location: recette.php?id=' . $idRecette . '&avis=succes#avis');
        exit;
    }
}
