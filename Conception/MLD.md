# Modèle Logique de Données (MLD)

Convention : clé primaire en **gras**, clé étrangère précédée de `#` (une clé étrangère qui fait aussi partie de la clé primaire composée est à la fois en `#` et en **gras**).

**UTILISATEUR** (**id_utilisateur**, nom, prenom, email, mot_de_passe, role, statut_compte, date_inscription)

**CATEGORIE** (**id_categorie**, nom)

**RECETTE** (**id_recette**, titre, description, photo, nb_personnes, temps_preparation, temps_cuisson, difficulte, statut, motif_refus, date_creation, date_modification, date_validation, #id_categorie, #id_utilisateur, #id_validateur)
- `#id_categorie` et `#id_utilisateur` : non nuls (références obligatoires)
- `#id_validateur` : nul tant que la recette n'est pas validée ou refusée (référence facultative, administrateur uniquement)

**ETAPE** (**id_etape**, numero_ordre, description, #id_recette)
- Contrainte unique : (id_recette, numero_ordre)

**INGREDIENT** (**id_ingredient**, nom)

**RECETTE_INGREDIENT** (**#id_recette**, **#id_ingredient**, quantite DECIMAL(10,2), unite)
- Clé primaire composée : (id_recette, id_ingredient)
- quantite et unite : facultatifs (ex. « sel à votre convenance ») ; si quantite est renseignée, elle doit être strictement positive (> 0)

**AVIS** (**id_avis**, note, commentaire, date_avis, #id_utilisateur, #id_recette)
- Contrainte unique : (id_utilisateur, id_recette)

**FAVORI** (**#id_utilisateur**, **#id_recette**, date_ajout)
- Clé primaire composée : (id_utilisateur, id_recette)

## Règles de passage appliquées

- Les associations **PUBLIE** et **CLASSE** sont de type (0,n)–(1,1) : la clé primaire de l'entité côté (0,n) migre en clé étrangère, non nulle, dans l'entité côté (1,1). → `id_utilisateur` (côté 0,n de PUBLIE) et `id_categorie` (côté 0,n de CLASSE) migrent dans `RECETTE` (côté 1,1 des deux associations).
- L'association **COMPORTE** est de type (1,n)–(1,1) : `RECETTE` est côté (1,n) et `ETAPE` côté (1,1) ; la clé primaire de `RECETTE` migre donc en clé étrangère, non nulle, dans `ETAPE`.
- Les associations **COMPOSE** et **AJOUTE** sont de type (0/1,n)–(0,n) : aucun des deux côtés n'ayant de cardinalité maximale à 1, elles deviennent des tables à part entière (`RECETTE_INGREDIENT`, `FAVORI`), avec une clé primaire composée des deux clés étrangères.
- L'association **ECRIT/CONCERNE** (via l'entité `AVIS`) : `AVIS` étant côté (1,1) des deux associations, les clés primaires de `UTILISATEUR` et `RECETTE` y migrent en clés étrangères non nulles, avec une contrainte d'unicité du couple utilisateur-recette pour respecter la règle « un seul avis par membre et par recette ».
- L'association **VALIDE** est de type (0,n)–(0,1) : même principe que PUBLIE/CLASSE, mais côté `RECETTE` la cardinalité maximale est 1 avec un minimum de 0 (une recette n'est pas forcément déjà validée). La clé de `UTILISATEUR` migre donc dans `RECETTE`, sous forme de clé étrangère **nullable** (`id_validateur`), puisque la cardinalité minimale côté RECETTE est 0.

Règle de gestion associée à **VALIDE** : lorsqu'un membre modifie une recette déjà publiée ou refusée, celle-ci repasse au statut « en attente » et les champs `motif_refus`, `date_validation` et `id_validateur` sont remis à `NULL`, puisque la modification annule la validation ou le refus précédent.

## Correspondance avec les tables MySQL

Chaque entité/association ci-dessus correspondra à une table MySQL créée via un script SQL (phase suivante), avec les mêmes noms de champs et les clés étrangères avec contraintes `FOREIGN KEY`. L'`AUTO_INCREMENT` ne s'applique qu'aux tables à identifiant simple (`UTILISATEUR`, `CATEGORIE`, `RECETTE`, `ETAPE`, `INGREDIENT`, `AVIS`) ; les tables `RECETTE_INGREDIENT` et `FAVORI`, dont la clé primaire est composée des clés étrangères, n'utilisent pas l'`AUTO_INCREMENT`.
