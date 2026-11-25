<?php

require_once __DIR__ . '/../../modeles/Recette.php';
require_once __DIR__ . '/../../modeles/Ingredient.php';
require_once __DIR__ . '/../../modeles/Etape.php';
require_once __DIR__ . '/../../modeles/Avis.php';
require_once __DIR__ . '/../../modeles/Favori.php';

/*
 * Contrôleur de la page de détail d'une recette.
 * Lit l'id dans l'URL (?id=...) et rassemble tout ce dont la vue a
 * besoin. Retourne null si la recette n'existe pas, ou si elle n'est
 * ni publiée ni possédée par le membre connecté.
 */
class RecetteControleur
{
    public function afficher()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $idUtilisateurConnecte = isset($_SESSION['utilisateur_id']) ? (int) $_SESSION['utilisateur_id'] : null;

        // Envoi du formulaire d'avis : seulement possible si connecté.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idUtilisateurConnecte !== null) {
            $this->enregistrerAvis($id);
        }

        $recette = Recette::trouverVisibleParId($id, $idUtilisateurConnecte);

        if ($recette === null) {
            return null;
        }

        // Aperçu : le membre voit sa propre recette pas encore publiée.
        // Dans ce cas, pas de favoris ni d'avis (elle n'est pas visible
        // par les autres, donc personne n'a encore pu l'évaluer).
        $estApercu = $recette['statut'] !== 'publiee';

        $estFavori = false;
        $dejaDonneAvis = false;

        if (!$estApercu && $idUtilisateurConnecte !== null) {
            $estFavori = Favori::estFavori($idUtilisateurConnecte, $id);
            $dejaDonneAvis = Avis::aDejaDonneAvis($idUtilisateurConnecte, $id);
        }

        return [
            'recette' => $recette,
            'ingredients' => Ingredient::listerPourRecette($id),
            'etapes' => Etape::listerParRecette($id),
            'avis' => $estApercu ? [] : Avis::listerParRecette($id),
            'estApercu' => $estApercu,
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
