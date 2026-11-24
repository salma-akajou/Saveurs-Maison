<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité AVIS.
 */
class Avis
{
    // Retourne les avis d'une recette, du plus récent au plus ancien,
    // avec le prénom et le nom de leur auteur.
    public static function listerParRecette(int $idRecette)
    {
        global $pdo;

        $sql = "SELECT a.note, a.commentaire, a.date_avis, u.prenom, u.nom
                FROM AVIS a
                JOIN UTILISATEUR u ON u.id_utilisateur = a.id_utilisateur
                WHERE a.id_recette = ?
                ORDER BY a.date_avis DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idRecette]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
