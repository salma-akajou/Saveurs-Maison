# Saveurs Maison

Saveurs Maison est une application web collaborative dédiée à la découverte et au partage de recettes de cuisine.

Elle permet de consulter des recettes, de publier ses propres préparations, d’enregistrer des favoris et de partager son avis. Un espace d’administration permet de vérifier les publications et de gérer le contenu de la plateforme.

Ce projet est réalisé dans le cadre de la formation **Développeur Web et Web Mobile (DWWM)**.

## Objectifs du projet

- Faciliter la recherche de recettes de cuisine.
- Permettre aux membres de partager leurs recettes.
- Présenter clairement les ingrédients et les étapes de préparation.
- Permettre l’enregistrement de recettes favorites.
- Proposer des notes et des commentaires.
- Assurer la validation et la modération des contenus.

## Technologies prévues

| Technologie | Utilisation |
|---|---|
| HTML5 | Structure des pages |
| CSS3 | Personnalisation de l’interface |
| Bootstrap | Mise en page responsive et composants |
| JavaScript | Interactions et formulaires dynamiques |
| PHP | Traitements côté serveur |
| MySQL | Stockage des données |
| PDO | Accès à la base de données |

L’application sera organisée selon une architecture **MVC (Modèle - Vue - Contrôleur)**.

## Profils utilisateurs

### Visiteur

Le visiteur peut :

- Consulter les recettes publiées.
- Rechercher une recette par son nom.
- Filtrer les recettes par catégorie et par difficulté.
- Consulter les ingrédients, les étapes et les avis.
- Créer un compte et se connecter.

### Membre connecté

En complément des fonctionnalités publiques, le membre peut :

- Proposer une recette avec une photo.
- Consulter, modifier et supprimer ses propres recettes.
- Suivre le statut de ses publications.
- Consulter le motif de refus d’une recette.
- Ajouter ou retirer des recettes de ses favoris.
- Attribuer une note et laisser un commentaire.
- Modifier ou supprimer son propre avis.
- Modifier son profil et son mot de passe.

### Administrateur

L’administrateur peut :

- Consulter un tableau de bord.
- Ajouter, modifier et supprimer des recettes.
- Valider ou refuser les recettes proposées.
- Renseigner un motif en cas de refus.
- Gérer les catégories.
- Consulter les membres et activer ou désactiver leurs comptes.
- Consulter et supprimer les commentaires inappropriés.

## Fonctionnalités principales

### Catalogue et recherche

Les recettes sont présentées sous forme de cartes comprenant une photo, un titre, une catégorie et un temps de préparation.

Le catalogue permet :

- La recherche par nom.
- Le filtrage par catégorie : entrées, plats, desserts et boissons.
- Le filtrage par difficulté : facile, moyenne ou difficile.
- L’accès à la fiche complète d’une recette.

### Fiche d’une recette

Chaque recette comporte :

- Un titre.
- Une photo.
- Une courte description.
- Une catégorie.
- Un niveau de difficulté.
- Un nombre de personnes.
- Un temps de préparation et un temps de cuisson.
- Une liste d’ingrédients avec leurs quantités.
- Des étapes de préparation ordonnées.
- Le nom de l’auteur.
- Une note moyenne et les avis des membres.

### Publication des recettes

Le membre remplit un formulaire contenant les informations de la recette, sa photo, ses ingrédients et ses étapes.

La recette est ensuite soumise à validation.

| Statut | Signification |
|---|---|
| En attente | La recette attend la vérification de l’administrateur. |
| Publiée | La recette a été validée et apparaît dans le catalogue public. |
| Refusée | La recette n’a pas été acceptée ; un motif est communiqué au membre. |

Lorsqu’un membre modifie sa recette, celle-ci repasse en attente de validation et n’est plus visible dans le catalogue jusqu’à sa nouvelle validation.

### Favoris

Le membre peut enregistrer les recettes qui l’intéressent et les retrouver dans une page dédiée. Il peut les retirer de sa sélection à tout moment.

### Notes et commentaires

Le membre peut publier un avis comprenant une note de **1 à 5 étoiles** et un commentaire.

Chaque membre dispose d’un seul avis par recette. Il peut modifier ou supprimer son avis. La note moyenne affichée sur la recette est calculée à partir des notes enregistrées.

### Administration

Le tableau de bord présente notamment :

- Le nombre de recettes publiées.
- Le nombre de recettes en attente.
- Le nombre de membres.
- Le nombre d’avis.

L’administrateur dispose de pages dédiées à la gestion des recettes, des catégories, des membres et des commentaires.

## Pages de l’application

### Partie publique

| Page | Contenu |
|---|---|
| Accueil | Présentation du site, recherche, catégories et dernières recettes publiées |
| Les recettes | Catalogue avec recherche et filtres |
| Détail d’une recette | Informations pratiques, ingrédients, étapes et avis |
| Inscription | Création d’un compte |
| Connexion | Authentification des membres et de l’administrateur |
| À propos | Présentation du concept et du fonctionnement |
| Mentions légales et confidentialité | Informations légales et utilisation des données personnelles |

### Espace membre

| Page | Contenu |
|---|---|
| Mon espace | Résumé des publications, des statuts et des favoris |
| Mes recettes | Liste des recettes du membre et actions de gestion |
| Ajouter une recette | Formulaire de proposition d’une recette |
| Modifier une recette | Modification d’une recette existante |
| Mes favoris | Liste des recettes enregistrées |
| Mon profil | Modification des informations personnelles et du mot de passe |

Les notes et les commentaires sont ajoutés directement depuis la fiche d’une recette.

### Espace administrateur

| Page | Contenu |
|---|---|
| Tableau de bord | Indicateurs et dernières recettes en attente |
| Gestion des recettes | Liste des recettes et filtres par statut |
| Vérification d’une recette | Consultation, validation ou refus avec motif |
| Ajouter / modifier une recette | Création et modification des recettes |
| Gestion des catégories | Ajout, modification et suppression des catégories inutilisées |
| Gestion des membres | Consultation, activation et désactivation des comptes |
| Gestion des avis | Consultation et suppression des commentaires inappropriés |

Une seule page de connexion est prévue. Après authentification, chaque utilisateur est dirigé vers l’espace correspondant à son rôle.

## Règles de fonctionnement

- Seules les recettes publiées sont visibles dans le catalogue public.
- La publication, les favoris et les avis nécessitent un compte connecté.
- Un membre peut modifier ou supprimer uniquement ses propres recettes et avis.
- Les recettes proposées ou modifiées par les membres doivent être validées par l’administrateur.
- Le motif de refus est visible par l’auteur de la recette.
- Une recette ne peut être ajoutée qu’une seule fois aux favoris d’un même membre.
- Un membre ne peut publier qu’un seul avis par recette.
- Une catégorie utilisée par une recette ne peut pas être supprimée.
- Les pages d’administration sont réservées à l’administrateur.

## Interface et sécurité prévues

- Interface responsive adaptée aux ordinateurs, tablettes et téléphones.
- Navigation et formulaires cohérents entre les pages.
- Validation des données côté client et côté serveur.
- Stockage sécurisé des mots de passe par hachage.
- Requêtes préparées avec PDO.
- Contrôle des rôles et des droits d’accès.
- Protection des formulaires contre les requêtes non autorisées (CSRF).
- Échappement des contenus affichés pour prévenir les injections de scripts (XSS).
- Vérification du type et de la taille des photos envoyées.

## Exemple de parcours utilisateur

1. Un visiteur recherche une recette de gâteau au coco.
2. Il consulte les ingrédients et les étapes.
3. Il crée un compte pour enregistrer la recette dans ses favoris.
4. Après l’avoir préparée, il laisse une note et un commentaire.
5. Il propose ensuite sa propre recette de samoussas aux légumes.
6. L’administrateur vérifie et valide la proposition.
7. La recette devient accessible dans le catalogue public.

## État du projet

Les fonctionnalités présentées dans ce document constituent le périmètre prévu de l’application.
