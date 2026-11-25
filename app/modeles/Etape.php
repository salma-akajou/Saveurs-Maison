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

    public static function ajouter(int $idRecette, string $titre, string $description, int $numeroOrdre)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'INSERT INTO ETAPE (titre, description, numero_ordre, id_recette) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$titre, $description, $numeroOrdre, $idRecette]);
    }

    // Retire toutes les étapes actuellement enregistrées pour une recette
    // (utilisé avant de les réenregistrer, lors d'une modification).
    public static function supprimerPourRecette(int $idRecette)
    {
        global $pdo;

        $stmt = $pdo->prepare('DELETE FROM ETAPE WHERE id_recette = ?');
        $stmt->execute([$idRecette]);
    }
}
