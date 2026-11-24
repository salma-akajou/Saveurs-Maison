<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité RECETTE.
 * Rassemble les méthodes qui vont chercher des recettes en base.
 */
class Recette
{
    // Retourne les dernières recettes publiées, avec leur catégorie
    // et leur note moyenne (calculée à partir de la table AVIS).
    public static function dernieresPubliees($limite = 6)
    {
        global $pdo;

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.photo,
                    r.temps_preparation,
                    r.temps_cuisson,
                    r.difficulte,
                    c.nom AS categorie,
                    (SELECT AVG(a.note) FROM AVIS a WHERE a.id_recette = r.id_recette) AS note_moyenne
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie
                WHERE r.statut = 'publiee'
                ORDER BY r.date_creation DESC
                LIMIT ?";

        $stmt = $pdo->prepare($sql);

        // LIMIT attend un entier : on précise le type avec bindValue.
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Construit la partie "WHERE ..." commune à la recherche et au comptage,
    // selon les filtres reçus. Retourne le SQL et les valeurs à lier.
    private static function construireFiltres($recherche, $idCategorie, $difficulte)
    {
        $sql = " WHERE r.statut = 'publiee'";
        $parametres = [];

        if ($recherche !== '') {
            $sql .= " AND r.titre LIKE ?";
            $parametres[] = '%' . $recherche . '%';
        }

        if ($idCategorie !== null) {
            $sql .= " AND r.id_categorie = ?";
            $parametres[] = $idCategorie;
        }

        if ($difficulte !== '') {
            $sql .= " AND r.difficulte = ?";
            $parametres[] = $difficulte;
        }

        return [$sql, $parametres];
    }

    // Recherche les recettes publiées selon le nom, la catégorie et la
    // difficulté, triées et paginées pour le catalogue.
    public static function rechercherPubliees($recherche, $idCategorie, $difficulte, $tri, $limite, $decalage)
    {
        global $pdo;

        list($filtres, $parametres) = self::construireFiltres($recherche, $idCategorie, $difficulte);

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.photo,
                    r.temps_preparation,
                    r.temps_cuisson,
                    r.difficulte,
                    c.nom AS categorie,
                    (SELECT AVG(a.note) FROM AVIS a WHERE a.id_recette = r.id_recette) AS note_moyenne
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie"
                . $filtres;

        switch ($tri) {
            case 'note':
                $sql .= " ORDER BY note_moyenne DESC";
                break;

            case 'rapides':
                $sql .= " ORDER BY (r.temps_preparation + r.temps_cuisson) ASC";
                break;

            case 'nom':
                $sql .= " ORDER BY r.titre ASC";
                break;

            default:
                $sql .= " ORDER BY r.date_creation DESC";
                break;
        }

        $sql .= " LIMIT ? OFFSET ?";
        $parametres[] = $limite;
        $parametres[] = $decalage;

        $stmt = $pdo->prepare($sql);

        // Les entiers (id_categorie, LIMIT, OFFSET) doivent être liés en
        // PDO::PARAM_INT, sinon PDO les traite comme du texte.
        foreach ($parametres as $position => $valeur) {
            $type = is_int($valeur) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($position + 1, $valeur, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Compte le nombre total de recettes publiées correspondant aux mêmes
    // filtres, pour afficher le total et calculer le nombre de pages.
    public static function compterPubliees($recherche, $idCategorie, $difficulte)
    {
        global $pdo;

        list($filtres, $parametres) = self::construireFiltres($recherche, $idCategorie, $difficulte);

        $sql = "SELECT COUNT(*) FROM RECETTE r" . $filtres;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametres);

        return (int) $stmt->fetchColumn();
    }
}
