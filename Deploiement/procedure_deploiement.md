# Procédure de déploiement

Étapes à suivre pour mettre Saveurs-Maison en ligne sur un serveur d'hébergement.

1. **Vérifier les prérequis du serveur**
   PHP 8 ou plus, avec l'extension PDO (pdo_mysql) activée, et l'upload de fichiers autorisé (pour les photos de recettes).

2. **Copier les fichiers**
   Déposer le dossier `app/` sur le serveur (par FTP ou SFTP). C'est le seul dossier à héberger : les autres dossiers du projet (`Analyse/`, `Conception/`, `Tests/`...) sont des documents de travail, pas du code à mettre en ligne.

3. **Créer et préparer la base de données**
   Créer une base de données vide sur le serveur, puis importer `app/base_de_donnees/script_creation.sql` pour créer les tables. Importer aussi `app/base_de_donnees/script_insertion.sql` s'il faut un jeu de données de départ.

4. **Modifier la configuration**
   Dans `app/config/connexion.php`, remplacer les 4 identifiants par ceux du serveur :
   - `$hote` (adresse de la base de données)
   - `$nomBase` (nom de la base créée à l'étape 3)
   - `$utilisateur` (utilisateur MySQL fourni par l'hébergeur)
   - `$motDePasse` (mot de passe fourni par l'hébergeur)

5. **Tester la connexion**
   Ouvrir la page d'accueil : les recettes doivent s'afficher. Si une erreur de connexion apparaît, revérifier l'étape 4.

6. **Vérifier les pages principales**
   Inscription, connexion, ajout d'une recette avec photo, et accès refusé sur les pages admin pour un simple membre.

En cas de problème, voir `plan_rollback.md`.
