<?php

// Doit être tout en haut, avant le moindre affichage.
session_start();

// Page réservée aux administrateurs.
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

if ($_SESSION['utilisateur_role'] !== 'administrateur') {
    header('Location: ../public/accueil.php');
    exit;
}

require_once __DIR__ . '/../../controleurs/admin/DetailRecetteControleur.php';

$controleur = new DetailRecetteControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'recettes';
$titrePage = 'Détail';

// Traduction des statuts pour l'affichage.
$statuts = [
    'publiee' => ['texte' => 'Publiée', 'classe' => 'ar-status-published'],
    'en_attente' => ['texte' => 'En attente', 'classe' => 'ar-status-pending'],
    'refusee' => ['texte' => 'Refusée', 'classe' => 'ar-status-rejected'],
];

$difficultes = [
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

  <title>
    <?= $donnees !== null ? htmlspecialchars($donnees['recette']['titre']) . ' — ' : '' ?>Administration
  </title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="admin-page">

  <?php require __DIR__ . '/../../layouts/admin/header.php'; ?>

    <?php if ($donnees === null): ?>

      <div class="alert alert-danger" role="alert">
        Cette recette est introuvable.
        <a href="recettes.php">Retour aux recettes</a>
      </div>

    <?php else: ?>

      <?php
        $recette = $donnees['recette'];

        // Sa propre recette : pas de décision à prendre sur soi-même.
        $estProprietaire = $recette['id_utilisateur'] === (int) $_SESSION['utilisateur_id'];
      ?>

      <!-- TITRE -->
      <section class="ad-page-heading" aria-labelledby="titre-page">

        <div>
          <h1 id="titre-page">Détail de la <em>recette</em></h1>

          <p class="ad-page-description">
            Consultez le contenu avant de prendre une décision.
          </p>
        </div>

        <a href="recettes.php" class="ad-button ar-return-button">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          Retour aux recettes
        </a>

      </section>

      <!-- PRÉSENTATION -->
      <section class="ad-panel av-hero" aria-labelledby="titre-recette">

        <img
          class="av-photo"
          src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
          alt="<?= htmlspecialchars($recette['titre']) ?>"
        >

        <div class="av-hero-content">

          <div class="av-badges">
            <span class="ad-badge av-category"><?= htmlspecialchars($recette['categorie']) ?></span>

            <span class="ad-badge <?= $statuts[$recette['statut']]['classe'] ?>">
              <?= $statuts[$recette['statut']]['texte'] ?>
            </span>
          </div>

          <h2 id="titre-recette"><?= htmlspecialchars($recette['titre']) ?></h2>

          <p class="av-author">
            Par <strong><?= htmlspecialchars($recette['auteur_prenom'] . ' ' . $recette['auteur_nom']) ?></strong>
            · Proposée le
            <time datetime="<?= $recette['date_creation'] ?>">
              <?= date('d/m/Y', strtotime($recette['date_creation'])) ?>
            </time>
          </p>

          <p class="av-description">
            <?= htmlspecialchars($recette['description']) ?>
          </p>

          <dl class="av-facts">

            <div>
              <i class="bi bi-clock" aria-hidden="true"></i>
              <dt>Préparation</dt>
              <dd><?= (int) $recette['temps_preparation'] ?> min</dd>
            </div>

            <div>
              <i class="bi bi-fire" aria-hidden="true"></i>
              <dt>Cuisson</dt>
              <dd>
                <?= $recette['temps_cuisson'] > 0 ? (int) $recette['temps_cuisson'] . ' min' : 'Sans cuisson' ?>
              </dd>
            </div>

            <div>
              <i class="bi bi-bar-chart" aria-hidden="true"></i>
              <dt>Difficulté</dt>
              <dd><?= $difficultes[$recette['difficulte']] ?? $recette['difficulte'] ?></dd>
            </div>

            <div>
              <i class="bi bi-people" aria-hidden="true"></i>
              <dt>Personnes</dt>
              <dd><?= (int) $recette['nb_personnes'] ?></dd>
            </div>

          </dl>

        </div>
      </section>

      <!-- INGRÉDIENTS ET ÉTAPES -->
      <div class="row g-4 av-preparation">

        <!-- INGRÉDIENTS -->
        <div class="col-lg-4">
          <section class="ad-panel av-preparation-panel">

            <h2 id="titre-ingredients">Ingrédients</h2>

            <div class="av-scroll" tabindex="0" role="region" aria-labelledby="titre-ingredients">

              <ul class="av-ingredients">
                <?php foreach ($donnees['ingredients'] as $ingredient): ?>
                  <li>
                    <span><?= htmlspecialchars($ingredient['nom']) ?></span>
                    <span>
                      <?= $ingredient['quantite'] !== null
                          ? str_replace('.', ',', rtrim(rtrim($ingredient['quantite'], '0'), '.')) . ' ' . htmlspecialchars($ingredient['unite'] ?? '')
                          : 'À votre convenance' ?>
                    </span>
                  </li>
                <?php endforeach; ?>
              </ul>

            </div>

          </section>
        </div>

        <!-- ÉTAPES -->
        <div class="col-lg-8">
          <section class="ad-panel av-preparation-panel">

            <h2 id="titre-etapes">Étapes de préparation</h2>

            <div class="av-scroll" tabindex="0" role="region" aria-labelledby="titre-etapes">

              <ol class="av-steps">
                <?php foreach ($donnees['etapes'] as $etape): ?>
                  <li>
                    <h3><?= htmlspecialchars($etape['titre']) ?></h3>
                    <p><?= htmlspecialchars($etape['description']) ?></p>
                  </li>
                <?php endforeach; ?>
              </ol>

            </div>

          </section>
        </div>

      </div>

      <?php if ($recette['statut'] === 'en_attente' && !$estProprietaire): ?>

        <!-- DÉCISION -->
        <section class="ad-panel av-decision" aria-labelledby="titre-decision">

          <h2 id="titre-decision">Décision de publication</h2>

          <p>Validez la recette ou indiquez les corrections nécessaires.</p>

          <?php if ($donnees['erreur'] !== null): ?>
            <div class="alert alert-danger" role="alert">
              <?= htmlspecialchars($donnees['erreur']) ?>
            </div>
          <?php endif; ?>

          <form method="post" action="detail-recette.php?id=<?= (int) $recette['id_recette'] ?>" id="formulaire-decision">

            <label class="form-label" for="motif-refus">
              Motif du refus (obligatoire en cas de refus)
            </label>

            <textarea
              class="form-control"
              id="motif-refus"
              name="motif_refus"
              rows="4"
              placeholder="Expliquez les corrections à apporter…"
            ></textarea>

            <div class="av-decision-actions">

              <button type="submit" name="action" value="refuser" class="ad-button av-button-reject">
                Refuser la recette
              </button>

              <button type="submit" name="action" value="valider" class="ad-button ad-button-primary" id="bouton-valider">
                Valider et publier
              </button>

            </div>

          </form>

        </section>

      <?php elseif ($recette['statut'] === 'refusee' && $recette['motif_refus']): ?>

        <!-- MOTIF DU REFUS (lecture seule) -->
        <section class="ad-panel av-decision" aria-labelledby="titre-motif">
          <h2 id="titre-motif">Motif du refus</h2>
          <p><?= htmlspecialchars($recette['motif_refus']) ?></p>
        </section>

      <?php endif; ?>

    <?php endif; ?>

  <?php require __DIR__ . '/../../layouts/admin/footer.php'; ?>

  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <?php if ($donnees !== null && $recette['statut'] === 'en_attente' && !$estProprietaire): ?>
    <script>
      // Le motif n'est obligatoire que pour le bouton "Refuser".
      var formulaire = document.getElementById("formulaire-decision");
      var motifRefus = document.getElementById("motif-refus");
      var boutonValider = document.getElementById("bouton-valider");

      // Si un motif est saisi, impossible de valider par erreur :
      // le bouton "Valider" se désactive tant que le champ n'est pas vide.
      function actualiserBoutonValider() {
        boutonValider.disabled = motifRefus.value.trim() !== "";
      }

      motifRefus.addEventListener("input", function () {
        motifRefus.setCustomValidity("");
        actualiserBoutonValider();
      });

      actualiserBoutonValider();

      formulaire.addEventListener("submit", function (event) {
        var bouton = event.submitter;

        if (bouton && bouton.value === "refuser" && motifRefus.value.trim() === "") {
          event.preventDefault();
          motifRefus.focus();
          motifRefus.setCustomValidity("Veuillez expliquer le motif du refus.");
          motifRefus.reportValidity();
        } else {
          motifRefus.setCustomValidity("");
        }
      });
    </script>
  <?php endif; ?>

</body>
</html>
