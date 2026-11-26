<?php

require_once __DIR__ . '/../config/connexion.php';

/*
 * Modèle de l'entité UTILISATEUR.
 */
class Utilisateur
{
    // Recherche un compte par son adresse e-mail (utilisé pour la connexion).
    public static function trouverParEmail(string $email)
    {
        global $pdo;

        $sql = "SELECT id_utilisateur, nom, prenom, email, mot_de_passe, role, statut_compte
                FROM UTILISATEUR
                WHERE email = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        return $utilisateur !== false ? $utilisateur : null;
    }

    // Vérifie si une adresse e-mail est déjà utilisée par un compte.
    public static function emailExiste(string $email)
    {
        global $pdo;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM UTILISATEUR WHERE email = ?");
        $stmt->execute([$email]);

        return $stmt->fetchColumn() > 0;
    }

    // Même vérification, mais en ignorant le compte lui-même (pour
    // modifier son profil sans se bloquer sur sa propre adresse).
    public static function emailExistePourUnAutreCompte(string $email, int $idUtilisateur)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM UTILISATEUR WHERE email = ? AND id_utilisateur != ?"
        );
        $stmt->execute([$email, $idUtilisateur]);

        return $stmt->fetchColumn() > 0;
    }

    public static function trouverParId(int $id)
    {
        global $pdo;

        $sql = "SELECT id_utilisateur, nom, prenom, email, mot_de_passe
                FROM UTILISATEUR
                WHERE id_utilisateur = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        return $utilisateur !== false ? $utilisateur : null;
    }

    public static function modifierInfos(int $id, string $nom, string $prenom, string $email)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "UPDATE UTILISATEUR SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?"
        );
        $stmt->execute([$nom, $prenom, $email, $id]);
    }

    // Le nouveau mot de passe est haché avant d'être enregistré,
    // comme à l'inscription.
    public static function modifierMotDePasse(int $id, string $nouveauMotDePasse)
    {
        global $pdo;

        $motDePasseHache = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE UTILISATEUR SET mot_de_passe = ? WHERE id_utilisateur = ?");
        $stmt->execute([$motDePasseHache, $id]);
    }

    // Crée un compte membre. Le mot de passe est haché avant d'être
    // enregistré : on ne stocke jamais un mot de passe en clair.
    public static function creerCompte(string $nom, string $prenom, string $email, string $motDePasse)
    {
        global $pdo;

        $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);

        $sql = "INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, role, statut_compte)
                VALUES (?, ?, ?, ?, 'membre', 'actif')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $motDePasseHache]);

        return (int) $pdo->lastInsertId();
    }

    // Nombre de membres (hors administrateurs), pour le tableau de bord.
    public static function compterMembres()
    {
        global $pdo;

        $stmt = $pdo->query("SELECT COUNT(*) FROM UTILISATEUR WHERE role = 'membre'");

        return (int) $stmt->fetchColumn();
    }

    // Recherche les membres (jamais les administrateurs) selon leur nom,
    // prénom, e-mail et statut, pour la page "Membres" de l'administration.
    public static function rechercherMembres(string $recherche, string $statut)
    {
        global $pdo;

        $sql = "SELECT id_utilisateur, nom, prenom, email, statut_compte, date_inscription
                FROM UTILISATEUR
                WHERE role = 'membre'";
        $parametres = [];

        if ($recherche !== '') {
            $sql .= " AND (CONCAT(prenom, ' ', nom) LIKE ? OR email LIKE ?)";
            $parametres[] = '%' . $recherche . '%';
            $parametres[] = '%' . $recherche . '%';
        }

        if ($statut !== '') {
            $sql .= ' AND statut_compte = ?';
            $parametres[] = $statut;
        }

        $sql .= ' ORDER BY date_inscription DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametres);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Active ou désactive un compte membre. "AND role = 'membre'" protège
    // les comptes administrateurs, même si l'id était falsifié.
    public static function definirStatutMembre(int $id, string $statut)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "UPDATE UTILISATEUR SET statut_compte = ? WHERE id_utilisateur = ? AND role = 'membre'"
        );
        $stmt->execute([$statut, $id]);
    }
}
