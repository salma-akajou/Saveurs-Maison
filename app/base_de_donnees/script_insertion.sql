-- ==================================================
-- SAVEURS MAISON — JEU DE DONNÉES DE TEST
-- À exécuter après script_creation.sql
-- Les comptes, recettes et avis reprennent les mêmes noms
-- que les maquettes (Conception/maquettes/), pour rester cohérents.
-- ==================================================

USE saveurs_maison;

-- --------------------------------------------------
-- UTILISATEUR
-- Mot de passe de test pour TOUS les comptes ci-dessous : motdepasse123
-- (haché avec password_hash(), le même hachage est réutilisé pour
-- chaque compte mais correspond bien à "motdepasse123" pour tous).
-- id_utilisateur 1 = l'administrateur, 2 à 7 = les membres.
-- --------------------------------------------------

INSERT INTO UTILISATEUR
  (id_utilisateur, nom, prenom, email, mot_de_passe, role, statut_compte, date_inscription)
VALUES
  (1, 'Admin',   'Administrateur', 'admin@saveurs-maison.fr',     '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'administrateur', 'actif',     '2026-10-01 09:00:00'),
  (2, 'Laurent', 'Sophie',         'sophie.laurent@example.fr',   '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'membre',         'actif',     '2026-11-12 10:15:00'),
  (3, 'Ali',     'Nadia',          'nadia.ali@example.fr',        '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'membre',         'actif',     '2026-11-13 11:20:00'),
  (4, 'Martin',  'Karim',          'karim.martin@example.fr',     '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'membre',         'desactive', '2026-11-14 14:05:00'),
  (5, 'Diallo',  'Yanis',          'yanis.diallo@example.fr',     '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'membre',         'actif',     '2026-11-15 16:40:00'),
  (6, 'Petit',   'Claire',         'claire.petit@example.fr',     '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'membre',         'actif',     '2026-11-16 08:30:00'),
  (7, 'Thomas',  'Hugo',           'hugo.thomas@example.fr',      '$2y$10$hTowJntkhUjpXcCeFvF86.ahA0xiBtIB4Jky2oGHJXAkt/hfoOxgO', 'membre',         'desactive', '2026-11-17 19:10:00');

-- --------------------------------------------------
-- CATEGORIE
-- Mêmes identifiants que les listes déroulantes des maquettes
-- (1 Entrée, 2 Plat, 3 Dessert, 4 Boisson).
-- --------------------------------------------------

INSERT INTO CATEGORIE (id_categorie, nom) VALUES
  (1, 'Entrée'),
  (2, 'Plat'),
  (3, 'Dessert'),
  (4, 'Boisson');

-- --------------------------------------------------
-- RECETTE
-- Chaque ligne illustre un cas différent :
-- 1, 2, 4 et 6 publiées, 3 en attente, 5 refusée (avec son motif_refus).
-- --------------------------------------------------

INSERT INTO RECETTE
  (id_recette, titre, description, photo, nb_personnes, temps_preparation, temps_cuisson,
   difficulte, statut, motif_refus, date_creation, date_modification, date_validation,
   id_categorie, id_utilisateur, id_validateur)
VALUES
  (1, 'Salade méditerranéenne',
      'Une salade fraîche aux tomates, concombre, feta et olives, relevée d’un filet d’huile d’olive.',
      'salade-mediterraneenne.jpg', 4, 15, 0, 'facile', 'publiee', NULL,
      '2026-11-18 09:00:00', NULL, '2026-11-18 11:00:00', 1, 1, 1),

  (2, 'Samoussas aux légumes',
      'Des samoussas croustillants garnis de légumes, à partager en entrée.',
      'samoussas-legumes.jpg', 4, 20, 10, 'facile', 'publiee', NULL,
      '2026-11-23 10:30:00', NULL, '2026-11-23 14:00:00', 1, 2, 1),

  (3, 'Gâteau au coco',
      'Un gâteau moelleux à la noix de coco et au lait de coco.',
      'gateau-coco.jpg', 6, 20, 35, 'moyenne', 'en_attente', NULL,
      '2026-11-22 15:45:00', NULL, NULL, 3, 3, NULL),

  (4, 'Tarte aux pommes',
      'Une tarte croustillante aux pommes fondantes et à la touche de cannelle.',
      'tarte-pommes.jpg', 6, 25, 35, 'moyenne', 'publiee', NULL,
      '2026-11-20 08:20:00', NULL, '2026-11-20 10:00:00', 3, 1, 1),

  (5, 'Smoothie mangue passion',
      'Une boisson fruitée et onctueuse aux saveurs de mangue et de passion.',
      'smoothie-mangue-passion.jpg', 2, 10, 0, 'facile', 'refusee',
      'Précisez les quantités de mangue et de jus de passion. Détaillez les étapes de préparation et indiquez la quantité de boisson obtenue pour deux personnes.',
      '2026-11-20 12:00:00', NULL, '2026-11-21 09:00:00', 4, 2, 1),

  (6, 'Poulet rôti aux herbes',
      'Un poulet rôti parfumé au thym et au romarin, à servir en plat principal.',
      'poulet-roti-herbes.jpg', 4, 15, 60, 'moyenne', 'publiee', NULL,
      '2026-11-19 18:00:00', NULL, '2026-11-19 19:30:00', 2, 5, 1);

-- --------------------------------------------------
-- ETAPE
-- Les étapes sont groupées par recette ci-dessous (le dernier
-- chiffre de chaque ligne est l'id_recette concerné).
-- --------------------------------------------------

INSERT INTO ETAPE (titre, numero_ordre, description, id_recette) VALUES
  ('Préparez les légumes', 1, 'Lavez les tomates, le concombre et les feuilles de basilic. Séchez-les soigneusement.', 1),
  ('Découpez les légumes', 2, 'Coupez les tomates en quartiers et le concombre en demi-rondelles. Déposez-les dans un grand saladier.', 1),
  ('Ajoutez la feta', 3, 'Égouttez la feta, puis coupez-la en cubes ou émiettez-la délicatement sur les légumes.', 1),
  ('Incorporez les olives', 4, 'Ajoutez les olives noires et mélangez doucement pour conserver les morceaux de feta.', 1),
  ('Assaisonnez', 5, 'Arrosez d’huile d’olive. Salez et poivrez selon votre goût, puis mélangez pour bien répartir les saveurs.', 1),
  ('Servez et savourez', 6, 'Ajoutez les feuilles de basilic et servez la salade fraîche. Goûtez et ajustez l’assaisonnement si nécessaire.', 1),

  ('Préparer les légumes', 1, 'Épluchez les pommes de terre et les carottes. Coupez-les en petits morceaux.', 2),
  ('Cuire les légumes', 2, 'Faites cuire les pommes de terre et les carottes à l’eau jusqu’à ce qu’elles soient tendres.', 2),
  ('Préparer la farce', 3, 'Égouttez les légumes et écrasez-les grossièrement avec les petits pois.', 2),
  ('Garnir et plier', 4, 'Répartissez la farce sur les feuilles de brick et pliez-les en triangles.', 2),
  ('Faire dorer', 5, 'Badigeonnez les samoussas d’huile et faites-les dorer pendant 10 minutes.', 2),

  ('Préparer les ingrédients', 1, 'Sortez le beurre à température ambiante et préchauffez le four.', 3),
  ('Mélanger la pâte', 2, 'Mélangez la farine, le sucre, les œufs et le beurre fondu jusqu’à obtenir une pâte lisse.', 3),
  ('Ajouter le coco', 3, 'Incorporez la noix de coco râpée et le lait de coco à la préparation.', 3),
  ('Verser dans le moule', 4, 'Versez la pâte dans un moule beurré et égalisez la surface.', 3),
  ('Cuire au four', 5, 'Faites cuire au four jusqu’à ce que le gâteau soit doré.', 3),

  ('Préparer la pâte', 1, 'Étalez la pâte brisée dans un moule à tarte.', 4),
  ('Éplucher les pommes', 2, 'Épluchez et coupez les pommes en fines tranches.', 4),
  ('Garnir la pâte', 3, 'Disposez les tranches de pommes en rosace sur la pâte.', 4),
  ('Saupoudrer de cannelle', 4, 'Saupoudrez de sucre et de cannelle, puis ajoutez quelques noisettes de beurre.', 4),
  ('Cuire au four', 5, 'Faites cuire au four jusqu’à ce que la tarte soit dorée.', 4),

  ('Préparer la mangue', 1, 'Épluchez la mangue et coupez sa chair en morceaux.', 5),
  ('Préparer le jus de passion', 2, 'Coupez les fruits de la passion en deux et récupérez la pulpe.', 5),
  ('Mixer les ingrédients', 3, 'Mixez la mangue, la pulpe de passion et l’eau jusqu’à obtenir une texture lisse.', 5),
  ('Servir frais', 4, 'Versez le smoothie dans un verre et servez immédiatement.', 5),

  ('Préparer le poulet', 1, 'Sortez le poulet du réfrigérateur et préchauffez le four.', 6),
  ('Préparer les herbes', 2, 'Hachez le thym, le romarin et l’ail.', 6),
  ('Assaisonner', 3, 'Frottez le poulet avec les herbes, l’huile d’olive, le sel et le poivre.', 6),
  ('Rôtir au four', 4, 'Faites rôtir au four en arrosant régulièrement, jusqu’à ce que le poulet soit bien doré.', 6),
  ('Laisser reposer', 5, 'Laissez reposer le poulet quelques minutes avant de le découper.', 6);

-- --------------------------------------------------
-- INGREDIENT
-- Noms uniques, réutilisables d'une recette à l'autre
-- (ex. « Huile d'olive » et « Sel et poivre »).
-- --------------------------------------------------

INSERT INTO INGREDIENT (id_ingredient, nom) VALUES
  (1,  'Tomates'),
  (2,  'Concombre'),
  (3,  'Feta'),
  (4,  'Olives noires'),
  (5,  'Huile d’olive'),
  (6,  'Basilic'),
  (7,  'Feuilles de brick'),
  (8,  'Pommes de terre'),
  (9,  'Petits pois'),
  (10, 'Carottes'),
  (11, 'Sel et poivre'),
  (12, 'Noix de coco râpée'),
  (13, 'Farine'),
  (14, 'Sucre'),
  (15, 'Œufs'),
  (16, 'Lait de coco'),
  (17, 'Beurre'),
  (18, 'Pâte brisée'),
  (19, 'Pommes'),
  (20, 'Cannelle'),
  (21, 'Mangue'),
  (22, 'Jus de passion'),
  (23, 'Eau'),
  (24, 'Poulet entier'),
  (25, 'Thym'),
  (26, 'Romarin'),
  (27, 'Ail');

-- --------------------------------------------------
-- RECETTE_INGREDIENT
-- quantite et unite restent NULL pour les ingrédients
-- facultatifs (« à votre convenance »).
-- --------------------------------------------------

INSERT INTO RECETTE_INGREDIENT (id_recette, id_ingredient, quantite, unite) VALUES
  -- Salade méditerranéenne
  (1, 1, 4,    'pièce'),
  (1, 2, 1,    'pièce'),
  (1, 3, 150,  'g'),
  (1, 4, 80,   'g'),
  (1, 5, 3,    'c. à soupe'),
  (1, 6, NULL, NULL),

  -- Samoussas aux légumes
  (2, 7,  8,   'pièce'),
  (2, 8,  300, 'g'),
  (2, 9,  100, 'g'),
  (2, 10, 2,   'pièce'),
  (2, 5,  2,   'c. à soupe'),
  (2, 11, NULL, NULL),

  -- Gâteau au coco
  (3, 12, 150, 'g'),
  (3, 13, 200, 'g'),
  (3, 14, 150, 'g'),
  (3, 15, 3,   'pièce'),
  (3, 16, 200, 'ml'),
  (3, 17, 80,  'g'),

  -- Tarte aux pommes
  (4, 18, 1,   'pièce'),
  (4, 19, 5,   'pièce'),
  (4, 14, 60,  'g'),
  (4, 20, 1,   'c. à café'),
  (4, 17, 20,  'g'),

  -- Smoothie mangue passion
  (5, 21, 250, 'g'),
  (5, 22, 150, 'ml'),
  (5, 23, 100, 'ml'),

  -- Poulet rôti aux herbes
  (6, 24, 1,   'pièce'),
  (6, 25, 1,   'c. à soupe'),
  (6, 26, 1,   'c. à soupe'),
  (6, 27, 3,   'pièce'),
  (6, 5,  2,   'c. à soupe'),
  (6, 11, NULL, NULL);

-- --------------------------------------------------
-- AVIS
-- Un avis par ligne : id_utilisateur = qui a donné la note,
-- id_recette = la recette concernée.
-- --------------------------------------------------

INSERT INTO AVIS (note, commentaire, date_avis, id_utilisateur, id_recette) VALUES
  -- Salade méditerranéenne (2 avis)
  (5, 'Fraîche et facile à préparer.',                               '2026-11-23 09:00:00', 3, 1),
  (4, 'Simple et rapide, parfaite en été.',                          '2026-11-23 11:30:00', 2, 1),

  -- Samoussas aux légumes (2 avis)
  (3, 'La farce est bonne, mais je préfère une cuisson plus longue.', '2026-11-19 13:25:00', 7, 2),
  (4, 'Bien croustillants, la farce est savoureuse.',                 '2026-11-20 10:00:00', 4, 2),

  -- Gâteau au coco (2 avis)
  (5, 'Un dessert moelleux que je referai.',                         '2026-11-21 17:40:00', 6, 3),
  (5, 'Un délice, toute la famille a adoré.',                        '2026-11-22 08:15:00', 5, 3),

  -- Tarte aux pommes (2 avis)
  (4, 'Une belle recette, simple à suivre.',                         '2026-11-20 21:05:00', 4, 4),
  (5, 'Les pommes sont fondantes, très bonne recette.',               '2026-11-21 09:40:00', 7, 4),

  -- Smoothie mangue passion (2 avis)
  (4, 'Bien frais et fruité, parfait pour l’été.',                    '2026-11-20 16:00:00', 3, 5),
  (5, 'Rapide à préparer et vraiment délicieux.',                    '2026-11-21 12:20:00', 6, 5),

  -- Poulet rôti aux herbes (2 avis)
  (4, 'Très bonne recette, cuisson parfaite.',                       '2026-11-22 20:15:00', 5, 6),
  (5, 'Le poulet est parfaitement cuit, un régal.',                  '2026-11-23 19:00:00', 2, 6);

-- --------------------------------------------------
-- FAVORI
-- --------------------------------------------------

INSERT INTO FAVORI (id_utilisateur, id_recette, date_ajout) VALUES
  (2, 1, '2026-11-19 08:00:00'),
  (2, 6, '2026-11-20 08:00:00'),
  (3, 4, '2026-11-21 08:00:00');
