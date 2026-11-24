<?php

// Doit être tout en haut, avant le moindre affichage.
session_start();

require_once __DIR__ . '/../../controleurs/RecettesControleur.php';

$controleur = new RecettesControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'recettes';

// Noms des catégories, pour l'affichage (les id correspondent à CATEGORIE).
$nomsCategories = [
    1 => 'Entrée',
    2 => 'Plat',
    3 => 'Dessert',
    4 => 'Boisson',
];

// Traduction de la difficulté pour l'affichage des étiquettes et du filtre.
$nomsDifficultes = [
    'facile' => 'Facile',
    'moyenne' => 'Moyenne',
    'difficile' => 'Difficile',
];

// Construit un lien vers une autre page de résultats, en conservant
// les filtres actuellement choisis (recherche, catégorie, difficulté).
function construireLienPage(int $numeroPage, array $donnees)
{
    $parametres = [
        'recherche' => $donnees['recherche'],
        'categorie' => $donnees['idCategorie'],
        'difficulte' => $donnees['difficulte'],
        'page' => $numeroPage,
    ];

    // On retire les valeurs vides pour garder une URL propre.
    $parametres = array_filter($parametres, function ($valeur) {
        return $valeur !== null && $valeur !== '';
    });

    return 'recettes.php?' . http_build_query($parametres) . '#resultats';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Saveurs Maison — Les recettes</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="catalog-page">

  <?php require __DIR__ . '/../../layouts/navbar.php'; ?>

  <main>

    <!-- PRÉSENTATION -->
    <section class="catalog-intro">
      <div class="container">

        <nav class="catalog-breadcrumb" aria-label="Fil d’Ariane">
          <a href="accueil.php">Accueil</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Les recettes</span>
        </nav>

        <div class="catalog-intro-layout">

          <div class="catalog-intro-content">
            <p class="eyebrow">Le carnet gourmand</p>

            <h1>
              À chaque envie,<br class="catalog-mobile-break">
              <em>sa recette.</em>
            </h1>

            <p class="catalog-intro-description">
              Des idées maison à découvrir, cuisiner et partager.
            </p>
          </div>

          <div class="catalog-decoration" aria-hidden="true">
            <img
              src="../../assets/images/salade-mediterraneenne.jpg"
              alt=""
            >
          </div>

        </div>

      </div>
    </section>

    <!-- RECHERCHE ET FILTRES -->
    <section class="catalog-filters-section" aria-label="Rechercher et filtrer">
      <div class="container">

        <form class="catalog-filters" method="get" action="recettes.php#resultats" id="formulaire-filtres">

          <div class="catalog-search">
            <label for="recherche" class="visually-hidden">
              Rechercher une recette par son nom
            </label>

            <i class="bi bi-search" aria-hidden="true"></i>

            <input
              type="search"
              id="recherche"
              name="recherche"
              placeholder="Rechercher une recette..."
              autocomplete="off"
              value="<?= htmlspecialchars($donnees['recherche']) ?>"
            >
          </div>

          <div class="catalog-filter">
            <label for="categorie">Catégorie</label>

            <select id="categorie" name="categorie" class="form-select">
              <option value="">Toutes</option>

              <?php foreach ($nomsCategories as $id => $nom): ?>
                <option
                  value="<?= $id ?>"
                  <?= $donnees['idCategorie'] === $id ? 'selected' : '' ?>
                >
                  <?= htmlspecialchars($nom) ?>s
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="catalog-filter">
            <label for="difficulte">Difficulté</label>

            <select id="difficulte" name="difficulte" class="form-select">
              <option value="">Toutes</option>

              <?php foreach ($nomsDifficultes as $valeur => $nom): ?>
                <option
                  value="<?= $valeur ?>"
                  <?= $donnees['difficulte'] === $valeur ? 'selected' : '' ?>
                >
                  <?= htmlspecialchars($nom) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </form>

      </div>
    </section>

    <!-- RÉSULTATS -->
    <section class="catalog-results-section" id="resultats">
      <div class="container">

        <div class="catalog-results-header">

          <div class="catalog-heading-group">
            <h2>Toutes les recettes</h2>

            <span class="catalog-count">
              <?= $donnees['total'] ?> recette<?= $donnees['total'] > 1 ? 's' : '' ?>
            </span>
          </div>

        </div>

        <?php if ($donnees['total'] === 0): ?>

          <!-- AUCUN RÉSULTAT -->
          <div class="catalog-empty">
            <i class="bi bi-search" aria-hidden="true"></i>

            <h3>Aucune recette trouvée</h3>

            <p>
              Essayez un autre nom ou modifiez les filtres.
            </p>

            <a href="recettes.php" class="btn-green">
              Réinitialiser la recherche
            </a>
          </div>

        <?php else: ?>

          <div class="catalog-grid">

            <?php foreach ($donnees['recettes'] as $recette): ?>

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

                $note = $recette['note_moyenne'] !== null ? round($recette['note_moyenne'], 1) : null;
              ?>

              <article class="catalog-card">

                <a
                  href="recette.php?id=<?= (int) $recette['id_recette'] ?>"
                  class="catalog-card-image"
                  aria-label="Voir la recette : <?= htmlspecialchars($recette['titre']) ?>"
                >
                  <img
                    src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
                    alt="<?= htmlspecialchars($recette['titre']) ?>"
                    loading="lazy"
                  >
                </a>

                <div class="catalog-card-content">

                  <div class="catalog-card-top">
                    <span class="catalog-card-tag">
                      <?= htmlspecialchars($recette['categorie']) ?>
                    </span>

                    <?php if (isset($_SESSION['utilisateur_id'])): ?>

                      <?php $estFavori = in_array($recette['id_recette'], $donnees['idsFavoris']); ?>

                      <form method="post" action="../membre/basculer-favori.php" class="catalog-heart-form">
                        <input type="hidden" name="id_recette" value="<?= (int) $recette['id_recette'] ?>">
                        <input type="hidden" name="retour" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                        <button
                          type="submit"
                          class="catalog-heart"
                          aria-label="<?= $estFavori ? 'Retirer' : 'Ajouter' ?> <?= htmlspecialchars($recette['titre']) ?> des favoris"
                        >
                          <i class="bi bi-heart<?= $estFavori ? '-fill' : '' ?>" aria-hidden="true"></i>
                        </button>
                      </form>

                    <?php else: ?>

                      <a
                        href="connexion.php"
                        class="catalog-heart"
                        aria-label="Se connecter pour ajouter <?= htmlspecialchars($recette['titre']) ?> aux favoris"
                      >
                        <i class="bi bi-heart" aria-hidden="true"></i>
                      </a>

                    <?php endif; ?>
                  </div>

                  <h3>
                    <a href="recette.php?id=<?= (int) $recette['id_recette'] ?>">
                      <?= htmlspecialchars($recette['titre']) ?>
                    </a>
                  </h3>

                  <p class="catalog-card-description">
                    <?= htmlspecialchars($recette['description']) ?>
                  </p>

                  <div class="catalog-card-meta">

                    <span>
                      <i class="bi bi-clock" aria-hidden="true"></i>
                      <?= $tempsAffiche ?>
                    </span>

                    <span>
                      <i class="bi bi-egg-fried" aria-hidden="true"></i>
                      <?= $nomsDifficultes[$recette['difficulte']] ?? $recette['difficulte'] ?>
                    </span>

                    <?php if ($note !== null): ?>
                      <span class="catalog-rating" aria-label="Note : <?= $note ?> sur 5">
                        <i class="bi bi-star-fill" aria-hidden="true"></i>
                        <?= str_replace('.', ',', $note) ?>
                      </span>
                    <?php endif; ?>

                  </div>

                </div>

              </article>

            <?php endforeach; ?>

          </div>

          <!-- PAGINATION -->
          <div class="catalog-pagination-bar">

            <p class="catalog-range">
              <?= $donnees['debut'] ?>–<?= $donnees['fin'] ?> sur <?= $donnees['total'] ?> recette<?= $donnees['total'] > 1 ? 's' : '' ?>
            </p>

            <?php if ($donnees['nombrePages'] > 1): ?>

              <nav aria-label="Pagination des recettes">
                <ul class="catalog-pagination">

                  <li>
                    <?php if ($donnees['page'] > 1): ?>
                      <a href="<?= construireLienPage($donnees['page'] - 1, $donnees) ?>" aria-label="Page précédente">
                        ← Précédent
                      </a>
                    <?php else: ?>
                      <span class="disabled">← Précédent</span>
                    <?php endif; ?>
                  </li>

                  <?php for ($numero = 1; $numero <= $donnees['nombrePages']; $numero++): ?>
                    <li>
                      <a
                        href="<?= construireLienPage($numero, $donnees) ?>"
                        class="<?= $numero === $donnees['page'] ? 'active' : '' ?>"
                        <?= $numero === $donnees['page'] ? 'aria-current="page"' : '' ?>
                        aria-label="Page <?= $numero ?>"
                      >
                        <?= $numero ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <li>
                    <?php if ($donnees['page'] < $donnees['nombrePages']): ?>
                      <a href="<?= construireLienPage($donnees['page'] + 1, $donnees) ?>" aria-label="Page suivante">
                        Suivant →
                      </a>
                    <?php else: ?>
                      <span class="disabled">Suivant →</span>
                    <?php endif; ?>
                  </li>

                </ul>
              </nav>

            <?php endif; ?>

          </div>

        <?php endif; ?>

      </div>
    </section>

  </main>

  <?php require __DIR__ . '/../../layouts/footer.php'; ?>

  <!-- Nécessaire pour le menu déroulant du compte (navbar membre). -->
  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Recherche et filtres appliqués directement, sans bouton.
    var formulaire = document.getElementById("formulaire-filtres");
    var champRecherche = document.getElementById("recherche");
    var selectCategorie = document.getElementById("categorie");
    var selectDifficulte = document.getElementById("difficulte");

    // Les listes (catégorie, difficulté) soumettent le formulaire
    // dès qu'on choisit une autre option.
    selectCategorie.addEventListener("change", function () {
      formulaire.submit();
    });

    selectDifficulte.addEventListener("change", function () {
      formulaire.submit();
    });

    // Pour le texte, on attend un court instant après la dernière
    // frappe avant d'envoyer le formulaire (sinon ça recharge la
    // page à chaque lettre tapée).
    var delaiRecherche;

    champRecherche.addEventListener("input", function () {
      clearTimeout(delaiRecherche);

      delaiRecherche = setTimeout(function () {
        formulaire.submit();
      }, 500);
    });

    // Après rechargement de la page, on remet le curseur dans le champ
    // de recherche (sinon il faut recliquer dedans pour continuer à taper).
    if (champRecherche.value) {
      var longueur = champRecherche.value.length;
      champRecherche.focus();
      champRecherche.setSelectionRange(longueur, longueur);
    }
  </script>

</body>
</html>
