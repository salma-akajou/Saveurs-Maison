# Tableau des besoins — Méthode MoSCoW

Ce tableau recense, pour chaque acteur (Visiteur, Membre connecté, Administrateur), les besoins fonctionnels de l'application, priorisés selon la méthode **MoSCoW** :

- **Must have** : indispensable, le projet ne fonctionne pas sans.
- **Should have** : important, mais le projet peut fonctionner sans dans un premier temps.
- **Could have** : confort, amélioration de l'expérience utilisateur.
- **Won't have** : hors périmètre pour cette version du projet.

## Visiteur

| Besoin / Fonctionnalité | Priorité |
|---|---|
| Consulter les recettes publiées | Must have |
| Rechercher une recette par son nom | Must have |
| Filtrer les recettes par catégorie et par difficulté | Should have |
| Consulter les ingrédients, les étapes et les avis d'une recette | Must have |
| Créer un compte | Must have |
| Se connecter | Must have |

## Membre connecté

| Besoin / Fonctionnalité | Priorité |
|---|---|
| Proposer une recette avec une photo | Must have |
| Consulter ses propres recettes | Must have |
| Modifier ses propres recettes | Must have |
| Supprimer ses propres recettes | Should have |
| Suivre le statut de ses publications (en attente / publiée / refusée) | Must have |
| Consulter le motif de refus d'une recette | Must have |
| Ajouter une recette à ses favoris | Must have |
| Retirer une recette de ses favoris | Must have |
| Consulter ses favoris | Must have |
| Attribuer une note (1 à 5 étoiles) et laisser un commentaire | Must have |
| Modifier son propre avis | Should have |
| Supprimer son propre avis | Should have |
| Modifier son profil et son mot de passe | Could have |
| Se déconnecter | Must have |

## Administrateur

| Besoin / Fonctionnalité | Priorité |
|---|---|
| Consulter un tableau de bord (recettes publiées, en attente, membres, avis) | Should have |
| Ajouter une recette | Must have |
| Modifier une recette | Must have |
| Supprimer une recette | Must have |
| Valider une recette proposée | Must have |
| Refuser une recette proposée avec un motif | Must have |
| Gérer les catégories (ajout, modification, suppression) | Should have |
| Consulter la liste des membres | Must have |
| Activer ou désactiver un compte membre | Must have |
| Consulter les commentaires | Should have |
| Supprimer un commentaire inapproprié | Should have |
| Se déconnecter | Must have |

## Hors périmètre (Won't have)

| Besoin écarté | Raison |
|---|---|
| Notifications par e-mail en temps réel | Non prévu pour cette version du projet |
| Partage des recettes sur les réseaux sociaux | Non prévu pour cette version du projet |

## Règles de gestion associées

- Seules les recettes publiées sont visibles dans le catalogue public.
- La publication, les favoris et les avis nécessitent un compte connecté.
- Un membre peut modifier ou supprimer uniquement ses propres recettes et avis.
- Les recettes proposées ou modifiées par les membres doivent être validées par l'administrateur.
- Le motif de refus est visible par l'auteur de la recette.
- Une recette ne peut être ajoutée qu'une seule fois aux favoris d'un même membre.
- Un membre ne peut publier qu'un seul avis par recette.
- Une catégorie utilisée par une recette ne peut pas être supprimée.
- Les pages d'administration sont réservées à l'administrateur.
