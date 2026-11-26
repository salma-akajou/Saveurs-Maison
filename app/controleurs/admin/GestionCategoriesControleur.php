<?php

require_once __DIR__ . '/../../modeles/Categorie.php';

/*
 * Contrôleur de la page "Catégories" de l'administration.
 * Page réservée aux administrateurs (vérifié dans la vue).
 */
class GestionCategoriesControleur
{
    public function afficher()
    {
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $erreurs = $this->traiterEnvoi();
        }

        // Mode modification : on pré-remplit le formulaire avec la
        // catégorie demandée dans l'URL (categories.php?modifier=3).
        $categorieAModifier = isset($_GET['modifier'])
            ? Categorie::trouverParId((int) $_GET['modifier'])
            : null;

        return [
            'categories' => Categorie::listerAvecNombreRecettes(),
            'erreurs' => $erreurs,
            'categorieAModifier' => $categorieAModifier,
        ];
    }

    // Aiguille vers l'ajout, la modification ou la suppression, selon
    // l'action envoyée. Retourne la liste des erreurs à afficher (vide
    // si tout s'est bien passé, auquel cas on a déjà redirigé).
    private function traiterEnvoi()
    {
        $action = $_POST['action'] ?? '';

        if ($action === 'ajouter') {
            return $this->ajouter();
        }

        if ($action === 'modifier') {
            return $this->modifier();
        }

        if ($action === 'supprimer') {
            $this->supprimer();
        }

        return [];
    }

    private function ajouter()
    {
        $nom = trim($_POST['nom'] ?? '');
        $erreurs = $this->validerNom($nom, null);

        if (!empty($erreurs)) {
            return $erreurs;
        }

        Categorie::creer($nom);

        header('Location: categories.php?action=ajoutee');
        exit;
    }

    private function modifier()
    {
        $id = (int) ($_POST['id_categorie'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $erreurs = $this->validerNom($nom, $id);

        if (!empty($erreurs)) {
            return $erreurs;
        }

        Categorie::modifier($id, $nom);

        header('Location: categories.php?action=modifiee');
        exit;
    }

    // Supprime une catégorie, seulement si plus aucune recette ne l'utilise
    // (le bouton est déjà désactivé dans ce cas, cette vérification est
    // une sécurité supplémentaire côté serveur).
    private function supprimer()
    {
        $id = (int) ($_POST['id_categorie'] ?? 0);

        if (Categorie::compterRecettes($id) > 0) {
            header('Location: categories.php?action=utilisee');
            exit;
        }

        Categorie::supprimer($id);

        header('Location: categories.php?action=supprimee');
        exit;
    }

    private function validerNom(string $nom, ?int $idAExclure)
    {
        $erreurs = [];

        if ($nom === '') {
            $erreurs[] = 'Le nom de la catégorie est obligatoire.';
        } elseif (mb_strlen($nom) > 50) {
            $erreurs[] = 'Le nom ne doit pas dépasser 50 caractères.';
        } elseif (Categorie::nomExiste($nom, $idAExclure)) {
            $erreurs[] = 'Cette catégorie existe déjà.';
        }

        return $erreurs;
    }
}
