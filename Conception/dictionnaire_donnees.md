# Dictionnaire des données

Ce dictionnaire décrit, pour chaque entité de l'application (Utilisateur, Catégorie, Recette, Étape, Ingrédient, Avis, Favori), la liste des champs avec leur type, leur taille, leurs contraintes et leur description.

## UTILISATEUR

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_utilisateur | Entier | - | Clé primaire, auto-incrément | Identifiant unique de l'utilisateur |
| nom | Texte | 50 | Non nul | Nom de l'utilisateur |
| prenom | Texte | 50 | Non nul | Prénom de l'utilisateur |
| email | Texte | 100 | Non nul, unique | Adresse e-mail, utilisée pour la connexion |
| mot_de_passe | Texte | 255 | Non nul | Mot de passe haché |
| role | Enuméré | - | Non nul (membre / administrateur) | Rôle de l'utilisateur dans l'application |
| statut_compte | Enuméré | - | Non nul (actif / désactivé) | Statut du compte, géré par l'administrateur |
| date_inscription | Date/heure | - | Non nul | Date de création du compte |

## CATEGORIE

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_categorie | Entier | - | Clé primaire, auto-incrément | Identifiant unique de la catégorie |
| nom | Texte | 50 | Non nul, unique | Nom de la catégorie (entrées, plats, desserts, boissons) |

## RECETTE

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_recette | Entier | - | Clé primaire, auto-incrément | Identifiant unique de la recette |
| titre | Texte | 100 | Non nul | Titre de la recette |
| description | Texte long | - | Non nul | Courte description de la recette |
| photo | Texte | 255 | Non nul | Chemin du fichier image |
| nb_personnes | Entier | - | Non nul, > 0 | Nombre de personnes |
| temps_preparation | Entier | - | Non nul, ≥ 0 | Temps de préparation (en minutes) |
| temps_cuisson | Entier | - | Non nul, ≥ 0 | Temps de cuisson (en minutes) |
| difficulte | Enuméré | - | Non nul (facile / moyenne / difficile) | Niveau de difficulté |
| statut | Enuméré | - | Non nul (en attente / publiée / refusée) | Statut de validation de la recette |
| motif_refus | Texte long | - | Nul | Motif renseigné par l'administrateur en cas de refus |
| date_creation | Date/heure | - | Non nul | Date de création de la recette |
| date_modification | Date/heure | - | Nul | Date de la dernière modification, le cas échéant |
| date_validation | Date/heure | - | Nul | Date de validation ou de refus par l'administrateur |
| id_categorie | Entier | - | Clé étrangère → CATEGORIE, non nul | Catégorie de la recette |
| id_utilisateur | Entier | - | Clé étrangère → UTILISATEUR, non nul | Auteur de la recette |
| id_validateur | Entier | - | Clé étrangère → UTILISATEUR, nul | Administrateur ayant validé ou refusé la recette |

## ETAPE

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_etape | Entier | - | Clé primaire, auto-incrément | Identifiant unique de l'étape |
| numero_ordre | Entier | - | Non nul, > 0 | Ordre d'affichage de l'étape |
| description | Texte long | - | Non nul | Contenu de l'étape |
| id_recette | Entier | - | Clé étrangère → RECETTE, non nul | Recette concernée |

Contrainte d'unicité : le couple (id_recette, numero_ordre) doit être unique (deux étapes d'une même recette ne peuvent pas partager le même numéro d'ordre).

## INGREDIENT

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_ingredient | Entier | - | Clé primaire, auto-incrément | Identifiant unique de l'ingrédient |
| nom | Texte | 50 | Non nul, unique | Nom de l'ingrédient |

## RECETTE_INGREDIENT (table d'association)

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_recette | Entier | - | Clé primaire composée, clé étrangère → RECETTE, non nul | Recette concernée |
| id_ingredient | Entier | - | Clé primaire composée, clé étrangère → INGREDIENT, non nul | Ingrédient concerné |
| quantite | Décimal | DECIMAL(10,2) | Nul | Quantité de l'ingrédient (facultative, ex. « sel à votre convenance ») |
| unite | Texte | 20 | Nul | Unité de mesure (g, ml, pièce...), facultative pour les mêmes cas |

## AVIS

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_avis | Entier | - | Clé primaire, auto-incrément | Identifiant unique de l'avis |
| note | Entier | - | Non nul, entre 1 et 5 | Note attribuée à la recette |
| commentaire | Texte long | - | Non nul | Commentaire laissé par le membre |
| date_avis | Date/heure | - | Non nul | Date de l'avis |
| id_utilisateur | Entier | - | Clé étrangère → UTILISATEUR, non nul | Auteur de l'avis |
| id_recette | Entier | - | Clé étrangère → RECETTE, non nul | Recette concernée |

Contrainte d'unicité du couple utilisateur-recette : un couple (id_utilisateur, id_recette) ne peut apparaître qu'une seule fois (un membre ne peut laisser qu'un seul avis par recette).

## FAVORI (table d'association)

| Champ | Type | Taille | Contraintes | Description |
|---|---|---|---|---|
| id_utilisateur | Entier | - | Clé primaire composée, clé étrangère → UTILISATEUR, non nul | Membre concerné |
| id_recette | Entier | - | Clé primaire composée, clé étrangère → RECETTE, non nul | Recette ajoutée en favori |
| date_ajout | Date/heure | - | Non nul | Date d'ajout aux favoris |
