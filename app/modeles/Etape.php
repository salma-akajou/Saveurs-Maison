<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité ETAPE.
 */
class Etape
{
    // Retourne les étapes d'une recette, dans l'ordre de préparation.
    public static function listerParRecette(int $idRecette)
    {
        global $pdo;

        $sql = "SELECT titre, description, numero_ordre
                FROM ETAPE
                WHERE id_recette = ?
                ORDER BY numero_ordre";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idRecette]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
