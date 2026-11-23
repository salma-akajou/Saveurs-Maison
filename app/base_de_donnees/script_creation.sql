-- ==================================================
-- SAVEURS MAISON — SCRIPT DE CRÉATION DE LA BASE
-- Généré à partir du MLD (Conception/MLD.md)
-- ==================================================

CREATE DATABASE IF NOT EXISTS saveurs_maison
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE saveurs_maison;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS FAVORI;
DROP TABLE IF EXISTS AVIS;
DROP TABLE IF EXISTS RECETTE_INGREDIENT;
DROP TABLE IF EXISTS INGREDIENT;
DROP TABLE IF EXISTS ETAPE;
DROP TABLE IF EXISTS RECETTE;
DROP TABLE IF EXISTS CATEGORIE;
DROP TABLE IF EXISTS UTILISATEUR;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------
-- UTILISATEUR
-- --------------------------------------------------

CREATE TABLE UTILISATEUR (
  id_utilisateur    INT UNSIGNED AUTO_INCREMENT,
  nom               VARCHAR(50)  NOT NULL,
  prenom            VARCHAR(50)  NOT NULL,
  email             VARCHAR(100) NOT NULL,
  mot_de_passe      VARCHAR(255) NOT NULL,
  role              ENUM('membre', 'administrateur') NOT NULL DEFAULT 'membre',
  statut_compte     ENUM('actif', 'desactive') NOT NULL DEFAULT 'actif',
  date_inscription  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id_utilisateur),
  UNIQUE KEY uq_utilisateur_email (email)
) ENGINE = InnoDB;

-- --------------------------------------------------
-- CATEGORIE
-- --------------------------------------------------

CREATE TABLE CATEGORIE (
  id_categorie  INT UNSIGNED AUTO_INCREMENT,
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
  photo               VARCHAR(255) NOT NULL,
  nb_personnes        INT UNSIGNED NOT NULL,
  temps_preparation   INT UNSIGNED NOT NULL,
  temps_cuisson       INT UNSIGNED NOT NULL,
  difficulte          ENUM('facile', 'moyenne', 'difficile') NOT NULL,
  statut              ENUM('en_attente', 'publiee', 'refusee') NOT NULL DEFAULT 'en_attente',
  motif_refus         TEXT NULL,
  date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification   DATETIME NULL,
  date_validation     DATETIME NULL,
  id_categorie        INT UNSIGNED NOT NULL,
  id_utilisateur      INT UNSIGNED NOT NULL,
  id_validateur       INT UNSIGNED NULL,

  PRIMARY KEY (id_recette),
  CONSTRAINT chk_recette_nb_personnes CHECK (nb_personnes > 0),
  CONSTRAINT chk_recette_temps_preparation CHECK (temps_preparation >= 0),
  CONSTRAINT chk_recette_temps_cuisson CHECK (temps_cuisson >= 0),
  CONSTRAINT fk_recette_categorie
    FOREIGN KEY (id_categorie) REFERENCES CATEGORIE (id_categorie)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_recette_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_recette_validateur
    FOREIGN KEY (id_validateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- ETAPE
-- --------------------------------------------------

CREATE TABLE ETAPE (
  id_etape      INT UNSIGNED AUTO_INCREMENT,
  titre         VARCHAR(100) NOT NULL,
  numero_ordre  INT UNSIGNED NOT NULL,
  description   TEXT NOT NULL,
  id_recette    INT UNSIGNED NOT NULL,

  PRIMARY KEY (id_etape),
  UNIQUE KEY uq_etape_recette_ordre (id_recette, numero_ordre),
  CONSTRAINT chk_etape_numero_ordre CHECK (numero_ordre > 0),
  CONSTRAINT fk_etape_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- INGREDIENT
-- --------------------------------------------------

CREATE TABLE INGREDIENT (
  id_ingredient  INT UNSIGNED AUTO_INCREMENT,
  nom            VARCHAR(50) NOT NULL,

  PRIMARY KEY (id_ingredient),
  UNIQUE KEY uq_ingredient_nom (nom)
) ENGINE = InnoDB;

-- --------------------------------------------------
-- RECETTE_INGREDIENT (association RECETTE / INGREDIENT)
-- --------------------------------------------------

CREATE TABLE RECETTE_INGREDIENT (
  id_recette     INT UNSIGNED NOT NULL,
  id_ingredient  INT UNSIGNED NOT NULL,
  quantite       DECIMAL(10, 2) NULL,
  unite          VARCHAR(20) NULL,

  PRIMARY KEY (id_recette, id_ingredient),
  CONSTRAINT chk_recette_ingredient_quantite CHECK (quantite IS NULL OR quantite > 0),
  CONSTRAINT fk_recette_ingredient_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_recette_ingredient_ingredient
    FOREIGN KEY (id_ingredient) REFERENCES INGREDIENT (id_ingredient)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- AVIS
-- --------------------------------------------------

CREATE TABLE AVIS (
  id_avis        INT UNSIGNED AUTO_INCREMENT,
  note           TINYINT UNSIGNED NOT NULL,
  commentaire    TEXT NOT NULL,
  date_avis      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_utilisateur INT UNSIGNED NOT NULL,
  id_recette     INT UNSIGNED NOT NULL,

  PRIMARY KEY (id_avis),
  UNIQUE KEY uq_avis_utilisateur_recette (id_utilisateur, id_recette),
  CONSTRAINT chk_avis_note CHECK (note BETWEEN 1 AND 5),
  CONSTRAINT fk_avis_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_avis_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- --------------------------------------------------
-- FAVORI (association UTILISATEUR / RECETTE)
-- --------------------------------------------------

CREATE TABLE FAVORI (
  id_utilisateur  INT UNSIGNED NOT NULL,
  id_recette      INT UNSIGNED NOT NULL,
  date_ajout      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id_utilisateur, id_recette),
  CONSTRAINT fk_favori_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_favori_recette
    FOREIGN KEY (id_recette) REFERENCES RECETTE (id_recette)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
