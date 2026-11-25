  <?php
    $estConnecteFooter = isset($_SESSION['utilisateur_id']);
    $estAdminFooter = $estConnecteFooter && $_SESSION['utilisateur_role'] === 'administrateur';
  ?>

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="container">

      <div class="row g-4 footer-main">

        <div class="col-12 col-md-6 col-lg-4 footer-brand">
          <a href="../public/accueil.php">
            <img
              src="../../assets/images/logo.png"
              alt="Saveurs Maison"
              class="footer-logo"
              loading="lazy"
            >
          </a>

          <p class="footer-tagline">
            La cuisine se savoure,<br>
            les recettes se partagent.
          </p>
        </div>

        <div class="col-12 col-sm-6 col-md-3 col-lg-2 footer-column">
          <h3>Explorer</h3>

          <a href="../public/recettes.php">Toutes les recettes</a>
          <a href="../public/recettes.php?categorie=1">Entrées</a>
          <a href="../public/recettes.php?categorie=2">Plats</a>
          <a href="../public/recettes.php?categorie=3">Desserts</a>
          <a href="../public/recettes.php?categorie=4">Boissons</a>
        </div>

        <div class="col-12 col-sm-6 col-md-3 col-lg-3 footer-column">
          <h3>Participer</h3>

          <?php if ($estAdminFooter): ?>
            <a href="../admin/dashboard.php">Tableau de bord</a>
          <?php elseif ($estConnecteFooter): ?>
            <a href="../membre/mes-recettes.php">Mes recettes</a>
            <a href="../membre/proposer-recette.php">Partager une recette</a>
          <?php else: ?>
            <a href="../public/inscription.php">Créer un compte</a>
            <a href="../public/connexion.php">Connexion</a>
            <a href="../public/inscription.php">Partager une recette</a>
          <?php endif; ?>
        </div>

        <div class="col-12 col-md-6 col-lg-3 footer-column">
          <h3>Informations</h3>

          <a href="../public/a-propos.php">À propos</a>
        </div>

      </div>

      <div class="footer-bottom">
        <span>© Saveurs Maison</span>

        <span class="footer-bottom-tagline">
          La cuisine se savoure, les recettes se partagent.
        </span>
      </div>

    </div>
  </footer>
