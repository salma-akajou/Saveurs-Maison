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

    // Retourne une recette à partir de son id, avec sa catégorie, son
    // auteur, sa note moyenne et son nombre d'avis. Visible dans deux cas
    // seulement : la recette est publiée (tout le monde peut la voir), ou
    // elle appartient au membre connecté (il peut voir l'aperçu de sa
    // propre recette même en attente ou refusée). Retourne null sinon.
    public static function trouverVisibleParId(int $id, ?int $idUtilisateur)
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
                    r.statut,
                    r.motif_refus,
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
                AND (r.statut = 'publiee' OR r.id_utilisateur = ?)";

        // Un visiteur non connecté n'a pas d'id : on met 0, qui ne
        // correspond jamais à un vrai membre, pour ne garder que la
        // condition "publiee" dans ce cas.
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $idUtilisateur ?? 0]);

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

    // Retourne une recette appartenant à ce membre (pour la modifier).
    // Retourne null si elle n'existe pas ou n'appartient pas à ce membre.
    public static function trouverParIdEtUtilisateur(int $id, int $idUtilisateur)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'SELECT * FROM RECETTE WHERE id_recette = ? AND id_utilisateur = ?'
        );
        $stmt->execute([$id, $idUtilisateur]);

        $recette = $stmt->fetch(PDO::FETCH_ASSOC);

        return $recette !== false ? $recette : null;
    }

    // Crée une nouvelle recette, en attente de validation par défaut.
    // Retourne l'id de la recette créée.
    public static function creer(array $donnees)
    {
        global $pdo;

        $sql = "INSERT INTO RECETTE
                    (titre, description, photo, nb_personnes, temps_preparation,
                     temps_cuisson, difficulte, id_categorie, id_utilisateur)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $donnees['titre'],
            $donnees['description'],
            $donnees['photo'],
            $donnees['nb_personnes'],
            $donnees['temps_preparation'],
            $donnees['temps_cuisson'],
            $donnees['difficulte'],
            $donnees['id_categorie'],
            $donnees['id_utilisateur'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    // Modifie une recette existante. Toute modification la repasse
    // "en attente" : elle doit être vérifiée de nouveau par l'administrateur.
    public static function modifier(int $idRecette, array $donnees)
    {
        global $pdo;

        $sql = "UPDATE RECETTE SET
                    titre = ?, description = ?, photo = ?, nb_personnes = ?,
                    temps_preparation = ?, temps_cuisson = ?, difficulte = ?,
                    id_categorie = ?, statut = 'en_attente', motif_refus = NULL,
                    date_modification = NOW()
                WHERE id_recette = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $donnees['titre'],
            $donnees['description'],
            $donnees['photo'],
            $donnees['nb_personnes'],
            $donnees['temps_preparation'],
            $donnees['temps_cuisson'],
            $donnees['difficulte'],
            $donnees['id_categorie'],
            $idRecette,
        ]);
    }

    // Retourne le nombre de recettes pour chaque statut, pour le
    // tableau de bord de l'administration.
    public static function compterParStatut()
    {
        global $pdo;

        $stmt = $pdo->query('SELECT statut, COUNT(*) AS total FROM RECETTE GROUP BY statut');

        // On part de 0 pour les 3 statuts, au cas où l'un d'eux n'a
        // encore aucune recette (la requête ne le renverrait pas).
        $compteurs = ['publiee' => 0, 'en_attente' => 0, 'refusee' => 0];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
            $compteurs[$ligne['statut']] = (int) $ligne['total'];
        }

        return $compteurs;
    }

    // Retourne les dernières recettes en attente, avec le nom de leur
    // auteur, pour le tableau "Recettes à vérifier" du tableau de bord.
    public static function listerEnAttenteRecentes(int $limite)
    {
        global $pdo;

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.photo,
                    r.date_creation,
                    u.nom AS auteur_nom,
                    u.prenom AS auteur_prenom
                FROM RECETTE r
                JOIN UTILISATEUR u ON u.id_utilisateur = r.id_utilisateur
                WHERE r.statut = 'en_attente'
                ORDER BY r.date_creation DESC
                LIMIT ?";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Construit la partie "WHERE ..." pour la liste de l'administration :
    // contrairement au catalogue public, tous les statuts sont visibles,
    // et le statut lui-même peut être un filtre.
    private static function construireFiltresAdmin(string $recherche, string $statut, ?int $idCategorie)
    {
        $sql = ' WHERE 1 = 1';
        $parametres = [];

        if ($recherche !== '') {
            $sql .= ' AND r.titre LIKE ?';
            $parametres[] = '%' . $recherche . '%';
        }

        if ($statut !== '') {
            $sql .= ' AND r.statut = ?';
            $parametres[] = $statut;
        }

        if ($idCategorie !== null) {
            $sql .= ' AND r.id_categorie = ?';
            $parametres[] = $idCategorie;
        }

        return [$sql, $parametres];
    }

    // Recherche les recettes pour la page "Recettes" de l'administration
    // (tous statuts), avec leur auteur, paginées.
    public static function rechercherPourAdmin(string $recherche, string $statut, ?int $idCategorie, int $limite, int $decalage)
    {
        global $pdo;

        list($filtres, $parametres) = self::construireFiltresAdmin($recherche, $statut, $idCategorie);

        $sql = "SELECT
                    r.id_recette,
                    r.titre,
                    r.photo,
                    r.statut,
                    r.date_creation,
                    r.id_utilisateur,
                    c.nom AS categorie,
                    u.nom AS auteur_nom,
                    u.prenom AS auteur_prenom
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie
                JOIN UTILISATEUR u ON u.id_utilisateur = r.id_utilisateur"
                . $filtres . "
                ORDER BY r.date_creation DESC
                LIMIT ? OFFSET ?";
        $parametres[] = $limite;
        $parametres[] = $decalage;

        $stmt = $pdo->prepare($sql);

        foreach ($parametres as $position => $valeur) {
            $type = is_int($valeur) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($position + 1, $valeur, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Compte le nombre total de recettes correspondant aux mêmes filtres,
    // pour la pagination de la page "Recettes" de l'administration.
    public static function compterPourAdmin(string $recherche, string $statut, ?int $idCategorie)
    {
        global $pdo;

        list($filtres, $parametres) = self::construireFiltresAdmin($recherche, $statut, $idCategorie);

        $sql = "SELECT COUNT(*) FROM RECETTE r" . $filtres;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametres);

        return (int) $stmt->fetchColumn();
    }

    // Retourne une recette avec son auteur, quel que soit son statut
    // (l'administration peut consulter n'importe quelle recette).
    // Retourne null si elle n'existe pas.
    public static function trouverParIdPourAdmin(int $id)
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
                    r.statut,
                    r.motif_refus,
                    r.date_creation,
                    c.nom AS categorie,
                    u.nom AS auteur_nom,
                    u.prenom AS auteur_prenom
                FROM RECETTE r
                JOIN CATEGORIE c ON c.id_categorie = r.id_categorie
                JOIN UTILISATEUR u ON u.id_utilisateur = r.id_utilisateur
                WHERE r.id_recette = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $recette = $stmt->fetch(PDO::FETCH_ASSOC);

        return $recette !== false ? $recette : null;
    }

    // Valide une recette en attente : elle devient publiée.
    public static function valider(int $idRecette, int $idValidateur)
    {
        global $pdo;

        $sql = "UPDATE RECETTE SET
                    statut = 'publiee',
                    motif_refus = NULL,
                    date_validation = NOW(),
                    id_validateur = ?
                WHERE id_recette = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idValidateur, $idRecette]);
    }

    // Refuse une recette en attente, avec un motif obligatoire.
    public static function refuser(int $idRecette, int $idValidateur, string $motifRefus)
    {
        global $pdo;

        $sql = "UPDATE RECETTE SET
                    statut = 'refusee',
                    motif_refus = ?,
                    date_validation = NOW(),
                    id_validateur = ?
                WHERE id_recette = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$motifRefus, $idValidateur, $idRecette]);
    }
}
