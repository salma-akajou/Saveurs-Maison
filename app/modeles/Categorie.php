<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité CATEGORIE.
 */
class Categorie
{
    // Retourne toutes les catégories (la plus récemment ajoutée en premier),
    // avec le nombre de recettes qui les utilisent (pour la page
    // "Catégories" de l'admin).
    public static function listerAvecNombreRecettes()
    {
        global $pdo;

        $sql = "SELECT
                    c.id_categorie,
                    c.nom,
                    COUNT(r.id_recette) AS nombre_recettes
                FROM CATEGORIE c
                LEFT JOIN RECETTE r ON r.id_categorie = c.id_categorie
                GROUP BY c.id_categorie, c.nom
                ORDER BY c.id_categorie DESC";

        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retourne toutes les catégories sous la forme id_categorie => nom,
    // triées par nom. Sert à remplir les listes déroulantes et les
    // filtres (catalogue public, formulaires de recette, filtre admin).
    public static function listerPourListeDeroulante()
    {
        global $pdo;

        $stmt = $pdo->query('SELECT id_categorie, nom FROM CATEGORIE ORDER BY nom');

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Indique si cet id correspond bien à une catégorie existante
    // (utilisé pour valider le formulaire d'une recette).
    public static function idExiste(int $id)
    {
        global $pdo;

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM CATEGORIE WHERE id_categorie = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Retourne une catégorie à partir de son id, ou null si elle n'existe pas.
    public static function trouverParId(int $id)
    {
        global $pdo;

        $stmt = $pdo->prepare('SELECT * FROM CATEGORIE WHERE id_categorie = ?');
        $stmt->execute([$id]);

        $categorie = $stmt->fetch(PDO::FETCH_ASSOC);

        return $categorie !== false ? $categorie : null;
    }

    // Indique si une catégorie porte déjà ce nom (insensible à la casse).
    // $idAExclure permet d'ignorer la catégorie elle-même lors d'une modification.
    public static function nomExiste(string $nom, ?int $idAExclure = null)
    {
        global $pdo;

        $sql = 'SELECT COUNT(*) FROM CATEGORIE WHERE LOWER(nom) = LOWER(?)';
        $parametres = [$nom];

        if ($idAExclure !== null) {
            $sql .= ' AND id_categorie != ?';
            $parametres[] = $idAExclure;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametres);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function creer(string $nom)
    {
        global $pdo;

        $stmt = $pdo->prepare('INSERT INTO CATEGORIE (nom) VALUES (?)');
        $stmt->execute([$nom]);
    }

    public static function modifier(int $id, string $nom)
    {
        global $pdo;

        $stmt = $pdo->prepare('UPDATE CATEGORIE SET nom = ? WHERE id_categorie = ?');
        $stmt->execute([$nom, $id]);
    }

    // Compte combien de recettes utilisent encore cette catégorie
    // (une catégorie utilisée ne peut pas être supprimée).
    public static function compterRecettes(int $id)
    {
        global $pdo;

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM RECETTE WHERE id_categorie = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn();
    }

    public static function supprimer(int $id)
    {
        global $pdo;

        $stmt = $pdo->prepare('DELETE FROM CATEGORIE WHERE id_categorie = ?');
        $stmt->execute([$id]);
    }
}
