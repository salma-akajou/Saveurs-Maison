<?php

// Doit être tout en haut, avant le moindre affichage : le contrôleur
// de connexion utilise $_SESSION sans démarrer lui-même la session.
session_start();

require_once __DIR__ . '/../../controleurs/ConnexionControleur.php';

$controleur = new ConnexionControleur();
$donnees = $controleur->afficher();

// Vient de InscriptionControleur après une création de compte réussie.
$inscriptionReussie = isset($_GET['inscription']) && $_GET['inscription'] === 'succes';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Saveurs Maison — Connexion</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
  <main class="auth-page auth-login">
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
            src="../../assets/images/hero.jpg"
            alt="Couscous aux légumes rôtis, pois chiches et herbes fraîches"
          >

          <div class="auth-visual-caption">
            <p>
              Retrouvez vos recettes favorites, où que vous soyez.
            </p>
            <div class="accent-line"></div>
          </div>
        </div>

        <!-- FORMULAIRE -->
        <div class="col-md-7">
          <div class="auth-form-wrap">

            <p class="auth-eyebrow">Content de vous revoir</p>

            <h1 class="auth-title">Se connecter</h1>

            <p class="auth-subtext">
              Accédez à votre compte Saveurs Maison.
            </p>

            <?php if ($inscriptionReussie): ?>
              <div class="alert alert-success" role="alert">
                Votre compte a bien été créé. Vous pouvez maintenant vous connecter.
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

            <form class="auth-form" method="post" action="connexion.php">

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
                  value="<?= htmlspecialchars($donnees['email']) ?>"
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
                    autocomplete="current-password"
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
              </div>

              <button type="submit" class="btn btn-green w-100">
                Se connecter
              </button>

              <p class="auth-switch">
                Pas encore de compte ?
                <a href="inscription.php">Créer un compte</a>
              </p>

            </form>

          </div>
        </div>

      </div>

    </div>
  </main>

  <script>
    // Afficher ou masquer le mot de passe.
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
  </script>
</body>
</html>
