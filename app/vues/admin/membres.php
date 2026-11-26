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

require_once __DIR__ . '/../../controleurs/admin/GestionMembresControleur.php';

$controleur = new GestionMembresControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'membres';
$titrePage = 'Membres';

// Message de succès après une activation ou une désactivation de compte.
$messagesSucces = [
    'activee' => 'Le compte a bien été activé.',
    'desactivee' => 'Le compte a bien été désactivé.',
];
$messageSucces = $messagesSucces[$_GET['action'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Gestion des membres — Saveurs Maison</title>

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
        <h1 id="titre-page">Gestion des <em>membres</em></h1>
        <p class="ad-page-description">
          Consultez les comptes et gérez leur accès à la plateforme.
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
    <section class="ad-panel am-filter-panel" aria-label="Rechercher et filtrer les membres">

      <form method="get" action="membres.php" id="formulaire-filtres">
        <div class="row g-3">

          <div class="col-md-7">
            <label class="form-label" for="recherche">
              Nom ou adresse e-mail
            </label>

            <input
              type="search"
              class="form-control"
              id="recherche"
              name="recherche"
              placeholder="Rechercher un membre…"
              value="<?= htmlspecialchars($donnees['recherche']) ?>"
            >
          </div>

          <div class="col-md-5">
            <label class="form-label" for="statut">Statut</label>

            <select class="form-select" id="statut" name="statut">
              <option value="">Tous les comptes</option>
              <option value="actif" <?= $donnees['statut'] === 'actif' ? 'selected' : '' ?>>Actifs</option>
              <option value="desactive" <?= $donnees['statut'] === 'desactive' ? 'selected' : '' ?>>Désactivés</option>
            </select>
          </div>

        </div>
      </form>

    </section>

    <!-- LISTE DES MEMBRES -->
    <section class="ad-panel am-list-panel" aria-labelledby="titre-membres">

      <div class="ad-panel-heading">
        <div class="ad-panel-title">
          <h2 id="titre-membres">Liste des membres</h2>
          <span class="ad-badge am-count">
            <?= count($donnees['membres']) ?> membre<?= count($donnees['membres']) > 1 ? 's' : '' ?>
          </span>
        </div>
      </div>

      <?php if (empty($donnees['membres'])): ?>

        <p class="ad-empty-text">Aucun membre ne correspond à ces critères.</p>

      <?php else: ?>

        <div class="table-responsive">

          <table class="table ad-table am-table">

            <caption class="visually-hidden">
              Membres inscrits et actions d’activation ou de désactivation.
            </caption>

            <thead>
              <tr>
                <th scope="col">Nom et prénom</th>
                <th scope="col">Adresse e-mail</th>
                <th scope="col">Inscription</th>
                <th scope="col">Statut</th>
                <th scope="col" class="am-action-column">Action</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($donnees['membres'] as $membre): ?>

                <?php $estActif = $membre['statut_compte'] === 'actif'; ?>
                <?php $nomComplet = $membre['prenom'] . ' ' . $membre['nom']; ?>

                <tr>

                  <th scope="row"><?= htmlspecialchars($nomComplet) ?></th>

                  <td><?= htmlspecialchars($membre['email']) ?></td>

                  <td>
                    <time datetime="<?= $membre['date_inscription'] ?>">
                      <?= date('d/m/Y', strtotime($membre['date_inscription'])) ?>
                    </time>
                  </td>

                  <td>
                    <?php if ($estActif): ?>
                      <span class="ad-badge am-status-active">Actif</span>
                    <?php else: ?>
                      <span class="ad-badge am-status-disabled">Désactivé</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div class="am-actions">

                      <form
                        method="post"
                        action="membres.php"
                        onsubmit="return confirm('<?= $estActif
                            ? 'Voulez-vous vraiment désactiver ce compte ?'
                            : 'Voulez-vous vraiment activer ce compte ?' ?>');"
                      >
                        <input type="hidden" name="action" value="<?= $estActif ? 'desactiver' : 'activer' ?>">
                        <input type="hidden" name="id_utilisateur" value="<?= (int) $membre['id_utilisateur'] ?>">

                        <?php if ($estActif): ?>
                          <button
                            type="submit"
                            class="am-icon-button am-disable"
                            title="Désactiver le compte"
                            aria-label="Désactiver le compte de <?= htmlspecialchars($nomComplet) ?>"
                          >
                            <i class="bi bi-slash-circle" aria-hidden="true"></i>
                          </button>
                        <?php else: ?>
                          <button
                            type="submit"
                            class="am-icon-button am-enable"
                            title="Activer le compte"
                            aria-label="Activer le compte de <?= htmlspecialchars($nomComplet) ?>"
                          >
                            <i class="bi bi-person-check" aria-hidden="true"></i>
                          </button>
                        <?php endif; ?>
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

    document.getElementById("statut").addEventListener("change", function () {
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
