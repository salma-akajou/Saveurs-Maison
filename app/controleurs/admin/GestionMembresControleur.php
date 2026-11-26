<?php

require_once __DIR__ . '/../../modeles/Utilisateur.php';

/*
 * Contrôleur de la page "Membres" de l'administration.
 * Page réservée aux administrateurs (vérifié dans la vue).
 */
class GestionMembresControleur
{
    public function afficher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterEnvoi();
        }

        $recherche = trim($_GET['recherche'] ?? '');
        $statut = $_GET['statut'] ?? '';

        return [
            'membres' => Utilisateur::rechercherMembres($recherche, $statut),
            'recherche' => $recherche,
            'statut' => $statut,
        ];
    }

    // Active ou désactive le compte d'un membre, puis redirige.
    private function traiterEnvoi()
    {
        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id_utilisateur'] ?? 0);

        if ($action === 'activer') {
            Utilisateur::definirStatutMembre($id, 'actif');
            header('Location: membres.php?action=activee');
            exit;
        }

        if ($action === 'desactiver') {
            Utilisateur::definirStatutMembre($id, 'desactive');
            header('Location: membres.php?action=desactivee');
            exit;
        }
    }
}
