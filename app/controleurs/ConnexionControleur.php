<?php

require_once __DIR__ . '/../modeles/Utilisateur.php';

/*
 * Contrôleur de la connexion.
 * Affiche le formulaire, et traite son envoi (méthode POST) :
 * vérifie l'e-mail et le mot de passe, puis ouvre la session.
 */
class ConnexionControleur
{
    public function afficher()
    {
        $erreurs = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            $utilisateur = Utilisateur::trouverParEmail($email);

            if ($utilisateur === null || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
                $erreurs[] = 'Adresse e-mail ou mot de passe incorrect.';
            } elseif ($utilisateur['statut_compte'] !== 'actif') {
                $erreurs[] = 'Ce compte a été désactivé. Contactez l’administrateur.';
            } else {
                // On garde seulement ce qui est utile à l'affichage :
                // jamais le mot de passe en session.
                $_SESSION['utilisateur_id'] = $utilisateur['id_utilisateur'];
                $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];
                $_SESSION['utilisateur_role'] = $utilisateur['role'];

                // Nouvel identifiant de session après la connexion
                // (empêche la réutilisation d'un ancien identifiant).
                session_regenerate_id(true);

                header('Location: accueil.php');
                exit;
            }
        }

        return [
            'erreurs' => $erreurs,
            'email' => $email,
        ];
    }
}
