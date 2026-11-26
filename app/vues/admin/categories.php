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

require_once __DIR__ . '/../../controleurs/admin/GestionCategoriesControleur.php';

$controleur = new GestionCategoriesControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'categories';
$titrePage = 'Catégories';

$enModification = $donnees['categorieAModifier'] !== null;

// Message de succès après un ajout, une modification ou une suppression.
$messagesSucces = [
    'ajoutee' => 'La catégorie a bien été ajoutée.',
    'modifiee' => 'La catégorie a bien été modifiée.',
    'supprimee' => 'La catégorie a bien été supprimée.',
];
$messageSucces = $messagesSucces[$_GET['action'] ?? ''] ?? null;

$messageErreurSuppression = ($_GET['action'] ?? '') === 'utilisee'
    ? 'Impossible de supprimer : cette catégorie est encore utilisée par au moins une recette.'
    : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Gestion des catégories — Saveurs Maison</title>

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
        <h1 id="titre-page">Gestion des <em>catégories</em></h1>
        <p class="ad-page-description">
          Organisez les recettes de la plateforme.
        </p>
      </div>
    </section>

    <?php if ($messageSucces !== null): ?>
      <div class="ad-message-succes">
        <i class="bi bi-check-circle" aria-hidden="true"></i>
        <p><?= htmlspecialchars($messageSucces) ?></p>
      </div>
    <?php endif; ?>

    <?php if ($messageErreurSuppression !== null): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($messageErreurSuppression) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($donnees['erreurs'])): ?>
      <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
          <?php foreach ($donnees['erreurs'] as $erreur): ?>
            <li><?= htmlspecialchars($erreur) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="row g-4">

      <!-- LISTE DES CATÉGORIES -->
      <div class="col-xl-7">

        <section class="ad-panel ac-list-panel" aria-labelledby="titre-categories">

          <div class="ad-panel-heading">
            <h2 id="titre-categories">Liste des catégories</h2>

            <span class="ad-badge ac-count">
              <?= count($donnees['categories']) ?> catégorie<?= count($donnees['categories']) > 1 ? 's' : '' ?>
            </span>
          </div>

          <?php if (empty($donnees['categories'])): ?>

            <p class="ad-empty-text">Aucune catégorie pour le moment.</p>

          <?php else: ?>

            <div class="table-responsive">

              <table class="table ad-table ac-table">

                <caption class="visually-hidden">
                  Catégories, nombre de recettes associées et actions.
                </caption>

                <thead>
                  <tr>
                    <th scope="col">Nom</th>
                    <th scope="col" class="text-center">Recettes associées</th>
                    <th scope="col" class="text-center">Actions</th>
                  </tr>
                </thead>

                <tbody>

                  <?php foreach ($donnees['categories'] as $categorie): ?>

                    <?php $estUtilisee = (int) $categorie['nombre_recettes'] > 0; ?>

                    <tr>

                      <th scope="row"><?= htmlspecialchars($categorie['nom']) ?></th>

                      <td class="text-center"><?= (int) $categorie['nombre_recettes'] ?></td>

                      <td>
                        <div class="ac-actions">

                          <a
                            href="categories.php?modifier=<?= (int) $categorie['id_categorie'] ?>"
                            class="ac-icon-button ac-edit"
                            title="Modifier <?= htmlspecialchars($categorie['nom']) ?>"
                            aria-label="Modifier la catégorie <?= htmlspecialchars($categorie['nom']) ?>"
                          >
                            <i class="bi bi-pencil" aria-hidden="true"></i>
                          </a>

                          <form
                            method="post"
                            action="categories.php"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');"
                          >
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_categorie" value="<?= (int) $categorie['id_categorie'] ?>">

                            <button
                              type="submit"
                              class="ac-icon-button ac-delete"
                              <?= $estUtilisee ? 'disabled' : '' ?>
                              title="<?= $estUtilisee ? 'Suppression impossible : catégorie utilisée' : 'Supprimer ' . htmlspecialchars($categorie['nom']) ?>"
                              aria-label="<?= $estUtilisee
                                  ? 'Supprimer ' . htmlspecialchars($categorie['nom']) . ' : indisponible, catégorie utilisée'
                                  : 'Supprimer la catégorie ' . htmlspecialchars($categorie['nom']) ?>"
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

          <p class="ac-information">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>Une catégorie utilisée par une recette ne peut pas être supprimée.</span>
          </p>

        </section>
      </div>

      <!-- AJOUT / MODIFICATION D'UNE CATÉGORIE -->
      <div class="col-xl-5">

        <section class="ad-panel ac-form-panel" aria-labelledby="titre-formulaire">

          <h2 id="titre-formulaire">
            <?= $enModification ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?>
          </h2>

          <form method="post" action="categories.php">

            <input type="hidden" name="action" value="<?= $enModification ? 'modifier' : 'ajouter' ?>">

            <?php if ($enModification): ?>
              <input type="hidden" name="id_categorie" value="<?= (int) $donnees['categorieAModifier']['id_categorie'] ?>">
            <?php endif; ?>

            <label class="form-label" for="nom-categorie">
              Nom de la catégorie
              <span class="ac-required" aria-hidden="true">*</span>
            </label>

            <input
              type="text"
              class="form-control"
              id="nom-categorie"
              name="nom"
              maxlength="50"
              placeholder="Ex. Soupes"
              value="<?= $enModification ? htmlspecialchars($donnees['categorieAModifier']['nom']) : '' ?>"
              aria-describedby="aide-categorie unicite-categorie"
              required
            >

            <p class="ac-hint" id="aide-categorie">
              50 caractères maximum.
            </p>

            <button type="submit" class="ad-button ad-button-primary ac-add-button">
              <i class="bi <?= $enModification ? 'bi-check-lg' : 'bi-plus-lg' ?>" aria-hidden="true"></i>
              <?= $enModification ? 'Enregistrer les modifications' : 'Ajouter la catégorie' ?>
            </button>

            <p class="ac-unique" id="unicite-categorie">
              <?php if ($enModification): ?>
                <a href="categories.php">Annuler la modification</a>
              <?php else: ?>
                Le nom doit être unique.
              <?php endif; ?>
            </p>

          </form>

        </section>
      </div>

    </div>

  <?php require __DIR__ . '/../../layouts/admin/footer.php'; ?>

  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
