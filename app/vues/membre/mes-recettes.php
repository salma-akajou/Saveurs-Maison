<?php

// Doit être tout en haut, avant le moindre affichage.
session_start();

// Page réservée aux membres connectés.
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

require_once __DIR__ . '/../../controleurs/MesRecettesControleur.php';

$controleur = new MesRecettesControleur();
$recettes = $controleur->afficher();

$pageActuelle = 'mes-recettes';

// Traduction du statut pour l'affichage (texte et classe CSS).
$statuts = [
    'publiee' => ['texte' => 'Publiée', 'classe' => 'mr-status-published'],
    'en_attente' => ['texte' => 'En attente', 'classe' => 'mr-status-pending'],
    'refusee' => ['texte' => 'Refusée', 'classe' => 'mr-status-rejected'],
];

// Message de succès après ajout, modification ou suppression.
$messagesSucces = [
    'ajoutee' => 'Votre recette a bien été envoyée ! Elle sera publiée après vérification.',
    'modifiee' => 'Votre recette a bien été modifiée ! Elle repasse en attente de vérification.',
    'supprimee' => 'La recette a bien été supprimée.',
];
$messageSucces = $messagesSucces[$_GET['action'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Mes recettes — Saveurs Maison</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="my-recipes-page">

  <?php require __DIR__ . '/../../layouts/navbar.php'; ?>

  <main>
    <div class="container">

      <!-- FIL D'ARIANE -->
      <nav class="mr-breadcrumb" aria-label="Fil d’Ariane">
        <ol>
          <li><a href="../public/accueil.php">Accueil</a></li>
          <li>Mon espace</li>
          <li aria-current="page">Mes recettes</li>
        </ol>
      </nav>

      <?php if ($messageSucces !== null): ?>
        <div class="mr-message-succes" role="alert">
          <i class="bi bi-check-circle" aria-hidden="true"></i>
          <p><?= htmlspecialchars($messageSucces) ?></p>
        </div>
      <?php endif; ?>

      <!-- INTRODUCTION -->
      <section class="mr-intro" aria-labelledby="titre-mes-recettes">

        <div>
          <p class="mr-eyebrow">Mon carnet de cuisine</p>

          <h1 id="titre-mes-recettes">
            Mes recettes, <em>mes inspirations.</em>
          </h1>

          <p class="mr-intro-text">
            Retrouvez vos créations et suivez leur publication.
          </p>
        </div>

        <a href="proposer-recette.php" class="mr-primary-button">
          <i class="bi bi-plus-lg" aria-hidden="true"></i>
          Proposer une recette
        </a>

      </section>

      <?php if (empty($recettes)): ?>

        <!-- AUCUNE RECETTE -->
        <div class="mr-empty">
          <h2>Aucune recette à afficher</h2>
          <p>Partagez votre première création avec la communauté.</p>

          <a href="proposer-recette.php" class="mr-primary-button">
            Proposer une recette
          </a>
        </div>

      <?php else: ?>

        <!-- FILTRES (uniquement visuels, en JavaScript, pas de rechargement) -->
        <div class="mr-filters" role="group" aria-label="Filtrer les recettes par statut">
          <button class="mr-filter active" type="button" data-filter="toutes">
            Toutes (<?= count($recettes) ?>)
          </button>

          <?php foreach ($statuts as $valeur => $infos): ?>
            <?php $nombre = count(array_filter($recettes, function ($r) use ($valeur) {
                return $r['statut'] === $valeur;
            })); ?>

            <button class="mr-filter" type="button" data-filter="<?= $valeur ?>">
              <?= $infos['texte'] ?> (<?= $nombre ?>)
            </button>
          <?php endforeach; ?>
        </div>

        <!-- LISTE -->
        <section class="mr-collection" aria-label="Vos recettes">

          <div class="mr-list" id="liste-recettes">

            <?php foreach ($recettes as $recette): ?>

              <article class="mr-recipe" data-status="<?= $recette['statut'] ?>">

                <a
                  href="../public/recette.php?id=<?= (int) $recette['id_recette'] ?>"
                  class="mr-recipe-image"
                  aria-label="Consulter <?= htmlspecialchars($recette['titre']) ?>"
                >
                  <img
                    src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
                    alt="<?= htmlspecialchars($recette['titre']) ?>"
                  >
                </a>

                <div class="mr-recipe-content">

                  <div class="mr-recipe-main">

                    <div class="mr-recipe-description">
                      <p class="mr-category"><?= htmlspecialchars($recette['categorie']) ?></p>

                      <h2>
                        <a href="../public/recette.php?id=<?= (int) $recette['id_recette'] ?>">
                          <?= htmlspecialchars($recette['titre']) ?>
                        </a>
                      </h2>

                      <p class="mr-excerpt">
                        <?= htmlspecialchars($recette['description']) ?>
                      </p>

                      <p class="mr-date">
                        Créée le
                        <time datetime="<?= $recette['date_creation'] ?>">
                          <?= date('d F Y', strtotime($recette['date_creation'])) ?>
                        </time>
                      </p>
                    </div>

                    <div class="mr-status-wrap">
                      <span class="mr-status <?= $statuts[$recette['statut']]['classe'] ?>">
                        <?= $statuts[$recette['statut']]['texte'] ?>
                      </span>

                      <?php if ($recette['statut'] === 'en_attente'): ?>
                        <p class="mr-status-note">En cours de vérification</p>
                      <?php endif; ?>
                    </div>

                    <div class="mr-actions">

                      <a href="../public/recette.php?id=<?= (int) $recette['id_recette'] ?>">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                        Consulter
                      </a>

                      <a href="modifier-recette.php?id=<?= (int) $recette['id_recette'] ?>">
                        <i class="bi bi-pencil" aria-hidden="true"></i>
                        Modifier
                      </a>

                      <form method="post" action="mes-recettes.php" onsubmit="return confirm('Voulez-vous vraiment supprimer cette recette ?');">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id_recette" value="<?= (int) $recette['id_recette'] ?>">

                        <button type="submit" class="mr-delete">
                          <i class="bi bi-trash" aria-hidden="true"></i>
                          Supprimer
                        </button>
                      </form>

                    </div>
                  </div>

                  <?php if ($recette['statut'] === 'refusee' && $recette['motif_refus']): ?>
                    <div class="mr-refusal">
                      <p class="mr-refusal-preview">
                        <strong>Motif du refus :</strong>
                        <?= htmlspecialchars($recette['motif_refus']) ?>
                      </p>
                    </div>
                  <?php endif; ?>

                </div>
              </article>

            <?php endforeach; ?>

          </div>

        </section>

        <!-- RAPPEL -->
        <aside class="mr-information" aria-label="Information sur la publication">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          <p>Toute modification d’une recette entraîne une nouvelle vérification avant publication.</p>
        </aside>

      <?php endif; ?>

    </div>
  </main>

  <?php require __DIR__ . '/../../layouts/footer.php'; ?>

  <!-- Nécessaire pour le menu déroulant du compte. -->
  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <?php if (!empty($recettes)): ?>
    <script>
      // Filtre d'affichage par statut (ne touche pas à la base de données).
      var boutonsFiltre = document.querySelectorAll(".mr-filter");
      var recettesAffichees = document.querySelectorAll(".mr-recipe");

      boutonsFiltre.forEach(function (bouton) {
        bouton.addEventListener("click", function () {
          var filtre = bouton.dataset.filter;

          boutonsFiltre.forEach(function (b) {
            b.classList.toggle("active", b === bouton);
          });

          recettesAffichees.forEach(function (recette) {
            recette.hidden = filtre !== "toutes" && recette.dataset.status !== filtre;
          });
        });
      });
    </script>
  <?php endif; ?>

</body>
</html>
