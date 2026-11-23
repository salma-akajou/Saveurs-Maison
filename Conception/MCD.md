# Modèle Conceptuel de Données (MCD) — Notation Merise

Ce document décrit les entités de l'application, leurs propriétés, ainsi que les associations entre elles et leurs cardinalités, en notation Merise.

## Entités et identifiants

| Entité | Identifiant | Autres propriétés |
|---|---|---|
| UTILISATEUR | id_utilisateur | nom, prenom, email, mot_de_passe, role, statut_compte, date_inscription |
| CATEGORIE | id_categorie | nom |
| RECETTE | id_recette | titre, description, photo, nb_personnes, temps_preparation, temps_cuisson, difficulte, statut, motif_refus, date_creation, date_modification |
| ETAPE | id_etape | titre, numero_ordre, description |
| INGREDIENT | id_ingredient | nom |
| AVIS | id_avis | note, commentaire, date_avis |

## Associations et cardinalités

La cardinalité notée à côté d'une entité indique, pour une occurrence de cette entité, le nombre d'occurrences de l'autre entité auxquelles elle peut être liée par l'association.

| Association | Entité 1 (cardinalité) | Entité 2 (cardinalité) | Propriétés portées | Règle de gestion |
|---|---|---|---|---|
| PUBLIE | UTILISATEUR (0,n) | RECETTE (1,1) | - | Un utilisateur publie 0 à n recettes ; une recette est publiée par exactement 1 utilisateur. |
| CLASSE | CATEGORIE (0,n) | RECETTE (1,1) | - | Une catégorie classe 0 à n recettes ; une recette appartient à exactement 1 catégorie. |
| COMPORTE | RECETTE (1,n) | ETAPE (1,1) | - | Une recette comporte au moins 1 étape ; une étape appartient à exactement 1 recette. |
| COMPOSE | RECETTE (1,n) | INGREDIENT (0,n) | quantite, unite | Une recette est composée d'au moins 1 ingrédient ; un ingrédient peut composer 0 à n recettes. |
| ECRIT | UTILISATEUR (0,n) | AVIS (1,1) | - | Un utilisateur écrit 0 à n avis ; un avis est écrit par exactement 1 utilisateur. |
| CONCERNE | RECETTE (0,n) | AVIS (1,1) | - | Une recette reçoit 0 à n avis ; un avis concerne exactement 1 recette. |
| AJOUTE | UTILISATEUR (0,n) | RECETTE (0,n) | date_ajout | Un utilisateur ajoute 0 à n recettes en favori ; une recette peut être ajoutée en favori par 0 à n utilisateurs. |
| VALIDE | UTILISATEUR (0,n) | RECETTE (0,1) | date_validation | Un administrateur valide 0 à n recettes ; une recette est validée ou refusée par 0 ou 1 administrateur. Côté UTILISATEUR, seul le rôle administrateur peut participer à cette association. |

## Contraintes d'intégrité

- Contrainte d'unicité du couple utilisateur-recette : un couple (UTILISATEUR, RECETTE) ne peut apparaître qu'une seule fois dans l'association ECRIT/CONCERNE (un seul avis par membre et par recette).
- Une recette ne peut être ajoutée qu'une seule fois en favori par le même utilisateur (l'association AJOUTE porte une clé primaire composée, ce qui empêche naturellement les doublons).
- Une catégorie utilisée par au moins une recette ne peut pas être supprimée.
- nb_personnes doit être strictement positif (> 0).
- temps_preparation et temps_cuisson doivent être positifs ou nuls (≥ 0).
- numero_ordre (ETAPE) doit être strictement positif (> 0), et le couple (RECETTE, ETAPE) via COMPORTE doit avoir un numero_ordre unique par recette.
- L'association VALIDE n'est possible qu'avec un UTILISATEUR ayant le rôle administrateur.
- Lorsqu'un membre modifie une recette déjà publiée ou refusée, celle-ci repasse au statut « en attente » et les propriétés motif_refus, date_validation (et le lien VALIDE) sont réinitialisés.
- Toutes les associations obligatoires (cardinalité minimale 1) doivent donner lieu, au MLD, à une clé étrangère non nulle ; seule VALIDE, de cardinalité minimale 0, donne lieu à une clé étrangère facultative. Une clé étrangère non nulle garantit uniquement qu'une occurrence (une ETAPE, un ingrédient associé) est bien liée à une RECETTE : elle ne garantit pas l'inverse, c'est-à-dire qu'une RECETTE possède au moins une ETAPE (association COMPORTE) ou au moins un INGREDIENT (association COMPOSE). Ces deux cardinalités minimales de 1 côté RECETTE devront être vérifiées au niveau de l'application (formulaire de création d'une recette), pas par la seule structure de la base de données.

## Schéma des associations (représentation textuelle)

```
UTILISATEUR (0,n) ──PUBLIE── (1,1) RECETTE
UTILISATEUR (0,n) ──ECRIT──── (1,1) AVIS ────(1,1)──CONCERNE── (0,n) RECETTE
UTILISATEUR (0,n) ──────────────── AJOUTE [date_ajout] ──────────────── (0,n) RECETTE
UTILISATEUR (0,n) ──VALIDE [date_validation]── (0,1) RECETTE   (administrateur uniquement)
CATEGORIE   (0,n) ──CLASSE─── (1,1) RECETTE
RECETTE     (1,n) ──COMPORTE── (1,1) ETAPE
RECETTE     (1,n) ──COMPOSE [quantite, unite]── (0,n) INGREDIENT
```
