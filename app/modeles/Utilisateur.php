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
}
