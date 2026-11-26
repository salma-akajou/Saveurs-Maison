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

require_once __DIR__ . '/../../controleurs/public/ModifierRecetteControleur.php';

$controleur = new ModifierRecetteControleur();
$donnees = $controleur->afficher();

$pageActuelle = 'recettes';
$titrePage = 'Modifier la recette';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Modifier la recette — Administration</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="admin-page recipe-form-page recipe-edit-page">

  <?php require __DIR__ . '/../../layouts/admin/header.php'; ?>

    <?php if ($donnees === null): ?>

      <div class="alert alert-danger" role="alert">
        Cette recette est introuvable.
        <a href="recettes.php">Retour aux recettes</a>
      </div>

    <?php else: ?>

      <?php $recette = $donnees['recette']; ?>

      <!-- TITRE -->
      <section class="ad-page-heading" aria-labelledby="titre-page">
        <div>
          <h1 id="titre-page">Modifier la <em>recette</em></h1>
          <p class="ad-page-description">Ajustez votre création avant de la partager à nouveau.</p>
        </div>

        <a href="recettes.php" class="ad-button ar-return-button">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          Retour aux recettes
        </a>

      </section>

      <?php if ($recette['statut'] === 'refusee'): ?>
        <aside class="re-refusal" aria-labelledby="titre-refus">
          <span class="re-status">Refusée</span>

          <div class="re-refusal-content">
            <h2 id="titre-refus">Motif du refus</h2>
            <p><?= htmlspecialchars($recette['motif_refus']) ?></p>
          </div>
        </aside>
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

      <p class="rf-required-note">
        Champs obligatoires <span aria-hidden="true">*</span>
      </p>

      <!-- FORMULAIRE -->
      <form
        id="formulaire-recette"
        class="rf-form"
        method="post"
        action="modifier-recette.php?id=<?= (int) $recette['id_recette'] ?>"
        enctype="multipart/form-data"
        novalidate
      >

        <!-- 01 INFORMATIONS -->
        <section class="rf-panel" aria-labelledby="titre-informations">

          <div class="rf-panel-heading">
            <span class="rf-section-number" aria-hidden="true">01</span>
            <h2 id="titre-informations">Les informations essentielles</h2>
          </div>

          <div class="row g-4">

            <!-- PHOTO ACTUELLE -->
            <div class="col-lg-4 rf-photo-column">

              <div class="re-photo-wrap">

                <img
                  id="apercu-photo"
                  class="re-current-photo"
                  src="../../assets/images/<?= htmlspecialchars($recette['photo']) ?>"
                  alt="<?= htmlspecialchars($recette['titre']) ?>"
                >

                <!-- Facultatif : la photo actuelle est conservée -->
                <input
                  type="file"
                  id="photo"
                  name="photo"
                  class="rf-file-input"
                  accept="image/jpeg,image/png"
                  aria-label="Remplacer la photo de la recette"
                  aria-describedby="aide-photo erreur-photo"
                >

                <label for="photo" class="rf-upload-button">
                  <i class="bi bi-image" aria-hidden="true"></i>
                  Changer la photo
                </label>

                <p class="re-photo-help" id="aide-photo">
                  La photo actuelle sera conservée si vous ne la remplacez pas.
                  JPG ou PNG · 5 Mo maximum.
                </p>

              </div>

              <p id="erreur-photo" class="rf-error" aria-live="polite"></p>

            </div>

            <!-- TITRE ET DESCRIPTION -->
            <div class="col-lg-8">

              <div class="mb-3">
                <label class="rf-label" for="titre">
                  Titre de la recette <span aria-hidden="true">*</span>
                </label>

                <input
                  type="text"
                  class="form-control"
                  id="titre"
                  name="titre"
                  maxlength="100"
                  value="<?= htmlspecialchars($recette['titre']) ?>"
                  required
                >
                <p class="rf-error"></p>
              </div>

              <div>
                <label class="rf-label" for="description">
                  Description <span aria-hidden="true">*</span>
                </label>

                <textarea
                  class="form-control rf-description"
                  id="description"
                  name="description"
                  rows="4"
                  required
                ><?= htmlspecialchars($recette['description']) ?></textarea>
                <p class="rf-error"></p>
              </div>

            </div>
          </div>

          <!-- AUTRES INFORMATIONS -->
          <div class="rf-details-grid">

            <div>
              <label class="rf-label" for="categorie">
                Catégorie <span aria-hidden="true">*</span>
              </label>

              <select class="form-select" id="categorie" name="id_categorie" required>
                <?php foreach ($donnees['categories'] as $id => $nom): ?>
                  <option value="<?= $id ?>" <?= $recette['id_categorie'] == $id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nom) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <p class="rf-error"></p>
            </div>

            <div>
              <label class="rf-label" for="difficulte">
                Difficulté <span aria-hidden="true">*</span>
              </label>

              <select class="form-select" id="difficulte" name="difficulte" required>
                <option value="facile" <?= $recette['difficulte'] === 'facile' ? 'selected' : '' ?>>Facile</option>
                <option value="moyenne" <?= $recette['difficulte'] === 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
                <option value="difficile" <?= $recette['difficulte'] === 'difficile' ? 'selected' : '' ?>>Difficile</option>
              </select>
              <p class="rf-error"></p>
            </div>

            <div>
              <label class="rf-label" for="nb-personnes">
                Nombre de personnes <span aria-hidden="true">*</span>
              </label>

              <input
                type="number"
                class="form-control"
                id="nb-personnes"
                name="nb_personnes"
                min="1"
                step="1"
                value="<?= (int) $recette['nb_personnes'] ?>"
                required
              >
              <p class="rf-error"></p>
            </div>

            <div>
              <label class="rf-label" for="temps-preparation">
                Préparation (min) <span aria-hidden="true">*</span>
              </label>

              <input
                type="number"
                class="form-control"
                id="temps-preparation"
                name="temps_preparation"
                min="0"
                step="1"
                value="<?= (int) $recette['temps_preparation'] ?>"
                required
              >
              <p class="rf-error"></p>
            </div>

            <div>
              <label class="rf-label" for="temps-cuisson">
                Cuisson (min) <span aria-hidden="true">*</span>
              </label>

              <input
                type="number"
                class="form-control"
                id="temps-cuisson"
                name="temps_cuisson"
                min="0"
                step="1"
                value="<?= (int) $recette['temps_cuisson'] ?>"
                required
              >
              <p class="rf-error"></p>
            </div>

          </div>
        </section>

        <!-- 02 INGRÉDIENTS -->
        <section class="rf-panel" aria-labelledby="titre-ingredients">

          <div class="rf-panel-heading">
            <span class="rf-section-number" aria-hidden="true">02</span>

            <div>
              <h2 id="titre-ingredients">Les ingrédients</h2>
              <p>Quantité et unité sont facultatives.</p>
            </div>
          </div>

          <div class="rf-ingredient-head" aria-hidden="true">
            <span>Ingrédient *</span>
            <span>Quantité</span>
            <span>Unité</span>
            <span></span>
          </div>

          <div id="liste-ingredients"></div>

          <button class="rf-add-button" type="button" id="ajouter-ingredient">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            Ajouter un ingrédient
          </button>

        </section>

        <!-- 03 ÉTAPES -->
        <section class="rf-panel" aria-labelledby="titre-etapes">

          <div class="rf-panel-heading">
            <span class="rf-section-number" aria-hidden="true">03</span>

            <div>
              <h2 id="titre-etapes">Les étapes de préparation</h2>
              <p>Décrivez les étapes dans l’ordre de réalisation.</p>
            </div>
          </div>

          <div id="liste-etapes"></div>

          <button class="rf-add-button" type="button" id="ajouter-etape">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            Ajouter une étape
          </button>

        </section>

        <!-- ACTIONS -->
        <div class="rf-form-actions">
          <a href="recettes.php" class="rf-button rf-button-outline">
            Annuler
          </a>

          <button type="submit" class="rf-button rf-button-primary">
            Enregistrer les modifications
          </button>
        </div>

      </form>

    <?php endif; ?>

  <?php require __DIR__ . '/../../layouts/admin/footer.php'; ?>

  <?php if ($donnees !== null): ?>

    <!-- MODÈLE D'UNE LIGNE INGRÉDIENT -->
    <template id="modele-ingredient">
      <div class="rf-ingredient-row">

        <div>
          <label class="rf-mobile-label rf-ingredient-label">Ingrédient *</label>

          <input
            type="text"
            class="form-control rf-ingredient-name"
            maxlength="50"
            placeholder="Ex. Farine"
            required
          >
          <p class="rf-error"></p>
        </div>

        <div>
          <label class="rf-mobile-label rf-quantity-label">Quantité</label>

          <input
            type="number"
            class="form-control rf-ingredient-quantity"
            min="0.01"
            max="99999999.99"
            step="0.01"
            placeholder="Facultatif"
          >
        </div>

        <div>
          <label class="rf-mobile-label rf-unit-label">Unité</label>

          <select class="form-select rf-ingredient-unit">
            <option value="">Facultatif</option>
            <option value="g">g</option>
            <option value="kg">kg</option>
            <option value="ml">ml</option>
            <option value="cl">cl</option>
            <option value="l">l</option>
            <option value="pièce">pièce</option>
            <option value="c. à café">c. à café</option>
            <option value="c. à soupe">c. à soupe</option>
            <option value="pincée">pincée</option>
          </select>
        </div>

        <button class="rf-remove-button" type="button" aria-label="Supprimer cet ingrédient">
          <i class="bi bi-trash" aria-hidden="true"></i>
        </button>

      </div>
    </template>

    <!-- MODÈLE D'UNE ÉTAPE -->
    <template id="modele-etape">
      <div class="rf-step-row">

        <span class="rf-step-number" aria-hidden="true"></span>

        <div>
          <label class="visually-hidden rf-step-title-label"></label>

          <input
            type="text"
            class="form-control rf-step-title"
            placeholder="Titre de l’étape"
            maxlength="100"
            required
          >
          <p class="rf-error"></p>

          <label class="visually-hidden rf-step-label"></label>

          <textarea
            class="form-control rf-step-description"
            rows="2"
            placeholder="Décrivez cette étape…"
            required
          ></textarea>
          <p class="rf-error"></p>
        </div>

        <button class="rf-remove-button" type="button" aria-label="Supprimer cette étape">
          <i class="bi bi-trash" aria-hidden="true"></i>
        </button>

      </div>
    </template>

    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
      var formulaire = document.getElementById("formulaire-recette");
      var listeIngredients = document.getElementById("liste-ingredients");
      var listeEtapes = document.getElementById("liste-etapes");

      var modeleIngredient = document.getElementById("modele-ingredient");
      var modeleEtape = document.getElementById("modele-etape");

      var boutonIngredient = document.getElementById("ajouter-ingredient");
      var boutonEtape = document.getElementById("ajouter-etape");

      var photo = document.getElementById("photo");
      var apercuPhoto = document.getElementById("apercu-photo");
      var erreurPhoto = document.getElementById("erreur-photo");
      var photoActuelle = apercuPhoto.getAttribute("src");

      var identifiantIngredient = 0;
      var identifiantEtape = 0;
      var urlPhoto = null;

      /* INGRÉDIENTS */

      function actualiserIngredients() {
        var lignes = [].slice.call(listeIngredients.querySelectorAll(".rf-ingredient-row"));

        lignes.forEach(function (ligne, index) {
          ligne.querySelector(".rf-ingredient-name").name = "ingredients[" + index + "][nom]";
          ligne.querySelector(".rf-ingredient-quantity").name = "ingredients[" + index + "][quantite]";
          ligne.querySelector(".rf-ingredient-unit").name = "ingredients[" + index + "][unite]";

          var suppression = ligne.querySelector(".rf-remove-button");
          suppression.disabled = lignes.length === 1;
          suppression.setAttribute("aria-label", "Supprimer l’ingrédient " + (index + 1));
        });
      }

      function ajouterIngredient(nom, quantite, unite, donnerFocus) {
        var fragment = modeleIngredient.content.cloneNode(true);
        var ligne = fragment.querySelector(".rf-ingredient-row");
        var prefixe = "ingredient-" + (++identifiantIngredient);

        [
          [".rf-ingredient-name", ".rf-ingredient-label", "-nom"],
          [".rf-ingredient-quantity", ".rf-quantity-label", "-quantite"],
          [".rf-ingredient-unit", ".rf-unit-label", "-unite"]
        ].forEach(function (champsLabel) {
          var champ = ligne.querySelector(champsLabel[0]);
          var label = ligne.querySelector(champsLabel[1]);

          champ.id = prefixe + champsLabel[2];
          label.htmlFor = champ.id;
        });

        ligne.querySelector(".rf-ingredient-name").value = nom || "";
        ligne.querySelector(".rf-ingredient-quantity").value = quantite || "";
        ligne.querySelector(".rf-ingredient-unit").value = unite || "";

        listeIngredients.appendChild(fragment);
        actualiserIngredients();

        if (donnerFocus !== false) {
          ligne.querySelector(".rf-ingredient-name").focus();
        }
      }

      boutonIngredient.addEventListener("click", function () {
        ajouterIngredient("", "", "", true);
      });

      listeIngredients.addEventListener("click", function (event) {
        var bouton = event.target.closest(".rf-remove-button");

        if (!bouton || bouton.disabled) return;

        bouton.closest(".rf-ingredient-row").remove();
        actualiserIngredients();
        boutonIngredient.focus();
      });

      /* ÉTAPES */

      function actualiserEtapes() {
        var lignes = [].slice.call(listeEtapes.querySelectorAll(".rf-step-row"));

        lignes.forEach(function (ligne, index) {
          var numero = index + 1;

          ligne.querySelector(".rf-step-number").textContent = numero;
          ligne.querySelector(".rf-step-title-label").textContent = "Titre de l’étape " + numero;
          ligne.querySelector(".rf-step-label").textContent = "Description de l’étape " + numero;

          ligne.querySelector(".rf-step-title").name = "etapes[" + index + "][titre]";
          ligne.querySelector(".rf-step-description").name = "etapes[" + index + "][description]";

          var suppression = ligne.querySelector(".rf-remove-button");
          suppression.disabled = lignes.length === 1;
          suppression.setAttribute("aria-label", "Supprimer l’étape " + numero);
        });
      }

      function ajouterEtape(titre, description, donnerFocus) {
        var fragment = modeleEtape.content.cloneNode(true);
        var ligne = fragment.querySelector(".rf-step-row");
        var champTitre = ligne.querySelector(".rf-step-title");
        var champDescription = ligne.querySelector(".rf-step-description");

        var suffixe = ++identifiantEtape;
        champTitre.id = "etape-titre-" + suffixe;
        champDescription.id = "etape-description-" + suffixe;

        ligne.querySelector(".rf-step-title-label").htmlFor = champTitre.id;
        ligne.querySelector(".rf-step-label").htmlFor = champDescription.id;

        champTitre.value = titre || "";
        champDescription.value = description || "";

        listeEtapes.appendChild(fragment);
        actualiserEtapes();

        if (donnerFocus !== false) champTitre.focus();
      }

      boutonEtape.addEventListener("click", function () {
        ajouterEtape("", "", true);
      });

      listeEtapes.addEventListener("click", function (event) {
        var bouton = event.target.closest(".rf-remove-button");

        if (!bouton || bouton.disabled) return;

        bouton.closest(".rf-step-row").remove();
        actualiserEtapes();
        boutonEtape.focus();
      });

      /* PHOTO : aperçu avant envoi, ou retour à la photo actuelle */

      photo.addEventListener("change", function () {
        if (urlPhoto) URL.revokeObjectURL(urlPhoto);

        urlPhoto = null;
        photo.setCustomValidity("");
        erreurPhoto.textContent = "";

        var fichier = photo.files[0];

        if (!fichier) {
          apercuPhoto.src = photoActuelle;
          return;
        }

        var formatValide = fichier.type === "image/jpeg" || fichier.type === "image/png";
        var tailleValide = fichier.size <= 5 * 1024 * 1024;

        if (!formatValide || !tailleValide) {
          var texte = !formatValide
            ? "Choisissez une image au format JPG ou PNG."
            : "La photo ne doit pas dépasser 5 Mo.";

          photo.value = "";
          photo.setCustomValidity(texte);
          erreurPhoto.textContent = texte;
          apercuPhoto.src = photoActuelle;
          return;
        }

        urlPhoto = URL.createObjectURL(fichier);
        apercuPhoto.src = urlPhoto;
      });

      /* ENVOI */

      // Affiche ou efface le message rouge sous un champ (le <p class="rf-error">
      // juste après lui dans le code HTML).
      function afficherErreurChamp(champ, message) {
        var erreur = champ.nextElementSibling;

        if (erreur && erreur.classList.contains("rf-error")) {
          erreur.textContent = message;
        }
      }

      // Vérifie tous les champs obligatoires du formulaire (sauf la photo,
      // facultative ici) et affiche un message sous ceux restés vides.
      function verifierChampsObligatoires() {
        var tousRemplis = true;
        var premierChampVide = null;

        formulaire.querySelectorAll(
          'input[required]:not([type="file"]), select[required], textarea[required]'
        ).forEach(function (champ) {
          var vide = champ.value.trim() === "";
          afficherErreurChamp(champ, vide ? "Ce champ est obligatoire." : "");

          if (vide) {
            tousRemplis = false;
            if (!premierChampVide) premierChampVide = champ;
          }
        });

        // Ramène la personne vers le premier champ vide pour qu'elle voie le message.
        if (premierChampVide) {
          premierChampVide.scrollIntoView({ behavior: "smooth", block: "center" });
          premierChampVide.focus();
        }

        return tousRemplis;
      }

      formulaire.addEventListener("input", function (event) {
        if (event.target.matches('input[type="text"], textarea')) {
          event.target.setCustomValidity("");
        }

        afficherErreurChamp(event.target, "");
      });

      formulaire.addEventListener("change", function (event) {
        afficherErreurChamp(event.target, "");
      });

      formulaire.addEventListener("submit", function (event) {
        var champsRemplis = verifierChampsObligatoires();

        formulaire.querySelectorAll('input[type="text"][required], textarea[required]').forEach(function (champ) {
          champ.setCustomValidity(champ.value.trim() ? "" : "Veuillez renseigner ce champ.");
        });

        var nomsDejaUtilises = {};

        listeIngredients.querySelectorAll(".rf-ingredient-name").forEach(function (champ) {
          var nom = champ.value.trim().toLocaleLowerCase("fr");

          if (nom && nomsDejaUtilises[nom]) {
            champ.setCustomValidity("Cet ingrédient est déjà présent dans la recette.");
          }

          nomsDejaUtilises[nom] = true;
        });

        if (!champsRemplis || !formulaire.reportValidity()) {
          event.preventDefault();
        }
      });

      // Ingrédients et étapes déjà enregistrés pour cette recette.
      <?php foreach ($donnees['ingredients'] as $ingredient): ?>
        ajouterIngredient(
          <?= json_encode($ingredient['nom']) ?>,
          <?= json_encode($ingredient['quantite']) ?>,
          <?= json_encode($ingredient['unite']) ?>,
          false
        );
      <?php endforeach; ?>

      <?php foreach ($donnees['etapes'] as $etape): ?>
        ajouterEtape(
          <?= json_encode($etape['titre']) ?>,
          <?= json_encode($etape['description']) ?>,
          false
        );
      <?php endforeach; ?>
    </script>

  <?php endif; ?>

</body>
</html>
