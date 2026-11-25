<?php

/*
 * Menu latéral et barre du haut, communs à toutes les pages admin.
 * Variables à définir avant d'inclure ce fichier :
 * - $pageActuelle : 'dashboard', 'recettes', 'categories', 'membres' ou 'avis'
 * - $titrePage : le texte affiché dans le fil d'Ariane
 *
 * La page qui inclut ce fichier doit avoir démarré la session et
 * déjà vérifié que la personne connectée est bien un administrateur.
 */

require_once __DIR__ . '/../../modeles/Recette.php';

// Nombre de recettes en attente, affiché en badge sur le lien "Recettes".
$nombreEnAttente = Recette::compterParStatut()['en_attente'];
?>
<!-- MENU LATÉRAL -->
<aside
  class="offcanvas-lg offcanvas-start ad-sidebar"
  tabindex="-1"
  id="menuAdmin"
  aria-labelledby="titre-menu-admin"
>

  <div class="offcanvas-header ad-mobile-heading">
    <h2 class="offcanvas-title" id="titre-menu-admin">Administration</h2>

    <button
      type="button"
      class="btn-close btn-close-white"
      data-bs-dismiss="offcanvas"
      data-bs-target="#menuAdmin"
      aria-label="Fermer le menu"
    ></button>
  </div>

  <div class="offcanvas-body ad-sidebar-body">

    <a href="dashboard.php" class="ad-brand">
      <img src="../../assets/images/logo.png" alt="Saveurs Maison">
    </a>

    <p class="ad-sidebar-label">Administration</p>

    <nav class="ad-navigation" aria-label="Navigation administration">

      <a
        href="dashboard.php"
        class="ad-nav-link <?= $pageActuelle === 'dashboard' ? 'active' : '' ?>"
        <?= $pageActuelle === 'dashboard' ? 'aria-current="page"' : '' ?>
      >
        <i class="bi bi-house-door" aria-hidden="true"></i>
        <span>Tableau de bord</span>
      </a>

      <a
        href="recettes.php"
        class="ad-nav-link <?= $pageActuelle === 'recettes' ? 'active' : '' ?>"
        <?= $pageActuelle === 'recettes' ? 'aria-current="page"' : '' ?>
      >
        <i class="bi bi-journal-text" aria-hidden="true"></i>
        <span>Recettes</span>
        <?php if ($nombreEnAttente > 0): ?>
          <span class="ad-nav-badge">
            <span class="visually-hidden">Recettes en attente : </span>
            <?= $nombreEnAttente ?>
          </span>
        <?php endif; ?>
      </a>

      <a
        href="categories.php"
        class="ad-nav-link <?= $pageActuelle === 'categories' ? 'active' : '' ?>"
        <?= $pageActuelle === 'categories' ? 'aria-current="page"' : '' ?>
      >
        <i class="bi bi-tag" aria-hidden="true"></i>
        <span>Catégories</span>
      </a>

      <a
        href="membres.php"
        class="ad-nav-link <?= $pageActuelle === 'membres' ? 'active' : '' ?>"
        <?= $pageActuelle === 'membres' ? 'aria-current="page"' : '' ?>
      >
        <i class="bi bi-people" aria-hidden="true"></i>
        <span>Membres</span>
      </a>

      <a
        href="avis.php"
        class="ad-nav-link <?= $pageActuelle === 'avis' ? 'active' : '' ?>"
        <?= $pageActuelle === 'avis' ? 'aria-current="page"' : '' ?>
      >
        <i class="bi bi-chat-left" aria-hidden="true"></i>
        <span>Avis</span>
      </a>

    </nav>

    <div class="ad-sidebar-bottom">

      <a href="../public/accueil.php" class="ad-nav-link">
        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
        <span>Voir le site</span>
      </a>

      <a href="../public/deconnexion.php" class="ad-nav-link ad-logout">
        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
        <span>Se déconnecter</span>
      </a>

    </div>

  </div>
</aside>

<!-- PARTIE PRINCIPALE -->
<div class="ad-main">

  <!-- BARRE SUPÉRIEURE -->
  <header class="ad-topbar">

    <div class="ad-topbar-left">

      <button
        class="ad-menu-button d-lg-none"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#menuAdmin"
        aria-controls="menuAdmin"
        aria-label="Ouvrir le menu administration"
      >
        <i class="bi bi-list" aria-hidden="true"></i>
      </button>

      <nav class="ad-breadcrumb" aria-label="Fil d’Ariane">
        <ol>
          <li>Administration</li>
          <li aria-current="page"><?= htmlspecialchars($titrePage) ?></li>
        </ol>
      </nav>

    </div>

    <div class="dropdown ad-account">

      <button
        class="ad-account-button"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
      >
        <span class="ad-avatar" aria-hidden="true">
          <?= strtoupper(mb_substr($_SESSION['utilisateur_prenom'], 0, 1)) ?>
        </span>
        <span class="ad-account-name">
          <?= htmlspecialchars($_SESSION['utilisateur_prenom']) ?>
        </span>
        <i class="bi bi-chevron-down" aria-hidden="true"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="../public/accueil.php">
            Voir le site
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="../public/deconnexion.php">
            Déconnexion
          </a>
        </li>
      </ul>

    </div>

  </header>

  <main class="ad-content">
