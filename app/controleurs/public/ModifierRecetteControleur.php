<?php

require_once __DIR__ . '/../../modeles/Recette.php';
require_once __DIR__ . '/../../modeles/Ingredient.php';
require_once __DIR__ . '/../../modeles/Etape.php';

/*
 * Contrôleur du formulaire "Modifier ma recette".
 * Page réservée au membre propriétaire de la recette (vérifié dans
 * la vue). Retourne null si la recette n'existe pas ou ne lui
 * appartient pas.
 */
class ModifierRecetteControleur
{
    public function afficher()
    {
        $idUtilisateur = (int) $_SESSION['utilisateur_id'];
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $recette = Recette::trouverParIdEtUtilisateur($id, $idUtilisateur);

        if ($recette === null) {
            return null;
        }

        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $erreurs = $this->traiterEnvoi($id, $recette);
        }

        return [
            'recette' => $recette,
            'ingredients' => Ingredient::listerPourRecette($id),
            'etapes' => Etape::listerParRecette($id),
            'erreurs' => $erreurs,
        ];
    }

    private function traiterEnvoi(int $idRecette, array $recetteActuelle)
    {
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $idCategorie = (int) ($_POST['id_categorie'] ?? 0);
        $difficulte = $_POST['difficulte'] ?? '';
        $nbPersonnes = (int) ($_POST['nb_personnes'] ?? 0);
        $tempsPreparation = isset($_POST['temps_preparation']) ? (int) $_POST['temps_preparation'] : -1;
        $tempsCuisson = isset($_POST['temps_cuisson']) ? (int) $_POST['temps_cuisson'] : -1;

        $ingredients = array_filter($_POST['ingredients'] ?? [], function ($ingredient) {
            return trim($ingredient['nom'] ?? '') !== '';
        });

        $etapes = array_filter($_POST['etapes'] ?? [], function ($etape) {
            return trim($etape['titre'] ?? '') !== '' && trim($etape['description'] ?? '') !== '';
        });

        $erreurs = $this->validerChamps(
            $titre,
            $description,
            $idCategorie,
            $difficulte,
            $nbPersonnes,
            $tempsPreparation,
            $tempsCuisson,
            $ingredients,
            $etapes
        );

        if (!empty($erreurs)) {
            return $erreurs;
        }

        // La photo est facultative ici : si rien n'est envoyé,
        // on garde l'ancienne.
        $nomPhoto = $this->enregistrerPhotoEnvoyee() ?? $recetteActuelle['photo'];

        Recette::modifier($idRecette, [
            'titre' => $titre,
            'description' => $description,
            'photo' => $nomPhoto,
            'nb_personnes' => $nbPersonnes,
            'temps_preparation' => $tempsPreparation,
            'temps_cuisson' => $tempsCuisson,
            'difficulte' => $difficulte,
            'id_categorie' => $idCategorie,
        ]);

        Ingredient::supprimerAssociationsPourRecette($idRecette);
        $this->enregistrerIngredients($idRecette, $ingredients);

        Etape::supprimerPourRecette($idRecette);
        $this->enregistrerEtapes($idRecette, $etapes);

        $estAdmin = $_SESSION['utilisateur_role'] === 'administrateur';

        // La recette modifiée par l'administrateur reste publiée
        // directement : Recette::modifier() l'a repassée en attente,
        // on la republie aussitôt (pas de validation à attendre de soi-même).
        if ($estAdmin) {
            Recette::valider($idRecette, (int) $_SESSION['utilisateur_id']);
        }

        // Cette page est utilisée à la fois par l'espace membre et par
        // l'espace admin (même formulaire) : on revient vers la bonne liste.
        $cible = $estAdmin ? '../admin/recettes.php' : 'mes-recettes.php';

        header('Location: ' . $cible . '?action=modifiee');
        exit;
    }

    private function validerChamps(
        string $titre,
        string $description,
        int $idCategorie,
        string $difficulte,
        int $nbPersonnes,
        int $tempsPreparation,
        int $tempsCuisson,
        array $ingredients,
        array $etapes
    ) {
        $erreurs = [];

        if ($titre === '') {
            $erreurs[] = 'Le titre est obligatoire.';
        }

        if ($description === '') {
            $erreurs[] = 'La description est obligatoire.';
        }

        if (!in_array($idCategorie, [1, 2, 3, 4], true)) {
            $erreurs[] = 'Choisissez une catégorie.';
        }

        if (!in_array($difficulte, ['facile', 'moyenne', 'difficile'], true)) {
            $erreurs[] = 'Choisissez une difficulté.';
        }

        if ($nbPersonnes < 1) {
            $erreurs[] = 'Le nombre de personnes doit être d’au moins 1.';
        }

        if ($tempsPreparation < 0 || $tempsCuisson < 0) {
            $erreurs[] = 'Les temps de préparation et de cuisson ne peuvent pas être négatifs.';
        }

        if (empty($ingredients)) {
            $erreurs[] = 'Ajoutez au moins un ingrédient.';
        }

        if (empty($etapes)) {
            $erreurs[] = 'Ajoutez au moins une étape.';
        }

        return $erreurs;
    }

    // Déplace la nouvelle photo envoyée vers le dossier des images du
    // site, et retourne son nouveau nom de fichier. Retourne null si
    // aucune photo (valide) n'a été envoyée : l'ancienne est conservée.
    private function enregistrerPhotoEnvoyee()
    {
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extensionsAutorisees = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $type = mime_content_type($_FILES['photo']['tmp_name']);

        if (!isset($extensionsAutorisees[$type])) {
            return null;
        }

        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $nomFichier = 'recette-' . uniqid() . '.' . $extensionsAutorisees[$type];
        $destination = __DIR__ . '/../../assets/images/' . $nomFichier;

        move_uploaded_file($_FILES['photo']['tmp_name'], $destination);

        return $nomFichier;
    }

    private function enregistrerIngredients(int $idRecette, array $ingredients)
    {
        foreach ($ingredients as $ingredient) {
            $nom = trim($ingredient['nom']);
            $quantite = ($ingredient['quantite'] ?? '') !== '' ? (float) $ingredient['quantite'] : null;
            $unite = ($ingredient['unite'] ?? '') !== '' ? $ingredient['unite'] : null;

            $idIngredient = Ingredient::trouverOuCreer($nom);
            Ingredient::associerARecette($idRecette, $idIngredient, $quantite, $unite);
        }
    }

    private function enregistrerEtapes(int $idRecette, array $etapes)
    {
        $numero = 1;

        foreach ($etapes as $etape) {
            Etape::ajouter($idRecette, trim($etape['titre']), trim($etape['description']), $numero);
            $numero++;
        }
    }
}
