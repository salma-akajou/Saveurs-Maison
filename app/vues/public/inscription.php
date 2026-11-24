<?php

// Doit être tout en haut, avant le moindre affichage.
session_start();

require_once __DIR__ . '/../../controleurs/InscriptionControleur.php';

$controleur = new InscriptionControleur();
$donnees = $controleur->afficher();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Saveurs Maison — Inscription</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
  <main class="auth-page auth-register">
    <div class="auth-layout">

      <a href="accueil.php" class="auth-brand">
        <img
          src="../../assets/images/logo.png"
          alt="Saveurs Maison — Retour à l’accueil"
          class="auth-logo"
        >
      </a>

      <div class="auth-card row g-0">

        <!-- PHOTO -->
        <div class="col-md-5 auth-visual d-none d-md-block">
          <img
            src="../../assets/images/tarte-pommes.jpg"
            alt="Tarte aux pommes maison"
          >

          <div class="auth-visual-caption">
            <p>
              La cuisine est encore meilleure quand elle se partage.
            </p>
            <div class="accent-line"></div>
          </div>
        </div>

        <!-- FORMULAIRE -->
        <div class="col-md-7">
          <div class="auth-form-wrap">

            <p class="auth-eyebrow">Bienvenue à table</p>

            <h1 class="auth-title">Créer un compte</h1>

            <p class="auth-subtext">
              Vos prochaines découvertes gourmandes commencent ici.
            </p>

            <?php if (!empty($donnees['erreurs'])): ?>
              <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                  <?php foreach ($donnees['erreurs'] as $erreur): ?>
                    <li><?= htmlspecialchars($erreur) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="inscription.php">

              <div class="row auth-name-row">
                <div class="col-sm-6">
                  <label class="form-label" for="nom">
                    Nom <span class="text-terracotta">*</span>
                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="nom"
                    name="nom"
                    placeholder="Votre nom"
                    autocomplete="family-name"
                    maxlength="50"
                    value="<?= htmlspecialchars($donnees['valeurs']['nom']) ?>"
                    required
                  >
                </div>

                <div class="col-sm-6">
                  <label class="form-label" for="prenom">
                    Prénom <span class="text-terracotta">*</span>
                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="prenom"
                    name="prenom"
                    placeholder="Votre prénom"
                    autocomplete="given-name"
                    maxlength="50"
                    value="<?= htmlspecialchars($donnees['valeurs']['prenom']) ?>"
                    required
                  >
                </div>
              </div>

              <div class="auth-field">
                <label class="form-label" for="email">
                  Adresse e-mail <span class="text-terracotta">*</span>
                </label>

                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="vous@exemple.fr"
                  autocomplete="email"
                  maxlength="100"
                  value="<?= htmlspecialchars($donnees['valeurs']['email']) ?>"
                  required
                >
              </div>

              <div class="auth-field">
                <label class="form-label" for="motdepasse">
                  Mot de passe <span class="text-terracotta">*</span>
                </label>

                <div class="input-with-icon">
                  <input
                    type="password"
                    class="form-control"
                    id="motdepasse"
                    name="mot_de_passe"
                    autocomplete="new-password"
                    minlength="8"
                    aria-describedby="aide-motdepasse"
                    required
                  >

                  <button
                    type="button"
                    class="toggle-eye"
                    data-password-target="motdepasse"
                    aria-label="Afficher le mot de passe"
                    aria-pressed="false"
                  >
                    <i class="bi bi-eye" aria-hidden="true"></i>
                  </button>
                </div>

                <div class="auth-hint" id="aide-motdepasse">
                  Au moins 8 caractères.
                </div>
              </div>

              <div class="auth-field">
                <label class="form-label" for="confirmation">
                  Confirmer le mot de passe
                  <span class="text-terracotta">*</span>
                </label>

                <div class="input-with-icon">
                  <input
                    type="password"
                    class="form-control"
                    id="confirmation"
                    name="confirmation"
                    autocomplete="new-password"
                    minlength="8"
                    required
                  >

                  <button
                    type="button"
                    class="toggle-eye"
                    data-password-target="confirmation"
                    aria-label="Afficher le mot de passe"
                    aria-pressed="false"
                  >
                    <i class="bi bi-eye" aria-hidden="true"></i>
                  </button>
                </div>
              </div>

              <button type="submit" class="btn btn-green w-100">
                Créer mon compte
              </button>

              <p class="auth-switch">
                Déjà membre ?
                <a href="connexion.php">Se connecter</a>
              </p>

            </form>

          </div>
        </div>

      </div>

    </div>
  </main>

  <script>
    // Afficher ou masquer les mots de passe.
    document.querySelectorAll("[data-password-target]").forEach(function (button) {
      button.addEventListener("click", function () {
        var input = document.getElementById(button.dataset.passwordTarget);
        var afficher = input.type === "password";

        input.type = afficher ? "text" : "password";

        button.setAttribute("aria-pressed", String(afficher));
        button.setAttribute(
          "aria-label",
          afficher ? "Masquer le mot de passe" : "Afficher le mot de passe"
        );

        button.querySelector("i").className = afficher ? "bi bi-eye-slash" : "bi bi-eye";
      });
    });

    // Vérifier la correspondance des deux mots de passe.
    var motdepasse = document.getElementById("motdepasse");
    var confirmation = document.getElementById("confirmation");

    function verifierConfirmation() {
      confirmation.setCustomValidity(
        confirmation.value !== motdepasse.value
          ? "Les mots de passe ne correspondent pas."
          : ""
      );
    }

    motdepasse.addEventListener("input", verifierConfirmation);
    confirmation.addEventListener("input", verifierConfirmation);
  </script>
</body>
</html>
