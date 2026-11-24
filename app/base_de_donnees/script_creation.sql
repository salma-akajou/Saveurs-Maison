-- ==================================================
-- SAVEURS MAISON — SCRIPT DE CRÉATION DE LA BASE
-- Ce script crée la base de données et ses 8 tables,
-- à partir du MLD (Conception/MLD.md).
-- ==================================================

-- On crée la base si elle n'existe pas déjà.
-- utf8mb4 permet d'enregistrer les accents et les emojis sans problème.
CREATE DATABASE IF NOT EXISTS saveurs_maison
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Toutes les instructions suivantes s'appliquent à cette base.
USE saveurs_maison;

-- On désactive un instant la vérification des clés étrangères,
-- pour pouvoir supprimer les tables dans n'importe quel ordre
-- (utile si on relance le script plusieurs fois pendant les tests).
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS FAVORI;
DROP TABLE IF EXISTS AVIS;
DROP TABLE IF EXISTS RECETTE_INGREDIENT;
DROP TABLE IF EXISTS INGREDIENT;
DROP TABLE IF EXISTS ETAPE;
DROP TABLE IF EXISTS RECETTE;
DROP TABLE IF EXISTS CATEGORIE;
DROP TABLE IF EXISTS UTILISATEUR;

-- On réactive la vérification des clés étrangères pour la création.
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------
-- UTILISATEUR
-- Les membres ET l'administrateur sont stockés dans la même table ;
-- la colonne "role" permet de les distinguer.
-- --------------------------------------------------

CREATE TABLE UTILISATEUR (
  -- Identifiant unique, généré automatiquement par MySQL (1, 2, 3...).
  id_utilisateur    INT UNSIGNED AUTO_INCREMENT,

  nom               VARCHAR(50)  NOT NULL,
  prenom            VARCHAR(50)  NOT NULL,

  -- "email" est unique : deux comptes ne peuvent pas avoir le même.
  email             VARCHAR(100) NOT NULL,

  -- On ne stocke jamais un mot de passe en clair : "mot_de_passe"
  -- contiendra le résultat de password_hash(), d'où les 255 caractères.
  mot_de_passe      VARCHAR(255) NOT NULL,

  -- ENUM : la colonne ne peut contenir qu'une de ces deux valeurs.
  role              ENUM('membre', 'administrateur') NOT NULL DEFAULT 'membre',

  -- Permet à l'administrateur d'activer ou de désactiver un compte.
  statut_compte     ENUM('actif', 'desactive') NOT NULL DEFAULT 'actif',

  -- Remplie automatiquement à la création de la ligne.
  date_inscription  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id_utilisateur),
  UNIQUE KEY uq_utilisateur_email (email)
) ENGINE = InnoDB;

-- --------------------------------------------------
-- CATEGORIE
-- Les catégories de recettes (Entrée, Plat, Dessert, Boisson...).
-- --------------------------------------------------

CREATE TABLE CATEGORIE (
  id_categorie  INT UNSIGNED AUTO_INCREMENT,

  -- "nom" est unique : pas deux fois la même catégorie.
  nom           VARCHAR(50) NOT NULL,

  PRIMARY KEY (id_categorie),
  UNIQUE KEY uq_categorie_nom (nom)
) ENGINE = InnoDB;

-- --------------------------------------------------
-- RECETTE
-- --------------------------------------------------

CREATE TABLE RECETTE (
  id_recette          INT UNSIGNED AUTO_INCREMENT,

  titre               VARCHAR(100) NOT NULL,
  description         TEXT NOT NULL,

  -- On stocke seulement le nom du fichier (ou son chemin),
  -- pas l'image elle-même.
  photo               VARCHAR(255) NOT NULL,

  nb_personnes        INT UNSIGNED NOT NULL,
  temps_preparation   INT UNSIGNED NOT NULL,
  temps_cuisson       INT UNSIGNED NOT NULL,

  difficulte          ENUM('facile', 'moyenne', 'difficile') NOT NULL,

  -- Statut de modération : la recette passe par ces 3 étapes.
  statut              ENUM('en_attente', 'publiee', 'refusee') NOT NULL DEFAULT 'en_attente',

  -- Rempli uniquement si l'administrateur refuse la recette.
  motif_refus         TEXT NULL,

  date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- Remplie seulement si la recette a été modifiée après sa création.
  date_modification   DATETIME NULL,

  -- Remplie seulement après la décision de l'administrateur.
  date_validation     DATETIME NULL,

  -- Ces 3 colonnes sont des clés étrangères (voir CONSTRAINT plus bas).
  id_categorie        INT UNSIGNED NOT NULL,
  id_utilisateur      INT UNSIGNED NOT NULL,
  id_validateur       INT UNSIGNED NULL,

  PRIMARY KEY (id_recette),

  -- CHECK : MySQL refuse l'insertion si la condition est fausse.
  CONSTRAINT chk_recette_nb_personnes CHECK (nb_personnes > 0),
  CONSTRAINT chk_recette_temps_preparation CHECK (temps_preparation >= 0),
  CONSTRAINT chk_recette_temps_cuisson CHECK (temps_cuisson >= 0),

  -- Une recette appartient obligatoirement à une catégorie existante.
  -- ON DELETE RESTRICT : on ne peut pas supprimer une catégorie
  -- si des recettes l'utilisent encore.
  CONSTRAINT fk_recette_categorie
    FOREIGN KEY (id_categorie) REFERENCES CATEGORIE (id_categorie)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  -- Une recette a obligatoirement un auteur (membre ou administrateur).
  CONSTRAINT fk_recette_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  -- id_validateur peut être vide (NULL) tant que la recette n'est pas
  -- encore vérifiée par un administrateur.
  CONSTRAINT fk_recette_validateur
    FOREIGN KEY (id_validateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- ETAPE
-- Une recette comporte plusieurs étapes, chacune avec un titre,
-- un numéro d'ordre et une description.
-- --------------------------------------------------

CREATE TABLE ETAPE (
  id_etape      INT UNSIGNED AUTO_INCREMENT,

  titre         VARCHAR(100) NOT NULL,

  -- Sert à afficher les étapes dans le bon ordre (1, 2, 3...).
  numero_ordre  INT UNSIGNED NOT NULL,

  description   TEXT NOT NULL,

  -- Clé étrangère : l'étape appartient à une seule recette.
  id_recette    INT UNSIGNED NOT NULL,

  PRIMARY KEY (id_etape),

  -- Deux étapes d'une même recette ne peuvent pas avoir le même numéro.
  UNIQUE KEY uq_etape_recette_ordre (id_recette, numero_ordre),

  CONSTRAINT chk_etape_numero_ordre CHECK (numero_ordre > 0),

  -- Si on supprime une recette, ses étapes sont supprimées avec elle.
  CONSTRAINT fk_etape_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- INGREDIENT
-- Catalogue des ingrédients, partagé par toutes les recettes
-- (un même ingrédient n'est enregistré qu'une seule fois).
-- --------------------------------------------------

CREATE TABLE INGREDIENT (
  id_ingredient  INT UNSIGNED AUTO_INCREMENT,
  nom            VARCHAR(50) NOT NULL,

  PRIMARY KEY (id_ingredient),
  UNIQUE KEY uq_ingredient_nom (nom)
) ENGINE = InnoDB;

-- --------------------------------------------------
-- RECETTE_INGREDIENT
-- Table d'association : elle relie une RECETTE à un INGREDIENT,
-- avec la quantité et l'unité propres à cette recette.
-- --------------------------------------------------

CREATE TABLE RECETTE_INGREDIENT (
  id_recette     INT UNSIGNED NOT NULL,
  id_ingredient  INT UNSIGNED NOT NULL,

  -- Facultatifs : certains ingrédients n'ont pas de quantité précise
  -- (ex. "sel et poivre, à votre convenance").
  quantite       DECIMAL(10, 2) NULL,
  unite          VARCHAR(20) NULL,

  -- Clé primaire composée des deux clés étrangères : une même
  -- recette ne peut pas avoir deux fois le même ingrédient.
  PRIMARY KEY (id_recette, id_ingredient),

  CONSTRAINT chk_recette_ingredient_quantite CHECK (quantite IS NULL OR quantite > 0),

  -- Si on supprime la recette, la ligne d'association disparaît aussi.
  CONSTRAINT fk_recette_ingredient_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE,

  -- On ne supprime pas un ingrédient encore utilisé par une recette.
  CONSTRAINT fk_recette_ingredient_ingredient
    FOREIGN KEY (id_ingredient) REFERENCES INGREDIENT (id_ingredient)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- AVIS
-- Note et commentaire laissés par un membre sur une recette.
-- --------------------------------------------------

CREATE TABLE AVIS (
  id_avis        INT UNSIGNED AUTO_INCREMENT,

  -- Note de 1 à 5, vérifiée par la contrainte CHECK ci-dessous.
  note           TINYINT UNSIGNED NOT NULL,

  commentaire    TEXT NOT NULL,
  date_avis      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  id_utilisateur INT UNSIGNED NOT NULL,
  id_recette     INT UNSIGNED NOT NULL,

  PRIMARY KEY (id_avis),

  -- Un même membre ne peut laisser qu'un seul avis par recette.
  UNIQUE KEY uq_avis_utilisateur_recette (id_utilisateur, id_recette),

  CONSTRAINT chk_avis_note CHECK (note BETWEEN 1 AND 5),

  CONSTRAINT fk_avis_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  -- Si on supprime la recette, ses avis sont supprimés avec elle.
  CONSTRAINT fk_avis_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- FAVORI
-- Table d'association : elle relie un UTILISATEUR à une RECETTE
-- qu'il a ajoutée à ses favoris.
-- --------------------------------------------------

CREATE TABLE FAVORI (
  id_utilisateur  INT UNSIGNED NOT NULL,
  id_recette      INT UNSIGNED NOT NULL,
  date_ajout      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- Clé primaire composée : une recette ne peut être ajoutée
  -- qu'une seule fois aux favoris d'un même membre.
  PRIMARY KEY (id_utilisateur, id_recette),

  CONSTRAINT fk_favori_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_favori_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
