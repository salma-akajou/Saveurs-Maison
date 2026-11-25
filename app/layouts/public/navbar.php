<?php
/*
 * Navbar commune à toutes les pages publiques.
 * La variable $pageActuelle doit être définie avant d'inclure ce fichier
 * (ex. 'accueil', 'a-propos', 'recettes'...), pour savoir quel lien
 * mettre en surbrillance.
 *
 * La page qui inclut ce fichier doit avoir démarré la session
 * (session_start()) avant d'afficher le moindre HTML : c'est ce qui
 * permet de savoir ici si un membre est connecté, et donc quelle
 * navbar afficher.
 */

$estConnecte = isset($_SESSION['utilisateur_id']);
?>

<?php if (!$estConnecte): ?>

  <!-- NAVBAR VISITEUR -->
  <header class="site-header">
    <div class="container header-inner">

      <a href="../public/accueil.php" class="brand" aria-label="Saveurs Maison — Accueil">
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
          href="../public/accueil.php"
          class="<?= $pageActuelle === 'accueil' ? 'active' : '' ?>"
          <?= $pageActuelle === 'accueil' ? 'aria-current="page"' : '' ?>
        >
          Accueil
        </a>

        <a
          href="<?= $pageActuelle === 'accueil' ? '#a-propos' : '../public/a-propos.php' ?>"
          class="<?= $pageActuelle === 'a-propos' ? 'active' : '' ?>"
          <?= $pageActuelle === 'a-propos' ? 'aria-current="page"' : '' ?>
        >
          À propos
        </a>

        <a
          href="../public/recettes.php"
          class="<?= $pageActuelle === 'recettes' ? 'active' : '' ?>"
          <?= $pageActuelle === 'recettes' ? 'aria-current="page"' : '' ?>
        >
          Les recettes
        </a>
      </nav>

      <div class="header-actions">
        <a href="../public/connexion.php" class="login-link">Connexion</a>
        <a href="../public/inscription.php" class="btn-green">Créer un compte</a>
      </div>

    </div>
  </header>

<?php else: ?>

  <!-- NAVBAR MEMBRE -->
  <header class="fv-header">
    <nav class="navbar navbar-expand-lg" aria-label="Navigation principale">
      <div class="container">

        <a class="navbar-brand fv-brand" href="../public/accueil.php">
          <img
            src="../../assets/images/logo.png"
            alt="Saveurs Maison — Accueil"
          >
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navigationMembre"
          aria-controls="navigationMembre"
          aria-expanded="false"
          aria-label="Ouvrir le menu"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navigationMembre">

          <ul class="navbar-nav ms-auto fv-nav-links">
            <li class="nav-item">
              <a
                class="nav-link <?= $pageActuelle === 'accueil' ? 'active' : '' ?>"
                href="../public/accueil.php"
                <?= $pageActuelle === 'accueil' ? 'aria-current="page"' : '' ?>
              >
                Accueil
              </a>
            </li>

            <li class="nav-item">
              <a
                class="nav-link <?= $pageActuelle === 'a-propos' ? 'active' : '' ?>"
                href="../public/a-propos.php"
                <?= $pageActuelle === 'a-propos' ? 'aria-current="page"' : '' ?>
              >
                À propos
              </a>
            </li>

            <li class="nav-item">
              <a
                class="nav-link <?= $pageActuelle === 'recettes' ? 'active' : '' ?>"
                href="../public/recettes.php"
                <?= $pageActuelle === 'recettes' ? 'aria-current="page"' : '' ?>
              >
                Les recettes
              </a>
            </li>

            <li class="nav-item">
              <a
                class="nav-link <?= $pageActuelle === 'favoris' ? 'active' : '' ?>"
                href="../membre/favoris.php"
                <?= $pageActuelle === 'favoris' ? 'aria-current="page"' : '' ?>
              >
                Mes favoris
              </a>
            </li>
          </ul>

          <div class="dropdown fv-account">

            <button
              class="fv-account-button"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <span class="fv-avatar" aria-hidden="true">
                <?= strtoupper(mb_substr($_SESSION['utilisateur_prenom'], 0, 1)) ?>
              </span>
              <span class="fv-account-name">
                <?= htmlspecialchars($_SESSION['utilisateur_prenom']) ?>
              </span>
              <i class="bi bi-chevron-down fv-account-chevron" aria-hidden="true"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="../membre/mes-recettes.php">
                  Mes recettes
                </a>
              </li>

              <li>
                <a class="dropdown-item" href="../membre/profil.php">
                  Mon profil
                </a>
              </li>

              <li><hr class="dropdown-divider"></li>

              <li>
                <a class="dropdown-item" href="../public/deconnexion.php">
                  <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                  Déconnexion
                </a>
              </li>
            </ul>

          </div>

        </div>
      </div>
    </nav>
  </header>

<?php endif; ?>
