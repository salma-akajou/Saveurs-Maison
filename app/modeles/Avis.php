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

    // Vrai si ce membre a déjà laissé un avis sur cette recette
    // (un seul avis autorisé par membre et par recette).
    public static function aDejaDonneAvis(int $idUtilisateur, int $idRecette)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'SELECT 1 FROM AVIS WHERE id_utilisateur = ? AND id_recette = ?'
        );
        $stmt->execute([$idUtilisateur, $idRecette]);

        return $stmt->fetch() !== false;
    }

    public static function ajouter(int $idUtilisateur, int $idRecette, int $note, string $commentaire)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            'INSERT INTO AVIS (note, commentaire, id_utilisateur, id_recette) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$note, $commentaire, $idUtilisateur, $idRecette]);
    }

    // Nombre total d'avis, pour le tableau de bord de l'administration.
    public static function compterTous()
    {
        global $pdo;

        $stmt = $pdo->query('SELECT COUNT(*) FROM AVIS');

        return (int) $stmt->fetchColumn();
    }

    // Derniers avis publiés, avec le membre et la recette concernés,
    // pour le tableau de bord de l'administration.
    public static function listerRecents(int $limite)
    {
        global $pdo;

        $sql = "SELECT
                    a.note,
                    a.commentaire,
                    u.nom AS membre_nom,
                    u.prenom AS membre_prenom,
                    r.titre AS recette_titre
                FROM AVIS a
                JOIN UTILISATEUR u ON u.id_utilisateur = a.id_utilisateur
                JOIN RECETTE r ON r.id_recette = a.id_recette
                ORDER BY a.date_avis DESC
                LIMIT ?";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recherche les avis selon le membre, la recette et la note, pour la
    // page "Avis" de l'administration (les plus récents en premier).
    public static function rechercherPourAdmin(string $recherche, ?int $note)
    {
        global $pdo;

        $sql = "SELECT
                    a.id_avis,
                    a.note,
                    a.commentaire,
                    a.date_avis,
                    u.nom AS membre_nom,
                    u.prenom AS membre_prenom,
                    r.id_recette,
                    r.titre AS recette_titre
                FROM AVIS a
                JOIN UTILISATEUR u ON u.id_utilisateur = a.id_utilisateur
                JOIN RECETTE r ON r.id_recette = a.id_recette
                WHERE 1 = 1";
        $parametres = [];

        if ($recherche !== '') {
            $sql .= " AND (CONCAT(u.prenom, ' ', u.nom) LIKE ? OR r.titre LIKE ?)";
            $parametres[] = '%' . $recherche . '%';
            $parametres[] = '%' . $recherche . '%';
        }

        if ($note !== null) {
            $sql .= ' AND a.note = ?';
            $parametres[] = $note;
        }

        $sql .= ' ORDER BY a.date_avis DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametres);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function supprimer(int $id)
    {
        global $pdo;

        $stmt = $pdo->prepare('DELETE FROM AVIS WHERE id_avis = ?');
        $stmt->execute([$id]);
    }
}
