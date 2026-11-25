<?php

require_once __DIR__ . '/../modeles/Recette.php';

/*
 * Contrôleur de la page "Mes recettes".
 * Cette page est réservée aux membres connectés (vérifié dans la vue).
 */
class MesRecettesControleur
{
    public function afficher()
    {
        $idUtilisateur = (int) $_SESSION['utilisateur_id'];

        // Suppression d'une recette, envoyée par le bouton "Supprimer".
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
            $idRecette = (int) ($_POST['id_recette'] ?? 0);

            Recette::supprimerSiProprietaire($idRecette, $idUtilisateur);

            // Redirige vers la même page pour éviter de supprimer deux
            // fois la même recette si la page est rechargée.
            header('Location: mes-recettes.php?action=supprimee');
            exit;
        }

        return Recette::listerParUtilisateur($idUtilisateur);
    }
}
