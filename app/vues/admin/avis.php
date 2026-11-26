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

require_once __DIR__ . '/../../controleurs/admin/GestionAvisControleur.php';

$controleur = new GestionAvisControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'avis';
$titrePage = 'Avis';

$messageSucces = ($_GET['action'] ?? '') === 'supprimee'
    ? 'L’avis a bien été supprimé.'
    : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Gestion des avis — Saveurs Maison</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="admin-page">

  <?php require __DIR__ . '/../../layouts/admin/header.php'; ?>

    <!-- TITRE -->
    <section class="ad-page-heading" aria-labelledby="titre-page">
      <div>
        <h1 id="titre-page">Gestion des <em>avis</em></h1>
        <p class="ad-page-description">
          Consultez les commentaires et retirez les contenus inappropriés.
        </p>
      </div>
    </section>

    <?php if ($messageSucces !== null): ?>
      <div class="ad-message-succes">
        <i class="bi bi-check-circle" aria-hidden="true"></i>
        <p><?= htmlspecialchars($messageSucces) ?></p>
      </div>
    <?php endif; ?>

    <!-- FILTRES -->
    <section class="ad-panel am-filter-panel" aria-label="Rechercher et filtrer les avis">

      <form method="get" action="avis.php" id="formulaire-filtres">
        <div class="row g-3">

          <div class="col-md-7">
            <label class="form-label" for="recherche">
              Membre ou recette
            </label>

            <input
              type="search"
              class="form-control"
              id="recherche"
              name="recherche"
              placeholder="Rechercher un avis…"
              value="<?= htmlspecialchars($donnees['recherche']) ?>"
            >
          </div>

          <div class="col-md-5">
            <label class="form-label" for="note">Note</label>

            <select class="form-select" id="note" name="note">
              <option value="">Toutes les notes</option>
              <?php for ($etoiles = 5; $etoiles >= 1; $etoiles--): ?>
                <option value="<?= $etoiles ?>" <?= $donnees['note'] === $etoiles ? 'selected' : '' ?>>
                  <?= $etoiles ?> étoile<?= $etoiles > 1 ? 's' : '' ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>

        </div>
      </form>

    </section>

    <!-- LISTE DES AVIS -->
    <section class="ad-panel am-list-panel" aria-labelledby="titre-avis">

      <div class="ad-panel-heading">
        <div class="ad-panel-title">
          <h2 id="titre-avis">Liste des avis</h2>
          <span class="ad-badge am-count-reviews">
            <?= count($donnees['avis']) ?> avis
          </span>
        </div>
      </div>

      <?php if (empty($donnees['avis'])): ?>

        <p class="ad-empty-text">Aucun avis ne correspond à ces critères.</p>

      <?php else: ?>

        <div class="table-responsive">

          <table class="table ad-table am-reviews-table">

            <caption class="visually-hidden">
              Avis publiés par les membres et action de suppression.
            </caption>

            <thead>
              <tr>
                <th scope="col">Membre</th>
                <th scope="col">Recette</th>
                <th scope="col">Note</th>
                <th scope="col" class="am-comment-column">Commentaire</th>
                <th scope="col">Date</th>
                <th scope="col" class="am-action-column">Action</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($donnees['avis'] as $avis): ?>

                <?php $nomMembre = $avis['membre_prenom'] . ' ' . $avis['membre_nom']; ?>

                <tr>

                  <th scope="row"><?= htmlspecialchars($nomMembre) ?></th>

                  <td class="am-review-recipe">
                    <a href="../public/recette.php?id=<?= (int) $avis['id_recette'] ?>">
                      <?= htmlspecialchars($avis['recette_titre']) ?>
                    </a>
                  </td>

                  <td>
                    <span class="am-stars" role="img" aria-label="<?= (int) $avis['note'] ?> sur 5">
                      <span aria-hidden="true"><?= str_repeat('★', (int) $avis['note']) ?><span class="am-star-empty"><?= str_repeat('★', 5 - (int) $avis['note']) ?></span></span>
                    </span>
                  </td>

                  <td class="am-comment">
                    <?= htmlspecialchars($avis['commentaire']) ?>
                  </td>

                  <td>
                    <time datetime="<?= $avis['date_avis'] ?>">
                      <?= date('d/m/Y', strtotime($avis['date_avis'])) ?>
                    </time>
                  </td>

                  <td>
                    <div class="am-actions">
                      <form
                        method="post"
                        action="avis.php"
                        onsubmit="return confirm('Voulez-vous vraiment supprimer cet avis ?');"
                      >
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id_avis" value="<?= (int) $avis['id_avis'] ?>">

                        <button
                          type="submit"
                          class="am-icon-button am-disable"
                          title="Supprimer l’avis"
                          aria-label="Supprimer l’avis de <?= htmlspecialchars($nomMembre) ?>"
                        >
                          <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                      </form>
                    </div>
                  </td>

                </tr>

              <?php endforeach; ?>

            </tbody>
          </table>
        </div>

      <?php endif; ?>

    </section>

  <?php require __DIR__ . '/../../layouts/admin/footer.php'; ?>

  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Les filtres s'appliquent directement, sans bouton "Rechercher".
    var formulaire = document.getElementById("formulaire-filtres");

    document.getElementById("note").addEventListener("change", function () {
      formulaire.submit();
    });

    var champRecherche = document.getElementById("recherche");
    var delaiRecherche;

    champRecherche.addEventListener("input", function () {
      clearTimeout(delaiRecherche);

      delaiRecherche = setTimeout(function () {
        formulaire.submit();
      }, 500);
    });

    if (champRecherche.value) {
      var longueur = champRecherche.value.length;
      champRecherche.focus();
      champRecherche.setSelectionRange(longueur, longueur);
    }
  </script>

</body>
</html>
