<?php
/*
 * Navbar commune à toutes les pages publiques.
 * La variable $pageActuelle doit être définie avant d'inclure ce fichier
 * (ex. 'accueil', 'a-propos', 'recettes'...), pour savoir quel lien
 * mettre en surbrillance.
 *
 * Pour l'instant, personne ne peut encore se connecter (les sessions
 * ne sont pas encore développées) : tout le monde voit donc la navbar
 * visiteur, quelle que soit la page. Quand la connexion existera, ce
 * fichier affichera la navbar membre si $_SESSION contient un compte
 * connecté — ce sera donc le seul endroit à modifier.
 */
?>
<header class="site-header">
  <div class="container header-inner">

    <a href="accueil.php" class="brand" aria-label="Saveurs Maison — Accueil">
      <img
        src="../../assets/images/logo.png"
        alt="Saveurs Maison"
        class="site-logo"
      >
    </a>

    <input type="checkbox" id="menu-toggle" class="menu-toggle-input">
    <label for="menu-toggle" class="menu-toggle" aria-label="Ouvrir le menu">
      <i class="bi bi-list" aria-hidden="true"></i>
      <i class="bi bi-x" aria-hidden="true"></i>
    </label>

    <nav class="main-nav" aria-label="Navigation principale">
      <a
        href="accueil.php"
        class="<?= $pageActuelle === 'accueil' ? 'active' : '' ?>"
        <?= $pageActuelle === 'accueil' ? 'aria-current="page"' : '' ?>
      >
        Accueil
      </a>

      <a
        href="<?= $pageActuelle === 'accueil' ? '#a-propos' : 'a-propos.php' ?>"
        class="<?= $pageActuelle === 'a-propos' ? 'active' : '' ?>"
        <?= $pageActuelle === 'a-propos' ? 'aria-current="page"' : '' ?>
      >
        À propos
      </a>

      <a
        href="recettes.php"
        class="<?= $pageActuelle === 'recettes' ? 'active' : '' ?>"
        <?= $pageActuelle === 'recettes' ? 'aria-current="page"' : '' ?>
      >
        Les recettes
      </a>
    </nav>

    <div class="header-actions">
      <a href="connexion.php" class="login-link">Connexion</a>
      <a href="inscription.php" class="btn-green">Créer un compte</a>
    </div>

  </div>
</header>
