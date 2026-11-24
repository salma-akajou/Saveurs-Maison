<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité INGREDIENT.
 */
class Ingredient
{
    // Retourne les ingrédients d'une recette (table d'association
    // RECETTE_INGREDIENT), avec leur quantité et leur unité.
    public static function listerPourRecette(int $idRecette)
    {
        global $pdo;

        $sql = "SELECT i.nom, ri.quantite, ri.unite
                FROM RECETTE_INGREDIENT ri
                JOIN INGREDIENT i ON i.id_ingredient = ri.id_ingredient
                WHERE ri.id_recette = ?
                ORDER BY ri.id_ingredient";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idRecette]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
