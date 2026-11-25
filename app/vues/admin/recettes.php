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

require_once __DIR__ . '/../../controleurs/admin/GestionRecettesControleur.php';

$controleur = new GestionRecettesControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'recettes';
$titrePage = 'Recettes';

// Message de succès après une suppression ou une décision de validation.
$messagesSucces = [
    'ajoutee' => 'Votre recette a bien été envoyée ! Elle sera publiée après vérification.',
    'modifiee' => 'Votre recette a bien été modifiée ! Elle repasse en attente de vérification.',
    'supprimee' => 'La recette a bien été supprimée.',
    'validee' => 'La recette a été validée et publiée.',
    'refusee' => 'La recette a été refusée.',
];
$messageSucces = $messagesSucces[$_GET['action'] ?? ''] ?? null;

// Traduction des statuts et catégories pour l'affichage et les filtres.
$statuts = [
    'publiee' => ['texte' => 'Publiée', 'classe' => 'ar-status-published'],
    'en_attente' => ['texte' => 'En attente', 'classe' => 'ar-status-pending'],
    'refusee' => ['texte' => 'Refusée', 'classe' => 'ar-status-rejected'],
];

$categories = [
    1 => 'Entrée',
    2 => 'Plat',
    3 => 'Dessert',
    4 => 'Boisson',
];

// Construit un lien vers une autre page de résultats, en conservant
// les filtres actuellement choisis.
function construireLienPage(int $numeroPage, array $donnees)
{
    $parametres = [
        'recherche' => $donnees['recherche'],
        'statut' => $donnees['statut'],
        'categorie' => $donnees['idCategorie'],
        'page' => $numeroPage,
    ];

    $parametres = array_filter($parametres, function ($valeur) {
        return $valeur !== null && $valeur !== '';
    });

    return 'recettes.php?' . http_build_query($parametres);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Gestion des recettes — Saveurs Maison</title>

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
        <h1 id="titre-page">Gestion des <em>recettes</em></h1>

        <p class="ad-page-description">
          Consultez et gérez les recettes de la plateforme.
        </p>
      </div>

      <a href="ajouter-recette.php" class="ad-button ad-button-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
        Ajouter une recette
      </a>

    </section>

    <?php if ($messageSucces !== null): ?>
      <div class="ad-message-succes">
        <i class="bi bi-check-circle" aria-hidden="true"></i>
        <p><?= htmlspecialchars($messageSucces) ?></p>
      </div>
    <?php endif; ?>

    <!-- FILTRES -->
    <section class="ad-panel ar-filter-panel" aria-label="Filtres des recettes">

      <form method="get" action="recettes.php" id="formulaire-filtres">
        <div class="row g-3">

          <div class="col-lg-5">
            <label class="form-label" for="recherche">
              Rechercher une recette
            </label>

            <input
              type="search"
              class="form-control"
              id="recherche"
              name="recherche"
              placeholder="Rechercher une recette…"
              value="<?= htmlspecialchars($donnees['recherche']) ?>"
            >
          </div>

          <div class="col-md-6 col-lg-3">
            <label class="form-label" for="statut">Statut</label>

            <select class="form-select" id="statut" name="statut">
              <option value="">Tous les statuts</option>
              <?php foreach ($statuts as $valeur => $infos): ?>
                <option value="<?= $valeur ?>" <?= $donnees['statut'] === $valeur ? 'selected' : '' ?>>
                  <?= $infos['texte'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6 col-lg-4">
            <label class="form-label" for="categorie">Catégorie</label>

            <select class="form-select" id="categorie" name="categorie">
              <option value="">Toutes les catégories</option>
              <?php foreach ($categories as $id => $nom): ?>
                <option value="<?= $id ?>" <?= $donnees['idCategorie'] === $id ? 'selected' : '' ?>>
                  <?= htmlspecialchars($nom) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>
      </form>

    </section>

    <!-- TABLEAU -->
    <section class="ad-panel ar-list-panel" aria-labelledby="titre-liste">

      <div class="ad-panel-heading">
        <div class="ad-panel-title">
          <h2 id="titre-liste">Toutes les recettes</h2>
          <span class="ad-badge ar-count">
            <?= $donnees['total'] ?> recette<?= $donnees['total'] > 1 ? 's' : '' ?>
          </span>
        </div>
      </div>

      <?php if ($donnees['total'] === 0): ?>

        <p class="ad-empty-text">Aucune recette ne correspond à ces critères.</p>

      <?php else: ?>

        <div class="table-responsive">

          <table class="table ad-table ar-table">

            <caption class="visually-hidden">
              Liste des recettes et actions disponibles.
            </caption>

            <thead>
              <tr>
                <th scope="col">Recette</th>
                <th scope="col">Auteur</th>
                <th scope="col">Catégorie</th>
                <th scope="col">Statut</th>
                <th scope="col">Créée le</th>
                <th scope="col" class="ar-actions-column">Actions</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($donnees['recettes'] as $recette): ?>

                <?php $estProprietaire = $recette['id_utilisateur'] === $donnees['idAdmin']; ?>

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
                    <?= $estProprietaire
                        ? 'Administrateur (vous)'
                        : htmlspecialchars($recette['auteur_prenom'] . ' ' . $recette['auteur_nom']) ?>
                  </td>

                  <td><?= htmlspecialchars($recette['categorie']) ?></td>

                  <td>
                    <span class="ad-badge <?= $statuts[$recette['statut']]['classe'] ?>">
                      <?= $statuts[$recette['statut']]['texte'] ?>
                    </span>
                  </td>

                  <td>
                    <time datetime="<?= $recette['date_creation'] ?>">
                      <?= date('d/m/Y', strtotime($recette['date_creation'])) ?>
                    </time>
                  </td>

                  <td>
                    <div class="ar-actions">

                      <a
                        href="detail-recette.php?id=<?= (int) $recette['id_recette'] ?>"
                        class="ar-icon-button"
                        title="Consulter la recette"
                        aria-label="Consulter <?= htmlspecialchars($recette['titre']) ?>"
                      >
                        <i class="bi bi-eye" aria-hidden="true"></i>
                      </a>

                      <?php if ($estProprietaire): ?>

                        <a
                          href="modifier-recette.php?id=<?= (int) $recette['id_recette'] ?>"
                          class="ar-icon-button ar-icon-edit"
                          title="Modifier la recette"
                          aria-label="Modifier <?= htmlspecialchars($recette['titre']) ?>"
                        >
                          <i class="bi bi-pencil" aria-hidden="true"></i>
                        </a>

                        <form
                          method="post"
                          action="recettes.php"
                          onsubmit="return confirm('Voulez-vous vraiment supprimer cette recette ?');"
                        >
                          <input type="hidden" name="action" value="supprimer">
                          <input type="hidden" name="id_recette" value="<?= (int) $recette['id_recette'] ?>">

                          <button
                            type="submit"
                            class="ar-icon-button ar-icon-delete"
                            title="Supprimer la recette"
                            aria-label="Supprimer <?= htmlspecialchars($recette['titre']) ?>"
                          >
                            <i class="bi bi-trash" aria-hidden="true"></i>
                          </button>
                        </form>

                      <?php endif; ?>

                    </div>
                  </td>

                </tr>

              <?php endforeach; ?>

            </tbody>
          </table>
        </div>

        <p class="ar-permissions">
          Modification et suppression réservées à vos propres recettes.
        </p>

        <!-- PAGINATION -->
        <?php if ($donnees['nombrePages'] > 1): ?>
          <div class="ar-pagination-row">

            <p><?= $donnees['debut'] ?>–<?= $donnees['fin'] ?> sur <?= $donnees['total'] ?> recettes</p>

            <nav class="ar-pagination" aria-label="Pagination des recettes">

              <?php if ($donnees['page'] > 1): ?>
                <a href="<?= construireLienPage($donnees['page'] - 1, $donnees) ?>" class="ar-page-button" aria-label="Page précédente">
                  <i class="bi bi-chevron-left" aria-hidden="true"></i>
                </a>
              <?php else: ?>
                <span class="ar-page-button" aria-disabled="true" aria-label="Page précédente">
                  <i class="bi bi-chevron-left" aria-hidden="true"></i>
                </span>
              <?php endif; ?>

              <?php for ($numero = 1; $numero <= $donnees['nombrePages']; $numero++): ?>
                <a
                  href="<?= construireLienPage($numero, $donnees) ?>"
                  class="ar-page-button <?= $numero === $donnees['page'] ? 'active' : '' ?>"
                  <?= $numero === $donnees['page'] ? 'aria-current="page"' : '' ?>
                  aria-label="Page <?= $numero ?>"
                >
                  <?= $numero ?>
                </a>
              <?php endfor; ?>

              <?php if ($donnees['page'] < $donnees['nombrePages']): ?>
                <a href="<?= construireLienPage($donnees['page'] + 1, $donnees) ?>" class="ar-page-button" aria-label="Page suivante">
                  <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>
              <?php else: ?>
                <span class="ar-page-button" aria-disabled="true" aria-label="Page suivante">
                  <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </span>
              <?php endif; ?>

            </nav>
          </div>
        <?php endif; ?>

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

    document.getElementById("categorie").addEventListener("change", function () {
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
