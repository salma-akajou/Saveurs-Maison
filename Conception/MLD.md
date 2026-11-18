# Modèle Logique de Données (MLD)

Dérivé du MCD. Convention : clé primaire soulignée (**gras**), clé étrangère précédée de `#`.

```
UTILISATEUR (id_utilisateur, nom, prenom, email, mot_de_passe, role, statut_compte, date_inscription)

CATEGORIE (id_categorie, nom)

RECETTE (id_recette, titre, description, photo, nb_personnes, temps_preparation,
         temps_cuisson, difficulte, statut, motif_refus, date_publication,
         #id_categorie, #id_utilisateur)

ETAPE (id_etape, numero_ordre, description, #id_recette)

INGREDIENT (id_ingredient, nom)

RECETTE_INGREDIENT (#id_recette, #id_ingredient, quantite, unite)
    Clé primaire composée : (id_recette, id_ingredient)

AVIS (id_avis, note, commentaire, date_avis, #id_utilisateur, #id_recette)
    Contrainte unique : (id_utilisateur, id_recette)

FAVORI (#id_utilisateur, #id_recette, date_ajout)
    Clé primaire composée : (id_utilisateur, id_recette)
```

## Règles de passage appliquées

- Les associations **PUBLIE** et **CLASSE** sont de type (0,n)–(1,1) : la clé primaire de l'entité côté (1,1) migre en clé étrangère dans l'entité côté (0,n). → `id_utilisateur` et `id_categorie` migrent dans `RECETTE`.
- L'association **COMPORTE** est de type (1,n)–(1,1) : la clé primaire de `RECETTE` migre en clé étrangère dans `ETAPE`.
- Les associations **COMPOSE** et **AJOUTE** sont de type (0/1,n)–(0,n) : elles deviennent des tables à part entière (`RECETTE_INGREDIENT`, `FAVORI`), avec une clé primaire composée des deux clés étrangères.
- L'association **ECRIT/CONCERNE** (via l'entité `AVIS`) : `AVIS` étant déjà une entité avec son propre identifiant, les clés primaires de `UTILISATEUR` et `RECETTE` y migrent en clés étrangères, avec une contrainte d'unicité sur le couple pour respecter la règle « un seul avis par membre et par recette ».

## Correspondance avec les tables MySQL

Chaque entité/association ci-dessus correspondra à une table MySQL créée via un script SQL (phase suivante), avec les mêmes noms de champs, les clés primaires en `AUTO_INCREMENT` et les clés étrangères avec contraintes `FOREIGN KEY`.
