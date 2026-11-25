<?php

// Doit être tout en haut, avant le moindre affichage.
session_start();

// Page réservée aux membres connectés.
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

require_once __DIR__ . '/../../controleurs/public/FavorisControleur.php';

$controleur = new FavorisControleur();
$favoris = $controleur->afficher();

$pageActuelle = 'favoris';

// Traduction de la difficulté pour l'affichage.
$nomsDifficultes = [
    'facile' => 'Facile',
    'moyenne' => 'Moyenne',
    'difficile' => 'Difficile',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Mes favoris — Saveurs Maison</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="favorites-page">

  <?php require __DIR__ . '/../../layouts/public/navbar.php'; ?>

  <main class="fv-main">
    <div class="container">

      <!-- FIL D'ARIANE -->
      <nav class="fv-breadcrumb" aria-label="Fil d’Ariane">
        <ol>
          <li><a href="../public/accueil.php">Accueil</a></li>
          <li aria-current="page">Mes favoris</li>
        </ol>
      </nav>

      <!-- INTRODUCTION -->
      <section class="fv-intro" aria-labelledby="titre-favoris">

        <p class="fv-eyebrow">Mon carnet personnel</p>

        <h1 id="titre-favoris">
          Mes envies <em>à cuisiner.</em>
        </h1>

        <p class="fv-intro-text">
          Une recette à essayer, un dessert à refaire…
          Retrouvez ici vos idées gourmandes mises de côté.
        </p>

      </section>

      <!-- FAVORIS -->
      <section class="fv-collection" aria-labelledby="titre-collection">

        <div class="fv-collection-heading">

          <div class="fv-collection-title">
            <h2 id="titre-collection">Ma sélection gourmande</h2>

            <span class="fv-count">
              <?= count($favoris) ?> recette<?= count($favoris) > 1 ? 's' : '' ?>
            </span>
          </div>

          <a href="../public/recettes.php" class="fv-discover-link">
            Découvrir d’autres recettes
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </a>

        </div>

        <?php if (empty($favoris)): ?>

          <!-- ÉTAT VIDE -->
          <div class="fv-empty">
            <h3>Votre carnet attend ses premières recettes.</h3>

            <p>
              Explorez les recettes et cliquez sur un cœur
              pour retrouver vos coups de cœur ici.
            </p>

            <a href="../public/recettes.php" class="fv-button">
              Découvrir les recettes
            </a>
          </div>

        <?php else: ?>

          <div class="fv-list">

            <?php foreach ($favoris as $recette): ?>

              <?php
                $tempsTotal = $recette['temps_preparation'] + $recette['temps_cuisson'];
                $tempsAffiche = $tempsTotal >= 60
                    ? intdiv($tempsTotal, 60) . ' h' . ($tempsTotal % 60 > 0 ? ' ' . ($tempsTotal % 60) : '')
                    : $tempsTotal . ' min';

                $note = $recette['note_moyenne'] !== null ? round($recette['note_moyenne'], 1) : null;
              ?>

              <article class="fv-card">

                <a
                  href="../public/recette.php?id=<?= (int) $recette['id_recette'] ?>"
                  class="fv-card-image"
                  aria-label="Voir la recette <?= htmlspecialchars($recette['titre']) ?>"
                >
                  <img
                    src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
                    alt="<?= htmlspecialchars($recette['titre']) ?>"
                    loading="lazy"
                  >
                </a>

                <div class="fv-card-content">

                  <div class="fv-card-top">
                    <span class="fv-category">
                      <?= htmlspecialchars($recette['categorie']) ?>
                    </span>

                    <form method="post" action="basculer-favori.php">
                      <input type="hidden" name="id_recette" value="<?= (int) $recette['id_recette'] ?>">
                      <input type="hidden" name="retour" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                      <button
                        class="fv-remove"
                        type="submit"
                        aria-label="Retirer <?= htmlspecialchars($recette['titre']) ?> des favoris"
                      >
                        <i class="bi bi-heart-fill" aria-hidden="true"></i>
                      </button>
                    </form>
                  </div>

                  <h3>
                    <a href="../public/recette.php?id=<?= (int) $recette['id_recette'] ?>">
                      <?= htmlspecialchars($recette['titre']) ?>
                    </a>
                  </h3>

                  <p class="fv-description">
                    <?= htmlspecialchars($recette['description']) ?>
                  </p>

                  <div class="fv-card-bottom">

                    <ul class="fv-metadata" aria-label="Informations sur la recette">
                      <li>
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        <?= $tempsAffiche ?>
                      </li>

                      <li>
                        <i class="bi bi-speedometer2" aria-hidden="true"></i>
                        <?= $nomsDifficultes[$recette['difficulte']] ?? $recette['difficulte'] ?>
                      </li>

                      <?php if ($note !== null): ?>
                        <li>
                          <i class="bi bi-star-fill fv-star" aria-hidden="true"></i>
                          <span>
                            <span class="visually-hidden">Note : </span>
                            <?= str_replace('.', ',', $note) ?><span class="visually-hidden"> sur 5</span>
                          </span>
                        </li>
                      <?php endif; ?>
                    </ul>

                    <a href="../public/recette.php?id=<?= (int) $recette['id_recette'] ?>" class="fv-recipe-link">
                      Voir la recette
                      <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>

                  </div>

                </div>
              </article>

            <?php endforeach; ?>

          </div>

        <?php endif; ?>

      </section>

    </div>
  </main>

  <?php require __DIR__ . '/../../layouts/public/footer.php'; ?>

  <!-- Nécessaire pour le menu déroulant du compte. -->
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
