<?php

// Doit être tout en haut, avant le moindre affichage : nécessaire pour
// savoir si un membre est connecté (utilisé par la navbar partagée).
session_start();

require_once __DIR__ . '/../../controleurs/AccueilControleur.php';

$controleur = new AccueilControleur();
$donnees = $controleur->afficher();
$recettes = $donnees['recettes'];
$idsFavoris = $donnees['idsFavoris'];
$estConnecte = isset($_SESSION['utilisateur_id']);

$pageActuelle = 'accueil';

// Vient de InscriptionControleur, juste après la création du compte.
$bienvenue = isset($_GET['bienvenue']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Saveurs Maison — Accueil</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

  <!-- NAVIGATION -->
  <?php require __DIR__ . '/../../layouts/navbar.php'; ?>

  <main>

    <?php if ($bienvenue): ?>
      <div class="container">
        <div class="alert alert-success mt-3" role="alert">
          Bienvenue <?= htmlspecialchars($_SESSION['utilisateur_prenom']) ?> ! Votre compte a bien été créé, vous êtes maintenant connecté(e).
        </div>
      </div>
    <?php endif; ?>

    <!-- HERO -->
    <section class="hero-section">
      <div class="container">

        <div class="row align-items-center g-4 g-lg-5">

          <div class="col-lg-6">
            <p class="eyebrow">Le plaisir de cuisiner ensemble</p>

            <h1 class="hero-title">
              De belles recettes,<br>
              à partager <em>simplement.</em>
            </h1>

            <p class="hero-description">
              Découvrez des idées gourmandes et partagez vos recettes maison.
            </p>

            <form class="hero-search" action="recettes.php" method="get">
              <label for="recherche" class="visually-hidden">
                Rechercher une recette
              </label>

              <input
                type="search"
                id="recherche"
                name="recherche"
                placeholder="Quelle recette vous fait envie ?"
              >

              <button type="submit" aria-label="Rechercher">
                <i class="bi bi-search" aria-hidden="true"></i>
              </button>
            </form>

            <div class="hero-pills" aria-label="Inspirations culinaires">
              <span>Rapide</span>
              <span>Facile</span>
              <span>Gourmand</span>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="hero-visual">

              <img
                src="../../assets/images/hero.jpg"
                alt="Couscous aux légumes rôtis, pois chiches et herbes fraîches"
                class="hero-image"
                fetchpriority="high"
              >

              <div class="hero-sticker">
                Fait maison,<br>
                avec passion
              </div>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- CATÉGORIES -->
    <section class="categories-section" aria-label="Catégories de recettes">
      <div class="container">

        <div class="row g-3">

          <div class="col-6 col-lg-3">
            <a href="recettes.php?categorie=1" class="category-card">
              <img
                src="../../assets/images/salade-mediterraneenne.jpg"
                alt=""
                loading="lazy"
              >
              <span>Entrées</span>
            </a>
          </div>

          <div class="col-6 col-lg-3">
            <a href="recettes.php?categorie=2" class="category-card">
              <img
                src="../../assets/images/poulet-roti-herbes.jpg"
                alt=""
                loading="lazy"
              >
              <span>Plats</span>
            </a>
          </div>

          <div class="col-6 col-lg-3">
            <a href="recettes.php?categorie=3" class="category-card">
              <img
                src="../../assets/images/gateau-coco.jpg"
                alt=""
                loading="lazy"
              >
              <span>Desserts</span>
            </a>
          </div>

          <div class="col-6 col-lg-3">
            <a href="recettes.php?categorie=4" class="category-card">
              <img
                src="../../assets/images/smoothie-mangue-passion.jpg"
                alt=""
                loading="lazy"
              >
              <span>Boissons</span>
            </a>
          </div>

        </div>

      </div>
    </section>

    <!-- À PROPOS -->
    <section id="a-propos" class="about-section section-space">
      <div class="container">

        <div class="row align-items-center g-4 g-lg-5">

          <div class="col-lg-6">
            <img
              src="../../assets/images/a-propos-cuisine.jpg"
              alt="Préparation de légumes frais dans une cuisine"
              class="about-image"
              loading="lazy"
            >
          </div>

          <div class="col-lg-6 about-content">

            <h2 class="section-title">À propos de Saveurs Maison</h2>

            <p>
              Une cuisine généreuse, des recettes accessibles et le plaisir
              de partager. Saveurs Maison réunit les passionnés autour
              des saveurs du quotidien.
            </p>

            <a href="a-propos.php" class="text-link">
              En savoir plus <span aria-hidden="true">→</span>
            </a>

          </div>

        </div>

      </div>
    </section>

    <!-- COMMENT ÇA MARCHE -->
    <section class="how-section section-space">
      <div class="container">

        <h2 class="section-title">Comment ça marche ?</h2>

        <div class="row g-4 steps-row">

          <div class="col-md-4">
            <div class="step">

              <div class="step-icon">
                <i class="bi bi-search" aria-hidden="true"></i>
              </div>

              <h3 class="step-heading">
                <span>01</span> Découvrez
              </h3>

              <p>Trouvez une recette selon vos envies.</p>

            </div>
          </div>

          <div class="col-md-4">
            <div class="step step-bordered">

              <div class="step-icon">
                <svg
                  viewBox="0 0 64 64"
                  fill="none"
                  aria-hidden="true"
                >
                  <path d="M19 38c-7 0-12-5-12-11s5-11 11-11c2-7 8-11 14-11s12 4 14 11c6 0 11 5 11 11s-5 11-12 11v17H19V38Z"/>
                  <path d="M19 46h26M26 36v8M38 36v8"/>
                </svg>
              </div>

              <h3 class="step-heading">
                <span>02</span> Cuisinez
              </h3>

              <p>Suivez les ingrédients et les étapes.</p>

            </div>
          </div>

          <div class="col-md-4">
            <div class="step step-bordered">

              <div class="step-icon">
                <i class="bi bi-people" aria-hidden="true"></i>
              </div>

              <h3 class="step-heading">
                <span>03</span> Partagez
              </h3>

              <p>Publiez vos recettes et donnez votre avis.</p>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- DERNIÈRES RECETTES -->
    <section id="recettes" class="recipes-section section-space">
      <div class="container">

        <div class="section-heading">
          <h2 class="section-title">Les dernières recettes</h2>

          <a href="recettes.php" class="text-link">
            Toutes les recettes <span aria-hidden="true">→</span>
          </a>
        </div>

        <div class="row g-4">

          <?php foreach ($recettes as $index => $recette): ?>

            <?php
              // Temps total affiché : préparation + cuisson.
              $tempsTotal = $recette['temps_preparation'] + $recette['temps_cuisson'];

              if ($tempsTotal >= 60) {
                  $heures = intdiv($tempsTotal, 60);
                  $minutes = $tempsTotal % 60;
                  $tempsAffiche = $minutes > 0 ? $heures . " h " . $minutes : $heures . " h";
              } else {
                  $tempsAffiche = $tempsTotal . " min";
              }

              // Traduction de la difficulté pour l'affichage.
              $difficultes = [
                  "facile" => "Facile",
                  "moyenne" => "Moyen",
                  "difficile" => "Difficile",
              ];
              $difficulteAffichee = $difficultes[$recette['difficulte']] ?? $recette['difficulte'];

              // Étoiles pleines selon la note moyenne arrondie (sur 5).
              $noteArrondie = $recette['note_moyenne'] !== null ? (int) round($recette['note_moyenne']) : 0;
              $etoiles = str_repeat("★", $noteArrondie) . str_repeat("☆", 5 - $noteArrondie);

              // Couleur du badge de catégorie, en alternance.
              $classeTag = $index % 2 === 0 ? "tag-sage" : "tag-peach";
            ?>

            <div class="col-md-6 col-lg-4">
              <article class="recipe-card">

                <div class="recipe-image-wrap">
                  <a href="recette.php?id=<?= (int) $recette['id_recette'] ?>">
                    <img
                      src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
                      alt="<?= htmlspecialchars($recette['titre']) ?>"
                      loading="lazy"
                    >
                  </a>

                  <?php if ($estConnecte): ?>

                    <?php $estFavori = in_array($recette['id_recette'], $idsFavoris); ?>

                    <form method="post" action="../membre/basculer-favori.php" class="favorite-form">
                      <input type="hidden" name="id_recette" value="<?= (int) $recette['id_recette'] ?>">
                      <input type="hidden" name="retour" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                      <button
                        type="submit"
                        class="favorite-button"
                        aria-label="<?= $estFavori ? 'Retirer' : 'Ajouter' ?> <?= htmlspecialchars($recette['titre']) ?> des favoris"
                      >
                        <i class="bi bi-heart<?= $estFavori ? '-fill' : '' ?>" aria-hidden="true"></i>
                      </button>
                    </form>

                  <?php else: ?>

                    <a
                      href="connexion.php"
                      class="favorite-button"
                      aria-label="Se connecter pour enregistrer cette recette en favori"
                    >
                      <i class="bi bi-heart" aria-hidden="true"></i>
                    </a>

                  <?php endif; ?>
                </div>

                <div class="recipe-content">
                  <span class="recipe-tag <?= $classeTag ?>">
                    <?= htmlspecialchars($recette['categorie']) ?>
                  </span>

                  <h3 class="recipe-title">
                    <a href="recette.php?id=<?= (int) $recette['id_recette'] ?>">
                      <?= htmlspecialchars($recette['titre']) ?>
                    </a>
                  </h3>

                  <div class="recipe-meta">
                    <span>
                      <i class="bi bi-clock" aria-hidden="true"></i> <?= $tempsAffiche ?>
                    </span>

                    <span class="difficulty">
                      <i class="bi bi-egg-fried" aria-hidden="true"></i> <?= $difficulteAffichee ?>
                    </span>

                    <span class="stars" aria-label="Note : <?= $noteArrondie ?> sur 5">
                      <?= $etoiles ?>
                    </span>
                  </div>
                </div>

              </article>
            </div>

          <?php endforeach; ?>

        </div>

      </div>
    </section>

    <!-- PARTAGER UNE RECETTE -->
    <section class="share-section section-space">
      <div class="container">

        <div class="share-banner">

          <div class="share-visual" aria-hidden="true"></div>

          <div class="share-content">
            <h2>Votre recette mérite d’être partagée.</h2>

            <p>
              Rejoignez notre communauté et inspirez les cuisiniers du quotidien.
            </p>

            <a href="<?= $estConnecte ? '../membre/proposer-recette.php' : 'inscription.php' ?>" class="btn-green">
              Partager une recette
            </a>
          </div>

        </div>

      </div>
    </section>

  </main>

  <?php require __DIR__ . '/../../layouts/footer.php'; ?>

  <!-- Nécessaire pour le menu déroulant du compte (navbar membre). -->
  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Garde la position de défilement lors d'un ajout/retrait de favori
    // (sinon la page revient en haut après le rechargement).
    document.querySelectorAll('form[action*="basculer-favori.php"]').forEach(function (formulaire) {
      formulaire.addEventListener("submit", function () {
        sessionStorage.setItem("positionAvantFavori", window.scrollY);
      });
    });

    var positionMemorisee = sessionStorage.getItem("positionAvantFavori");

    if (positionMemorisee !== null) {
      window.scrollTo(0, parseInt(positionMemorisee, 10));
      sessionStorage.removeItem("positionAvantFavori");
    }
  </script>

</body>
</html>
