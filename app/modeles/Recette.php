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
    public static function dernieresPubliees(int $limite = 3)
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

    // Retourne une recette publiée à partir de son id, avec sa catégorie,
    // son auteur, sa note moyenne et son nombre d'avis. Retourne null si
    // la recette n'existe pas ou n'est pas publiée (on ne montre jamais
    // une recette en attente ou refusée sur une page publique).
    public static function trouverPublieeParId(int $id)
    {
        global $pdo;

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.description,
                    r.photo,
                    r.nb_personnes,
                    r.temps_preparation,
                    r.temps_cuisson,
                    r.difficulte,
                    c.id_categorie,
                    c.nom AS categorie,
                    u.prenom AS auteur_prenom,
                    u.nom AS auteur_nom,
                    (SELECT AVG(a.note) FROM AVIS a WHERE a.id_recette = r.id_recette) AS note_moyenne,
                    (SELECT COUNT(*) FROM AVIS a WHERE a.id_recette = r.id_recette) AS nombre_avis
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie
                JOIN UTILISATEUR u ON u.id_utilisateur = r.id_utilisateur
                WHERE r.id_recette = ?
                AND r.statut = 'publiee'";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $recette = $stmt->fetch(PDO::FETCH_ASSOC);

        return $recette !== false ? $recette : null;
    }

    // Construit la partie "WHERE ..." commune à la recherche et au comptage,
    // selon les filtres reçus. Retourne le SQL et les valeurs à lier.
    private static function construireFiltres(string $recherche, ?int $idCategorie, string $difficulte)
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
    // difficulté, paginées pour le catalogue (les plus récentes d'abord).
    public static function rechercherPubliees(string $recherche, ?int $idCategorie, string $difficulte, int $limite, int $decalage)
    {
        global $pdo;

        list($filtres, $parametres) = self::construireFiltres($recherche, $idCategorie, $difficulte);

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
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie"
                . $filtres . "
                ORDER BY r.date_creation DESC
                LIMIT ? OFFSET ?";
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
    public static function compterPubliees(string $recherche, ?int $idCategorie, string $difficulte)
    {
        global $pdo;

        list($filtres, $parametres) = self::construireFiltres($recherche, $idCategorie, $difficulte);

        $sql = "SELECT COUNT(*) FROM RECETTE r" . $filtres;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametres);

        return (int) $stmt->fetchColumn();
    }

    // Retourne toutes les recettes d'un membre, quel que soit leur statut,
    // les plus récentes d'abord (pour la page "Mes recettes").
    public static function listerParUtilisateur(int $idUtilisateur)
    {
        global $pdo;

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.description,
                    r.photo,
                    r.statut,
                    r.motif_refus,
                    r.date_creation,
                    c.nom AS categorie
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie
                WHERE r.id_utilisateur = ?
                ORDER BY r.date_creation DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUtilisateur]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprime une recette, seulement si elle appartient bien à ce membre.
    public static function supprimerSiProprietaire(int $idRecette, int $idUtilisateur)
    {
        global $pdo;

        $stmt = $pdo->prepare('DELETE FROM RECETTE WHERE id_recette = ? AND id_utilisateur = ?');
        $stmt->execute([$idRecette, $idUtilisateur]);
    }
}
