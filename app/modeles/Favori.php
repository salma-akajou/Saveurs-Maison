<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité FAVORI.
 * Table d'association entre un UTILISATEUR et une RECETTE.
 */
class Favori
{
    // Vrai si le membre a déjà mis cette recette en favori.
    public static function estFavori(int $idUtilisateur, int $idRecette)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'SELECT 1 FROM FAVORI WHERE id_utilisateur = ? AND id_recette = ?'
        );
        $stmt->execute([$idUtilisateur, $idRecette]);

        return $stmt->fetch() !== false;
    }

    public static function ajouter(int $idUtilisateur, int $idRecette)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'INSERT INTO FAVORI (id_utilisateur, id_recette) VALUES (?, ?)'
        );
        $stmt->execute([$idUtilisateur, $idRecette]);
    }

    public static function retirer(int $idUtilisateur, int $idRecette)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'DELETE FROM FAVORI WHERE id_utilisateur = ? AND id_recette = ?'
        );
        $stmt->execute([$idUtilisateur, $idRecette]);
    }

    // Retourne juste les id des recettes mises en favori par ce membre
    // (pratique pour savoir quels cœurs doivent apparaître déjà remplis).
    public static function listerIdsParUtilisateur(int $idUtilisateur)
    {
        global $pdo;

        $stmt = $pdo->prepare('SELECT id_recette FROM FAVORI WHERE id_utilisateur = ?');
        $stmt->execute([$idUtilisateur]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Retourne les recettes mises en favori par ce membre, avec leur
    // catégorie et leur note moyenne, les plus récemment ajoutées d'abord.
    public static function listerParUtilisateur(int $idUtilisateur)
    {
        global $pdo;

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.description,
                    r.photo,
                    r.temps_preparation,
                    r.temps_cuisson,
                    r.difficulte,
                    c.nom AS categorie,
                    (SELECT AVG(a.note) FROM AVIS a WHERE a.id_recette = r.id_recette) AS note_moyenne
                FROM FAVORI f
                JOIN RECETTE r ON r.id_recette = f.id_recette
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie
                WHERE f.id_utilisateur = ?
                ORDER BY f.date_ajout DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUtilisateur]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
