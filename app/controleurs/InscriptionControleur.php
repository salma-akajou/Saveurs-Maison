<?php

require_once __DIR__ . '/../modeles/Utilisateur.php';

/*
 * Contrôleur de l'inscription.
 * Affiche le formulaire, et traite son envoi (méthode POST) :
 * validation des champs, création du compte, puis connexion
 * automatique et redirection vers l'accueil.
 */
class InscriptionControleur
{
    public function afficher()
    {
        $erreurs = [];
        $valeurs = ['nom' => '', 'prenom' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $confirmation = $_POST['confirmation'] ?? '';

            // On garde les valeurs saisies pour réafficher le formulaire
            // en cas d'erreur (sauf les mots de passe, jamais renvoyés).
            $valeurs = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email];

            if ($nom === '' || $prenom === '') {
                $erreurs[] = 'Le nom et le prénom sont obligatoires.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'L’adresse e-mail n’est pas valide.';
            } elseif (Utilisateur::emailExiste($email)) {
                $erreurs[] = 'Un compte existe déjà avec cette adresse e-mail.';
            }

            if (strlen($motDePasse) < 8) {
                $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            } elseif ($motDePasse !== $confirmation) {
                $erreurs[] = 'Les mots de passe ne correspondent pas.';
            }

            if (empty($erreurs)) {
                $idUtilisateur = Utilisateur::creerCompte($nom, $prenom, $email, $motDePasse);

                // Connexion automatique : pas besoin de ressaisir
                // son mot de passe juste après avoir créé le compte.
                $_SESSION['utilisateur_id'] = $idUtilisateur;
                $_SESSION['utilisateur_prenom'] = $prenom;
                $_SESSION['utilisateur_role'] = 'membre';
                session_regenerate_id(true);

                header('Location: accueil.php?bienvenue=1');
                exit;
            }
        }

        return [
            'erreurs' => $erreurs,
            'valeurs' => $valeurs,
        ];
    }
}
