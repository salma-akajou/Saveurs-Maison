<?php

require_once __DIR__ . '/../../modeles/Utilisateur.php';

/*
 * Contrôleur de la page "Mon profil".
 * Page réservée aux membres connectés (vérifié dans la vue).
 * Deux formulaires indépendants sur la même page : les informations
 * (nom, prénom, e-mail) et le mot de passe. Un champ caché "formulaire"
 * permet de savoir lequel a été envoyé.
 */
class ProfilControleur
{
    public function afficher()
    {
        $idUtilisateur = (int) $_SESSION['utilisateur_id'];

        $erreursInfos = [];
        $erreursMotDePasse = [];
        $succesInfos = false;
        $succesMotDePasse = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formulaire = $_POST['formulaire'] ?? '';

            if ($formulaire === 'infos') {
                $erreursInfos = $this->traiterInfos($idUtilisateur);
                $succesInfos = empty($erreursInfos);
            } elseif ($formulaire === 'mot_de_passe') {
                $erreursMotDePasse = $this->traiterMotDePasse($idUtilisateur);
                $succesMotDePasse = empty($erreursMotDePasse);
            }
        }

        return [
            'utilisateur' => Utilisateur::trouverParId($idUtilisateur),
            'erreursInfos' => $erreursInfos,
            'erreursMotDePasse' => $erreursMotDePasse,
            'succesInfos' => $succesInfos,
            'succesMotDePasse' => $succesMotDePasse,
        ];
    }

    private function traiterInfos(int $idUtilisateur)
    {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $erreurs = [];

        if ($nom === '' || $prenom === '') {
            $erreurs[] = 'Le nom et le prénom sont obligatoires.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = 'L’adresse e-mail n’est pas valide.';
        } elseif (Utilisateur::emailExistePourUnAutreCompte($email, $idUtilisateur)) {
            $erreurs[] = 'Un autre compte utilise déjà cette adresse e-mail.';
        }

        if (empty($erreurs)) {
            Utilisateur::modifierInfos($idUtilisateur, $nom, $prenom, $email);

            // Le prénom affiché dans la navbar doit être à jour tout de suite.
            $_SESSION['utilisateur_prenom'] = $prenom;
        }

        return $erreurs;
    }

    private function traiterMotDePasse(int $idUtilisateur)
    {
        $motDePasseActuel = $_POST['mot_de_passe_actuel'] ?? '';
        $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'] ?? '';
        $confirmation = $_POST['confirmation'] ?? '';

        $utilisateur = Utilisateur::trouverParId($idUtilisateur);

        $erreurs = [];

        if (!password_verify($motDePasseActuel, $utilisateur['mot_de_passe'])) {
            $erreurs[] = 'Le mot de passe actuel est incorrect.';
        }

        if (strlen($nouveauMotDePasse) < 8) {
            $erreurs[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        } elseif ($nouveauMotDePasse !== $confirmation) {
            $erreurs[] = 'Les mots de passe ne correspondent pas.';
        }

        if (empty($erreurs)) {
            Utilisateur::modifierMotDePasse($idUtilisateur, $nouveauMotDePasse);
        }

        return $erreurs;
    }
}
