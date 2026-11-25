<?php

// Doit être tout en haut, avant le moindre affichage.
session_start();

// Page réservée aux membres connectés.
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

require_once __DIR__ . '/../../controleurs/ProfilControleur.php';

$controleur = new ProfilControleur();
$donnees = $controleur->afficher();
$utilisateur = $donnees['utilisateur'];

$pageActuelle = 'profil';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Mon profil — Saveurs Maison</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="profile-page">

  <?php require __DIR__ . '/../../layouts/navbar.php'; ?>

  <main>
    <div class="container">

      <!-- FIL D'ARIANE -->
      <nav class="pr-breadcrumb" aria-label="Fil d’Ariane">
        <ol>
          <li><a href="../public/accueil.php">Accueil</a></li>
          <li aria-current="page">Mon profil</li>
        </ol>
      </nav>

      <div class="pr-content">

        <!-- INTRODUCTION -->
        <section class="pr-intro" aria-labelledby="titre-profil">
          <h1 id="titre-profil">Mon <em>profil</em></h1>

          <p>Modifiez vos informations et votre mot de passe.</p>
        </section>

        <!-- INFORMATIONS PERSONNELLES -->
        <section class="pr-card" aria-labelledby="titre-informations">

          <h2 id="titre-informations">
            Mes <span class="pr-orange">informations</span>
          </h2>

          <form method="post" action="profil.php">
            <input type="hidden" name="formulaire" value="infos">

            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label" for="nom">Nom</label>

                <input
                  class="form-control"
                  type="text"
                  id="nom"
                  name="nom"
                  value="<?= htmlspecialchars($utilisateur['nom']) ?>"
                  maxlength="50"
                  autocomplete="family-name"
                  required
                >
              </div>

              <div class="col-md-6">
                <label class="form-label" for="prenom">Prénom</label>

                <input
                  class="form-control"
                  type="text"
                  id="prenom"
                  name="prenom"
                  value="<?= htmlspecialchars($utilisateur['prenom']) ?>"
                  maxlength="50"
                  autocomplete="given-name"
                  required
                >
              </div>

              <div class="col-12">
                <label class="form-label" for="email">Adresse e-mail</label>

                <input
                  class="form-control"
                  type="email"
                  id="email"
                  name="email"
                  value="<?= htmlspecialchars($utilisateur['email']) ?>"
                  maxlength="100"
                  autocomplete="email"
                  required
                >
              </div>

            </div>

            <button type="submit" class="pr-button pr-button-green">
              Enregistrer
            </button>

            <?php if (!empty($donnees['erreursInfos'])): ?>
              <p class="pr-message pr-message-erreur">
                <?= htmlspecialchars(implode(' ', $donnees['erreursInfos'])) ?>
              </p>
            <?php elseif ($donnees['succesInfos']): ?>
              <p class="pr-message">
                Vos informations ont bien été enregistrées.
              </p>
            <?php endif; ?>

          </form>
        </section>

        <!-- MOT DE PASSE -->
        <section class="pr-card" aria-labelledby="titre-mot-de-passe">

          <h2 id="titre-mot-de-passe">
            Modifier mon <span class="pr-orange">mot de passe</span>
          </h2>

          <form method="post" action="profil.php">
            <input type="hidden" name="formulaire" value="mot_de_passe">

            <div class="mb-3">
              <label class="form-label" for="mot-de-passe-actuel">
                Mot de passe actuel
              </label>

              <input
                class="form-control"
                type="password"
                id="mot-de-passe-actuel"
                name="mot_de_passe_actuel"
                autocomplete="current-password"
                required
              >
            </div>

            <div class="mb-3">
              <label class="form-label" for="nouveau-mot-de-passe">
                Nouveau mot de passe
              </label>

              <input
                class="form-control"
                type="password"
                id="nouveau-mot-de-passe"
                name="nouveau_mot_de_passe"
                minlength="8"
                autocomplete="new-password"
                aria-describedby="aide-mot-de-passe"
                required
              >

              <p class="pr-hint" id="aide-mot-de-passe">
                Au moins 8 caractères.
              </p>
            </div>

            <div>
              <label class="form-label" for="confirmation">
                Confirmer le nouveau mot de passe
              </label>

              <input
                class="form-control"
                type="password"
                id="confirmation"
                name="confirmation"
                minlength="8"
                autocomplete="new-password"
                required
              >
            </div>

            <button type="submit" class="pr-button pr-button-orange">
              Modifier
            </button>

            <?php if (!empty($donnees['erreursMotDePasse'])): ?>
              <p class="pr-message pr-message-erreur">
                <?= htmlspecialchars(implode(' ', $donnees['erreursMotDePasse'])) ?>
              </p>
            <?php elseif ($donnees['succesMotDePasse']): ?>
              <p class="pr-message">
                Votre mot de passe a bien été modifié.
              </p>
            <?php endif; ?>

          </form>
        </section>

      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../../layouts/footer.php'; ?>

  <!-- Nécessaire pour le menu déroulant du compte. -->
  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Vérifier que les deux nouveaux mots de passe correspondent,
    // avant même l'envoi du formulaire.
    var nouveauMotDePasse = document.getElementById("nouveau-mot-de-passe");
    var confirmation = document.getElementById("confirmation");

    function verifierConfirmation() {
      confirmation.setCustomValidity(
        confirmation.value !== nouveauMotDePasse.value
          ? "Les mots de passe ne correspondent pas."
          : ""
      );
    }

    nouveauMotDePasse.addEventListener("input", verifierConfirmation);
    confirmation.addEventListener("input", verifierConfirmation);
  </script>

</body>
</html>
