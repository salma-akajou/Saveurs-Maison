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

require_once __DIR__ . '/../../controleurs/TableauDeBordControleur.php';

$controleur = new TableauDeBordControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'dashboard';
$titrePage = 'Tableau de bord';

// Traduction des statuts et pourcentages, pour la répartition.
$total = max(1, $donnees['totalRecettes']); // évite une division par 0
$pourcentagePubliees = round($donnees['recettesParStatut']['publiee'] / $total * 100, 1);
$pourcentageEnAttente = round($donnees['recettesParStatut']['en_attente'] / $total * 100, 1);
$pourcentageRefusees = round($donnees['recettesParStatut']['refusee'] / $total * 100, 1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Tableau de bord — Saveurs Maison</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="admin-page">

  <?php require __DIR__ . '/../../layouts/admin-header.php'; ?>

    <!-- TITRE -->
    <section class="ad-page-heading" aria-labelledby="titre-dashboard">

      <div>
        <p class="ad-eyebrow">Vue d’ensemble</p>

        <h1 id="titre-dashboard">
          Tableau de <em>bord</em>
        </h1>

        <p class="ad-page-description">
          Bienvenue dans votre espace de gestion.
        </p>
      </div>

      <a href="ajouter-recette.php" class="ad-button ad-button-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
        Ajouter une recette
      </a>

    </section>

    <!-- STATISTIQUES -->
    <section aria-label="Statistiques générales">

      <div class="row g-3 ad-statistics">

        <div class="col-sm-6 col-xl-3">
          <article class="ad-stat-card">

            <span class="ad-stat-icon ad-icon-green">
              <i class="bi bi-journal-check" aria-hidden="true"></i>
            </span>

            <div>
              <p class="ad-stat-number"><?= $donnees['recettesParStatut']['publiee'] ?></p>
              <h2>Recettes publiées</h2>
            </div>

          </article>
        </div>

        <div class="col-sm-6 col-xl-3">
          <article class="ad-stat-card">

            <span class="ad-stat-icon ad-icon-amber">
              <i class="bi bi-hourglass-split" aria-hidden="true"></i>
            </span>

            <div>
              <p class="ad-stat-number"><?= $donnees['recettesParStatut']['en_attente'] ?></p>
              <h2>En attente</h2>
            </div>

          </article>
        </div>

        <div class="col-sm-6 col-xl-3">
          <article class="ad-stat-card">

            <span class="ad-stat-icon ad-icon-green">
              <i class="bi bi-people" aria-hidden="true"></i>
            </span>

            <div>
              <p class="ad-stat-number"><?= $donnees['totalMembres'] ?></p>
              <h2>Membres</h2>
            </div>

          </article>
        </div>

        <div class="col-sm-6 col-xl-3">
          <article class="ad-stat-card">

            <span class="ad-stat-icon ad-icon-orange">
              <i class="bi bi-chat-left" aria-hidden="true"></i>
            </span>

            <div>
              <p class="ad-stat-number"><?= $donnees['totalAvis'] ?></p>
              <h2>Avis</h2>
            </div>

          </article>
        </div>

      </div>
    </section>

    <!-- RECETTES EN ATTENTE ET RÉPARTITION -->
    <div class="row g-4 ad-middle-row">

      <!-- RECETTES À VÉRIFIER -->
      <div class="col-xl-8">
        <section class="ad-panel" aria-labelledby="titre-verification">

          <div class="ad-panel-heading">

            <div class="ad-panel-title">
              <h2 id="titre-verification">Recettes à vérifier</h2>
              <span class="ad-badge ad-badge-pending">
                <?= $donnees['recettesParStatut']['en_attente'] ?> en attente
              </span>
            </div>

            <a href="recettes.php?statut=en_attente" class="ad-text-link">
              Voir toutes
              <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </a>

          </div>

          <?php if (empty($donnees['recettesEnAttente'])): ?>

            <p class="ad-empty-text">Aucune recette en attente pour le moment.</p>

          <?php else: ?>

            <div class="table-responsive">

              <table class="table ad-table ad-pending-table">

                <caption class="visually-hidden">
                  Les dernières recettes proposées à la validation.
                </caption>

                <thead>
                  <tr>
                    <th scope="col">Recette</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Proposée le</th>
                    <th scope="col" class="text-end">Action</th>
                  </tr>
                </thead>

                <tbody>

                  <?php foreach ($donnees['recettesEnAttente'] as $recette): ?>
                    <tr>
                      <td>
                        <div class="ad-recipe-cell">
                          <img
                            src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
                            alt=""
                            loading="lazy"
                          >
                          <span><?= htmlspecialchars($recette['titre']) ?></span>
                        </div>
                      </td>

                      <td>
                        <?= htmlspecialchars($recette['auteur_prenom'] . ' ' . $recette['auteur_nom']) ?>
                      </td>

                      <td>
                        <time datetime="<?= $recette['date_creation'] ?>">
                          <?= date('d/m/Y', strtotime($recette['date_creation'])) ?>
                        </time>
                      </td>

                      <td class="text-end">
                        <a
                          href="verifier-recette.php?id=<?= (int) $recette['id_recette'] ?>"
                          class="ad-button ad-button-outline ad-button-small"
                          aria-label="Vérifier <?= htmlspecialchars($recette['titre']) ?>"
                        >
                          Vérifier
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>

                </tbody>

              </table>

            </div>

          <?php endif; ?>

        </section>
      </div>

      <!-- RÉPARTITION -->
      <div class="col-xl-4">
        <section class="ad-panel" aria-labelledby="titre-repartition">

          <div class="ad-panel-heading">
            <h2 id="titre-repartition">Répartition des recettes</h2>
          </div>

          <div class="ad-distribution">

            <div class="ad-distribution-item">
              <div class="ad-progress-label">
                <span>Publiées</span>
                <span>
                  <?= $donnees['recettesParStatut']['publiee'] ?>
                  <span class="ad-percentage">(<?= str_replace('.', ',', $pourcentagePubliees) ?> %)</span>
                </span>
              </div>

              <div
                class="progress ad-progress"
                role="progressbar"
                aria-label="Recettes publiées"
                aria-valuenow="<?= $donnees['recettesParStatut']['publiee'] ?>"
                aria-valuemin="0"
                aria-valuemax="<?= $donnees['totalRecettes'] ?>"
              >
                <div class="progress-bar ad-bar-published" style="width: <?= $pourcentagePubliees ?>%"></div>
              </div>
            </div>

            <div class="ad-distribution-item">
              <div class="ad-progress-label">
                <span>En attente</span>
                <span>
                  <?= $donnees['recettesParStatut']['en_attente'] ?>
                  <span class="ad-percentage">(<?= str_replace('.', ',', $pourcentageEnAttente) ?> %)</span>
                </span>
              </div>

              <div
                class="progress ad-progress"
                role="progressbar"
                aria-label="Recettes en attente"
                aria-valuenow="<?= $donnees['recettesParStatut']['en_attente'] ?>"
                aria-valuemin="0"
                aria-valuemax="<?= $donnees['totalRecettes'] ?>"
              >
                <div class="progress-bar ad-bar-pending" style="width: <?= $pourcentageEnAttente ?>%"></div>
              </div>
            </div>

            <div class="ad-distribution-item">
              <div class="ad-progress-label">
                <span>Refusées</span>
                <span>
                  <?= $donnees['recettesParStatut']['refusee'] ?>
                  <span class="ad-percentage">(<?= str_replace('.', ',', $pourcentageRefusees) ?> %)</span>
                </span>
              </div>

              <div
                class="progress ad-progress"
                role="progressbar"
                aria-label="Recettes refusées"
                aria-valuenow="<?= $donnees['recettesParStatut']['refusee'] ?>"
                aria-valuemin="0"
                aria-valuemax="<?= $donnees['totalRecettes'] ?>"
              >
                <div class="progress-bar ad-bar-rejected" style="width: <?= $pourcentageRefusees ?>%"></div>
              </div>
            </div>

            <p class="ad-distribution-total">
              <strong><?= $donnees['totalRecettes'] ?></strong> recettes au total
            </p>

          </div>
        </section>
      </div>

    </div>

    <!-- DERNIERS AVIS -->
    <section class="ad-panel ad-reviews-panel" aria-labelledby="titre-avis">

      <div class="ad-panel-heading">
        <h2 id="titre-avis">Derniers avis</h2>

        <a href="avis.php" class="ad-text-link">
          Voir tous les avis
          <i class="bi bi-chevron-right" aria-hidden="true"></i>
        </a>
      </div>

      <?php if (empty($donnees['derniersAvis'])): ?>

        <p class="ad-empty-text">Aucun avis pour le moment.</p>

      <?php else: ?>

        <div class="table-responsive">

          <table class="table ad-table ad-reviews-table">

            <caption class="visually-hidden">
              Les derniers avis publiés par les membres.
            </caption>

            <thead>
              <tr>
                <th scope="col">Membre</th>
                <th scope="col">Recette</th>
                <th scope="col">Note</th>
                <th scope="col">Commentaire</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($donnees['derniersAvis'] as $avis): ?>
                <tr>
                  <td>
                    <div class="ad-member-cell">
                      <span class="ad-member-icon">
                        <i class="bi bi-person" aria-hidden="true"></i>
                      </span>
                      <?= htmlspecialchars($avis['membre_prenom'] . ' ' . $avis['membre_nom']) ?>
                    </div>
                  </td>

                  <td class="ad-recipe-name"><?= htmlspecialchars($avis['recette_titre']) ?></td>

                  <td>
                    <span class="ad-stars" role="img" aria-label="<?= $avis['note'] ?> sur 5">
                      <span aria-hidden="true">
                        <?= str_repeat('★', $avis['note']) ?><span class="ad-star-empty"><?= str_repeat('★', 5 - $avis['note']) ?></span>
                      </span>
                    </span>
                  </td>

                  <td><?= htmlspecialchars($avis['commentaire']) ?></td>
                </tr>
              <?php endforeach; ?>

            </tbody>

          </table>

        </div>

      <?php endif; ?>

    </section>

  <?php require __DIR__ . '/../../layouts/admin-footer.php'; ?>

  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
