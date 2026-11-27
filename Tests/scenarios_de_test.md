# Scénarios de test manuels

Ce document liste les scénarios de test de l'application Saveurs-Maison, organisés par acteur et par fonctionnalité, ainsi que les règles de gestion définies en phase 1 (`Analyse/tableau_besoins_moscow.md`).

Chaque ligne se teste à la main dans le navigateur : on suit les étapes, on compare avec le résultat attendu, et on note OK ou KO dans la colonne Statut.

- **OK** : testé, fonctionne comme attendu.
- **KO** : testé et problème constaté.

---

## 1. Visiteur (non connecté)

| ID | Scénario | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|
| V1 | Consulter le catalogue public | Aller sur `accueil.php` puis `recettes.php` | Seules les recettes au statut **publiée** apparaissent | OK |
| V2 | Rechercher une recette par son nom | Taper un mot du titre dans la barre de recherche | Seules les recettes dont le titre correspond s'affichent | OK |
| V3 | Filtrer par catégorie | Choisir une catégorie dans le filtre | Seules les recettes de cette catégorie s'affichent, y compris une catégorie ajoutée récemment par l'admin (ex. « Soupes ») | OK |
| V4 | Filtrer par difficulté | Choisir un niveau de difficulté | Seules les recettes de ce niveau s'affichent | OK |
| V5 | Consulter le détail d'une recette | Ouvrir une recette publiée | Ingrédients, étapes et avis existants s'affichent | OK |
| V6 | Accès refusé aux pages réservées | Essayer d'ouvrir une URL `admin/...` ou `membre/...` sans être connecté | Redirection vers `connexion.php` | OK |
| V7 | Créer un compte | Remplir le formulaire d'inscription | Compte créé, connexion possible ensuite | OK |
| V8 | Se connecter | Email + mot de passe corrects | Connexion réussie, redirigé selon le rôle (membre ou admin) | OK |
| V9 | Se connecter avec un mauvais mot de passe | Email correct, mot de passe faux | Message d'erreur, pas de connexion | OK |

## 2. Membre connecté

| ID | Scénario | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|
| M1 | Proposer une recette complète | Remplir tous les champs + une photo, enregistrer | Recette créée avec le statut **en attente** | OK |
| M2 | Proposer une recette avec un champ vide | Laisser le titre vide, enregistrer | Message d'erreur sous le champ titre, la page défile jusqu'au champ concerné | OK |
| M3 | Proposer une recette sans photo | Ne pas choisir de photo, enregistrer | Message d'erreur « Veuillez ajouter une photo », recette non créée | OK |
| M4 | Consulter mes recettes | Aller sur « Mes recettes » | Affiche uniquement les recettes du membre connecté, avec leur statut | OK |
| M5 | Modifier ma propre recette | Ouvrir une de ses recettes, changer un champ, enregistrer | Modification enregistrée | OK |
| M6 | Revenir en arrière depuis un formulaire | Cliquer sur la flèche/le lien retour en haut du formulaire | Retour à la page « Mes recettes » sans rien enregistrer | OK |
| M7 | Modifier la recette d'un autre membre | Modifier l'URL pour viser l'id d'une recette qui n'est pas la sienne | Accès refusé | OK |
| M8 | Supprimer ma propre recette | Cliquer sur supprimer, confirmer | Recette supprimée de la liste | OK |
| M9 | Suivre le statut d'une recette proposée | Observer le badge statut (en attente / publiée / refusée) | Le badge correspond à la décision de l'admin | OK |
| M10 | Consulter le motif de refus | Admin refuse une recette avec un motif, le membre consulte sa recette | Le motif de refus est visible | OK |
| M11 | Ajouter une recette aux favoris | Cliquer sur « Favoris » depuis une recette publiée | Recette ajoutée, visible dans « Mes favoris » | OK |
| M12 | Retirer une recette des favoris | Cliquer à nouveau sur « Favoris » | Recette retirée de la liste | OK |
| M13 | Laisser un avis (note + commentaire) | Donner une note de 1 à 5 et un commentaire sur une recette | Avis enregistré et visible sur la recette | OK |
| M14 | Laisser un deuxième avis sur la même recette | Essayer de noter deux fois la même recette | Refusé (un seul avis par membre et par recette) | OK |
| M15 | Modifier/supprimer mon propre avis | Depuis un avis déjà posté | Modification ou suppression possible | OK |

## 3. Administrateur

| ID | Scénario | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|
| A1 | Tableau de bord | Se connecter en admin | Affiche le nombre de recettes publiées/en attente, membres, avis, et le tableau des dernières recettes en attente (non vide) | OK |
| A2 | Valider une recette en attente | Depuis la liste des recettes, valider une recette « en attente » | Statut passe à « publiée », visible côté public | OK |
| A3 | Refuser une recette avec un motif | Refuser une recette en attente, saisir un motif | Statut passe à « refusée », motif enregistré | OK |
| A4 | Ajouter/modifier/supprimer une recette (admin) | Depuis « Gestion des recettes » | Fonctionne comme pour un membre, mais sans attente de validation | OK |
| A5 | Ajouter une catégorie | « Gestion des catégories », saisir un nom, enregistrer | Nouvelle catégorie en haut de la liste | OK |
| A6 | Ajouter une catégorie avec un nom déjà pris | Saisir un nom de catégorie existant | Message d'erreur, pas de doublon créé | OK |
| A7 | Supprimer une catégorie utilisée par une recette | Essayer de supprimer une catégorie liée à au moins une recette | Suppression bloquée (bouton désactivé + vérification côté serveur) | OK |
| A8 | Supprimer une catégorie inutilisée | Catégorie sans aucune recette | Suppression possible | OK |
| A9 | La nouvelle catégorie apparaît partout | Après ajout, vérifier le formulaire d'ajout de recette, les filtres public et admin, et le pied de page | La catégorie apparaît dans toutes les listes déroulantes, sans rien coder en dur | OK |
| A10 | Consulter la liste des membres | « Gestion des membres » | Liste de tous les membres avec leur statut de compte | OK |
| A11 | Désactiver un compte membre | Cliquer sur désactiver pour un membre | Statut passe à « désactivé », le membre ne peut plus se connecter | OK |
| A12 | Impossible de désactiver un administrateur | Essayer de désactiver un compte admin (y compris en forçant l'id dans la requête) | Refusé, le statut admin ne change jamais | OK |
| A13 | Consulter les avis | « Gestion des avis » | Liste de tous les avis avec note, commentaire, auteur, recette | OK |
| A14 | Supprimer un avis inapproprié | Cliquer sur supprimer depuis la liste des avis | Avis supprimé | OK |
| A15 | Accès admin refusé à un membre simple | Un membre (non admin) essaie d'ouvrir une page `admin/...` | Redirection, accès refusé | OK |

## 4. Règles de gestion transversales

| ID | Règle | Vérification | Statut |
|---|---|---|---|
| R1 | Seules les recettes publiées sont visibles dans le catalogue public | Une recette « en attente » ou « refusée » n'apparaît jamais côté visiteur | OK |
| R2 | Favoris et avis nécessitent un compte connecté | Un visiteur non connecté qui clique sur « Favoris » ou « Donner un avis » est redirigé vers la connexion | OK |
| R3 | Un membre ne modifie/supprime que ses propres recettes et avis | Voir M7 | OK |
| R4 | Une catégorie utilisée ne peut pas être supprimée | Voir A7 | OK |
| R5 | Les pages d'administration sont réservées à l'administrateur | Voir A15 | OK |

