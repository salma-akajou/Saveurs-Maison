<?php $pageActuelle = 'a-propos'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>À propos — Saveurs Maison</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="about-page">

  <!-- NAVBAR -->
  <?php require __DIR__ . '/../../layouts/navbar.php'; ?>

  <main>

    <div class="container">

      <!-- FIL D'ARIANE -->
      <nav class="ap-breadcrumb" aria-label="Fil d’Ariane">
        <ol>
          <li><a href="accueil.php">Accueil</a></li>
          <li aria-current="page">À propos</li>
        </ol>
      </nav>

      <!-- INTRODUCTION -->
      <section class="ap-intro" aria-labelledby="titre-a-propos">

        <p class="ap-eyebrow">
          Notre ingrédient essentiel : le partage
        </p>

        <h1 id="titre-a-propos">
          La cuisine se savoure,<br>
          <em>les recettes se partagent.</em>
        </h1>

        <p class="ap-intro-description">
          Un lieu pour découvrir des recettes maison et transmettre
          le plaisir de cuisiner.
        </p>

      </section>

      <!-- PHOTO PANORAMIQUE -->
      <div class="ap-cover">
        <img
          src="../../assets/images/a-propos-cuisine.jpg"
          alt="Préparation d’une recette maison avec des légumes frais"
          fetchpriority="high"
        >
      </div>

      <!-- NOTRE HISTOIRE -->
      <section class="ap-story" aria-labelledby="titre-histoire">

        <div>
          <p class="ap-eyebrow">L’esprit Saveurs Maison</p>

          <h2 id="titre-histoire">
            Une idée simple,<br>
            une cuisine généreuse.
          </h2>
        </div>

        <div class="ap-story-text">
          <p>
            Saveurs Maison rassemble celles et ceux qui aiment cuisiner
            et partager leurs découvertes. Des recettes accessibles,
            des ingrédients du quotidien et des étapes claires
            pour se lancer à son rythme.
          </p>

          <p>
            Chacun peut trouver une idée, enregistrer ses favoris et
            proposer sa propre recette. Les publications sont vérifiées
            avant leur mise en ligne pour garder un contenu soigné.
          </p>
        </div>

      </section>

      <!-- NOS VALEURS -->
      <section class="ap-values" aria-labelledby="titre-valeurs">

        <div class="ap-values-heading">
          <p class="ap-eyebrow">Des saveurs, des liens</p>
          <h2 id="titre-valeurs">Ce qui nous rassemble</h2>
        </div>

        <div class="row g-0">

          <div class="col-md-4">
            <article class="ap-value">

              <div class="ap-value-icon">
                <i class="bi bi-house-heart" aria-hidden="true"></i>
              </div>

              <h3>Le fait maison</h3>

              <p>
                Le plaisir de préparer des plats à son goût,
                avec des ingrédients que l’on connaît.
              </p>

            </article>
          </div>

          <div class="col-md-4">
            <article class="ap-value">

              <div class="ap-value-icon">
                <i class="bi bi-people" aria-hidden="true"></i>
              </div>

              <h3>Le partage</h3>

              <p>
                Des idées qui se transmettent, se réinventent
                et donnent envie de passer en cuisine.
              </p>

            </article>
          </div>

          <div class="col-md-4">
            <article class="ap-value">

              <div class="ap-value-icon">
                <i class="bi bi-flower1" aria-hidden="true"></i>
              </div>

              <h3>La simplicité</h3>

              <p>
                Des recettes claires, à la portée de chacun,
                pour cuisiner avec plaisir au quotidien.
              </p>

            </article>
          </div>

        </div>

      </section>

      <!-- INVITATION -->
      <section class="ap-invitation" aria-labelledby="titre-invitation">

        <span class="ap-invitation-icon" aria-hidden="true">
          <i class="bi bi-heart"></i>
        </span>

        <h2 id="titre-invitation">
          Et si votre prochaine recette<br class="d-none d-md-block">
          inspirait quelqu’un ?
        </h2>

        <p>
          Rejoignez Saveurs Maison et partagez, vous aussi,
          le plaisir de cuisiner.
        </p>

        <div class="ap-invitation-actions">

          <a href="inscription.php" class="ap-button ap-button-primary">
            Créer un compte
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </a>

          <a href="recettes.php" class="ap-button ap-button-outline">
            Découvrir les recettes
          </a>

        </div>

      </section>

    </div>
  </main>

  <?php require __DIR__ . '/../../layouts/footer.php'; ?>

</body>
</html>
