<?php

require_once __DIR__ . '/../modeles/Favori.php';

/*
 * Contrôleur de la page "Mes favoris".
 * Cette page est réservée aux membres connectés (vérifié dans la vue).
 */
class FavorisControleur
{
    public function afficher()
    {
        $idUtilisateur = (int) $_SESSION['utilisateur_id'];

        return Favori::listerParUtilisateur($idUtilisateur);
    }
}
