<?php

require_once __DIR__ . '/../../controleurs/RecetteControleur.php';

$controleur = new RecetteControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'recettes';

// Traduction de la difficulté pour l'affichage.
$nomsDifficultes = [
    'facile' => 'Facile',
    'moyenne' => 'Moyenne',
    'difficile' => 'Difficile',
];

// Enlève les zéros inutiles d'une quantité (ex. "4.00" -> "4", "2.50" -> "2,5").
function formaterQuantite(?string $quantite)
{
    if ($quantite === null) {
        return null;
    }

    $valeur = rtrim(rtrim($quantite, '0'), '.');

    return str_replace('.', ',', $valeur);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>
    <?= $donnees !== null ? htmlspecialchars($donnees['recette']['titre']) . ' — ' : '' ?>Saveurs Maison
  </title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="recipe-detail-page">

  <?php require __DIR__ . '/../../layouts/navbar.php'; ?>

  <main>
    <div class="container">

      <?php if ($donnees === null): ?>

        <!-- RECETTE INTROUVABLE -->
        <section class="rd-hero">
          <div class="rd-hero-content">
            <h1>Recette introuvable</h1>

            <p class="rd-description">
              Cette recette n’existe pas ou n’est plus disponible.
            </p>

            <a href="recettes.php" class="btn-green">
              Voir toutes les recettes
            </a>
          </div>
        </section>

      <?php else: ?>

        <?php
          $recette = $donnees['recette'];

          $noteMoyenne = $recette['note_moyenne'] !== null
              ? str_replace('.', ',', round($recette['note_moyenne'], 1))
              : null;
        ?>

        <!-- FIL D’ARIANE -->
        <nav class="rd-breadcrumb" aria-label="Fil d’Ariane">
          <a href="accueil.php">Accueil</a>
          <span aria-hidden="true">/</span>

          <a href="recettes.php">Les recettes</a>
          <span aria-hidden="true">/</span>

          <span aria-current="page"><?= htmlspecialchars($recette['titre']) ?></span>
        </nav>

        <!-- PRÉSENTATION -->
        <section class="rd-hero" aria-labelledby="titre-recette">

          <div class="rd-hero-visual">
            <img
              src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
              alt="<?= htmlspecialchars($recette['titre']) ?>"
              fetchpriority="high"
            >
          </div>

          <div class="rd-hero-content">

            <a href="recettes.php?categorie=<?= (int) $recette['id_categorie'] ?>" class="rd-category">
              <?= htmlspecialchars($recette['categorie']) ?>
            </a>

            <h1 id="titre-recette"><?= htmlspecialchars($recette['titre']) ?></h1>

            <p class="rd-description">
              <?= htmlspecialchars($recette['description']) ?>
            </p>

            <div class="rd-author-rating">

              <div class="rd-author">
                <span class="rd-avatar" aria-hidden="true">
                  <?= strtoupper(mb_substr($recette['auteur_prenom'], 0, 1) . mb_substr($recette['auteur_nom'], 0, 1)) ?>
                </span>
                <span>
                  Par <strong><?= htmlspecialchars($recette['auteur_prenom']) ?></strong>
                </span>
              </div>

              <?php if ($recette['nombre_avis'] > 0): ?>
                <a href="#avis" class="rd-rating-summary">
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                  <span><strong><?= $noteMoyenne ?></strong> / 5</span>
                  <span class="rd-rating-count">(<?= $recette['nombre_avis'] ?> avis)</span>
                </a>
              <?php else: ?>
                <a href="#avis" class="rd-rating-summary">
                  Aucun avis pour le moment
                </a>
              <?php endif; ?>

            </div>

            <dl class="rd-facts">

              <div class="rd-fact">
                <i class="bi bi-clock" aria-hidden="true"></i>
                <div>
                  <dt>Préparation</dt>
                  <dd><?= (int) $recette['temps_preparation'] ?> min</dd>
                </div>
              </div>

              <div class="rd-fact">
                <i class="bi bi-fire" aria-hidden="true"></i>
                <div>
                  <dt>Cuisson</dt>
                  <dd>
                    <?= $recette['temps_cuisson'] > 0 ? (int) $recette['temps_cuisson'] . ' min' : 'Sans cuisson' ?>
                  </dd>
                </div>
              </div>

              <div class="rd-fact">
                <i class="bi bi-egg-fried" aria-hidden="true"></i>
                <div>
                  <dt>Difficulté</dt>
                  <dd><?= $nomsDifficultes[$recette['difficulte']] ?? $recette['difficulte'] ?></dd>
                </div>
              </div>

              <div class="rd-fact">
                <i class="bi bi-people" aria-hidden="true"></i>
                <div>
                  <dt>Personnes</dt>
                  <dd><?= (int) $recette['nb_personnes'] ?></dd>
                </div>
              </div>

            </dl>

            <!-- Pas encore de connexion : le bouton renvoie vers la page de connexion. -->
            <a href="connexion.php" class="btn-green rd-favorite-button">
              <i class="bi bi-heart" aria-hidden="true"></i>
              Ajouter aux favoris
            </a>

          </div>

        </section>

        <!-- INGRÉDIENTS ET PRÉPARATION -->
        <section class="rd-preparation">

          <!-- INGRÉDIENTS -->
          <aside class="rd-ingredients-panel" aria-labelledby="titre-ingredients">

            <div class="rd-panel-heading">
              <h2 id="titre-ingredients">Dans votre panier</h2>
              <p>Ingrédients pour <?= (int) $recette['nb_personnes'] ?> personnes</p>
            </div>

            <div
              class="rd-panel-scroll rd-ingredients-scroll"
              tabindex="0"
              role="region"
              aria-label="Liste des ingrédients"
            >
              <ul class="rd-ingredient-list">

                <?php foreach ($donnees['ingredients'] as $ingredient): ?>
                  <li>
                    <span><?= htmlspecialchars($ingredient['nom']) ?></span>
                    <span>
                      <?php if ($ingredient['quantite'] !== null): ?>
                        <?= formaterQuantite($ingredient['quantite']) ?> <?= htmlspecialchars($ingredient['unite'] ?? '') ?>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </span>
                  </li>
                <?php endforeach; ?>

              </ul>
            </div>

          </aside>

          <!-- ÉTAPES -->
          <div class="rd-steps-panel">

            <div class="rd-panel-heading">
              <h2 id="titre-preparation">À vous de cuisiner</h2>
            </div>

            <div
              class="rd-panel-scroll rd-steps-scroll"
              tabindex="0"
              role="region"
              aria-labelledby="titre-preparation"
            >

              <ol class="rd-step-list">

                <?php foreach ($donnees['etapes'] as $etape): ?>
                  <li>
                    <div class="rd-step-content">
                      <p>
                        <strong><?= htmlspecialchars($etape['titre']) ?></strong>
                        <?= htmlspecialchars($etape['description']) ?>
                      </p>
                    </div>
                  </li>
                <?php endforeach; ?>

              </ol>

            </div>

          </div>

        </section>

        <!-- AVIS -->
        <section id="avis" class="rd-reviews-section" aria-labelledby="titre-avis">

          <div class="rd-reviews-heading">
            <h2 id="titre-avis">Ils l’ont cuisinée</h2>

            <?php if ($recette['nombre_avis'] > 0): ?>
              <p class="rd-reviews-score">
                <i class="bi bi-star-fill" aria-hidden="true"></i>
                <span><strong><?= $noteMoyenne ?></strong> / 5 · <?= $recette['nombre_avis'] ?> avis</span>
              </p>
            <?php endif; ?>
          </div>

          <?php if (count($donnees['avis']) === 0): ?>

            <p class="rd-reviews-empty">
              Aucun avis pour cette recette pour le moment.
            </p>

          <?php else: ?>

            <div class="rd-reviews-grid">

              <?php foreach ($donnees['avis'] as $avis): ?>

                <?php
                  $initiales = strtoupper(mb_substr($avis['prenom'], 0, 1) . mb_substr($avis['nom'], 0, 1));
                  $etoiles = str_repeat('★', $avis['note']) . str_repeat('☆', 5 - $avis['note']);
                ?>

                <article class="rd-review-card">

                  <div class="rd-review-header">

                    <div class="rd-review-author">
                      <span class="rd-avatar" aria-hidden="true"><?= $initiales ?></span>

                      <div>
                        <h3>
                          <?= htmlspecialchars($avis['prenom'] . ' ' . mb_substr($avis['nom'], 0, 1) . '.') ?>
                        </h3>
                        <div class="rd-review-stars" aria-label="Note : <?= $avis['note'] ?> sur 5">
                          <?= $etoiles ?>
                        </div>
                      </div>
                    </div>

                    <time datetime="<?= $avis['date_avis'] ?>">
                      <?= date('d/m/Y', strtotime($avis['date_avis'])) ?>
                    </time>

                  </div>

                  <p>
                    <?= htmlspecialchars($avis['commentaire']) ?>
                  </p>

                </article>

              <?php endforeach; ?>

            </div>

          <?php endif; ?>

          <!-- FORMULAIRE D'AVIS -->
          <!-- Pas encore de connexion : envoyer le formulaire redirige
               simplement vers la page de connexion pour l'instant. -->
          <div class="rd-review-form-panel">

            <h3>Et vous, qu’en pensez-vous ?</h3>

            <form
              class="rd-review-form"
              onsubmit="event.preventDefault(); window.location.href = 'connexion.php';"
            >

              <div class="rd-review-form-grid">

                <fieldset class="rd-rating-field">
                  <legend>Votre note</legend>

                  <div class="rd-star-options">

                    <div class="rd-star-choice">
                      <input type="radio" name="note" id="note-1" value="1" required>
                      <label for="note-1">
                        <span aria-hidden="true">★</span>
                        <span class="visually-hidden">1 étoile sur 5</span>
                      </label>
                    </div>

                    <div class="rd-star-choice">
                      <input type="radio" name="note" id="note-2" value="2">
                      <label for="note-2">
                        <span aria-hidden="true">★</span>
                        <span class="visually-hidden">2 étoiles sur 5</span>
                      </label>
                    </div>

                    <div class="rd-star-choice">
                      <input type="radio" name="note" id="note-3" value="3">
                      <label for="note-3">
                        <span aria-hidden="true">★</span>
                        <span class="visually-hidden">3 étoiles sur 5</span>
                      </label>
                    </div>

                    <div class="rd-star-choice">
                      <input type="radio" name="note" id="note-4" value="4">
                      <label for="note-4">
                        <span aria-hidden="true">★</span>
                        <span class="visually-hidden">4 étoiles sur 5</span>
                      </label>
                    </div>

                    <div class="rd-star-choice">
                      <input type="radio" name="note" id="note-5" value="5">
                      <label for="note-5">
                        <span aria-hidden="true">★</span>
                        <span class="visually-hidden">5 étoiles sur 5</span>
                      </label>
                    </div>

                  </div>

                </fieldset>

                <div class="rd-comment-field">
                  <label for="commentaire">Votre commentaire</label>

                  <textarea
                    id="commentaire"
                    name="commentaire"
                    rows="3"
                    placeholder="Partagez votre expérience en cuisine…"
                    required
                  ></textarea>
                </div>

              </div>

              <div class="rd-review-form-bottom">
                <p>Un seul avis par recette et par membre.</p>

                <button type="submit" class="btn-green">
                  Publier mon avis
                </button>
              </div>

            </form>

          </div>

        </section>

      <?php endif; ?>

    </div>
  </main>

  <?php require __DIR__ . '/../../layouts/footer.php'; ?>

  <?php if ($donnees !== null): ?>
    <script>
      // Colorer les étoiles jusqu'à la note choisie.
      var notes = document.querySelectorAll('.rd-star-choice input[name="note"]');

      function actualiserEtoiles() {
        var noteSelectionnee = document.querySelector(
          '.rd-star-choice input[name="note"]:checked'
        );

        var valeur = noteSelectionnee ? Number(noteSelectionnee.value) : 0;

        notes.forEach(function (input) {
          input.parentElement.classList.toggle(
            "is-filled",
            Number(input.value) <= valeur
          );
        });
      }

      notes.forEach(function (input) {
        input.addEventListener("change", actualiserEtoiles);
      });
    </script>
  <?php endif; ?>

</body>
</html>
