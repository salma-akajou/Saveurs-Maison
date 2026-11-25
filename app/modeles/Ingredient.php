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

    // Retourne l'id de l'ingrédient portant ce nom. S'il n'existe pas
    // encore dans la table INGREDIENT, il est créé.
    public static function trouverOuCreer(string $nom)
    {
        global $pdo;

        $stmt = $pdo->prepare('SELECT id_ingredient FROM INGREDIENT WHERE nom = ?');
        $stmt->execute([$nom]);
        $id = $stmt->fetchColumn();

        if ($id !== false) {
            return (int) $id;
        }

        $stmt = $pdo->prepare('INSERT INTO INGREDIENT (nom) VALUES (?)');
        $stmt->execute([$nom]);

        return (int) $pdo->lastInsertId();
    }

    // Associe un ingrédient à une recette, avec sa quantité et son unité
    // (toutes les deux facultatives).
    public static function associerARecette(int $idRecette, int $idIngredient, ?float $quantite, ?string $unite)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'INSERT INTO RECETTE_INGREDIENT (id_recette, id_ingredient, quantite, unite) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$idRecette, $idIngredient, $quantite, $unite]);
    }

    // Retire tous les ingrédients actuellement associés à une recette
    // (utilisé avant de les réenregistrer, lors d'une modification).
    public static function supprimerAssociationsPourRecette(int $idRecette)
    {
        global $pdo;

        $stmt = $pdo->prepare('DELETE FROM RECETTE_INGREDIENT WHERE id_recette = ?');
        $stmt->execute([$idRecette]);
    }
}
