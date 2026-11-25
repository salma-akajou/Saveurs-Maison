<?php

require_once __DIR__ . '/../modeles/Recette.php';
require_once __DIR__ . '/../modeles/Utilisateur.php';
require_once __DIR__ . '/../modeles/Avis.php';

/*
 * Contrôleur du tableau de bord de l'administration.
 * Page réservée aux administrateurs (vérifié dans la vue).
 */
class TableauDeBordControleur
{
    public function afficher()
    {
        $recettesParStatut = Recette::compterParStatut();
        $totalRecettes = array_sum($recettesParStatut);

        return [
            'recettesParStatut' => $recettesParStatut,
            'totalRecettes' => $totalRecettes,
            'totalMembres' => Utilisateur::compterMembres(),
            'totalAvis' => Avis::compterTous(),
            'recettesEnAttente' => Recette::listerEnAttenteRecentes(3),
            'derniersAvis' => Avis::listerRecents(3),
        ];
    }
}
