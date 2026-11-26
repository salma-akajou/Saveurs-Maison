<?php

require_once __DIR__ . '/../../modeles/Avis.php';

/*
 * Contrôleur de la page "Avis" de l'administration.
 * Page réservée aux administrateurs (vérifié dans la vue).
 */
class GestionAvisControleur
{
    public function afficher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
            Avis::supprimer((int) ($_POST['id_avis'] ?? 0));

            header('Location: avis.php?action=supprimee');
            exit;
        }

        $recherche = trim($_GET['recherche'] ?? '');
        $note = (isset($_GET['note']) && $_GET['note'] !== '') ? (int) $_GET['note'] : null;

        return [
            'avis' => Avis::rechercherPourAdmin($recherche, $note),
            'recherche' => $recherche,
            'note' => $note,
        ];
    }
}
