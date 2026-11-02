<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';   // contient $base_url

/* ───────────────── TRAITEMENT FORMULAIRE ───────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {

    /* Champs texte */
    $num_apogee       = trim($_POST['num_apogee'] ?? '');
    $nom              = trim($_POST['nom'] ?? '');
    $prenom           = trim($_POST['prenom'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password']         ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $filiere_id       = $_POST['filiere_id']       ?? '';
    $modulesChoisis   = $_POST['modules']          ?? [];

    /* Validation basique */
    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (empty($modulesChoisis)) {
        $error = "Veuillez sélectionner au moins un module.";
    } elseif (empty($_FILES['photo']['name'])) {
        $error = "Veuillez sélectionner une photo d'identité.";
    } else {

        /* Vérifier doublon apogée / email */
        $dup = $pdo->prepare("SELECT id FROM etudiants WHERE num_apogee = ? OR email = ?");
        $dup->execute([$num_apogee, $email]);
        if ($dup->fetch()) {
            $error = "Un étudiant avec ce numéro Apogée ou cet email existe déjà.";
        }
    }

    /* Si pas d’erreur à ce stade, on continue */
    if (empty($error)) {

        /* ────── TRAITEMENT DE LA PHOTO ────── */
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $photo         = $_FILES['photo'];
        $photo_path    = null;

        if (!in_array($photo['type'], $allowed_types)) {
            $error = "Format de photo non pris en charge (JPG ou PNG).";
        } elseif ($photo['error'] !== UPLOAD_ERR_OK) {
            $error = "Erreur lors de l’upload de la photo.";
        } else {
            // Création dossier /photos si besoin
            $upload_dir = __DIR__ . '/photos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Nom unique : photo_apogee.ext
            $ext        = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $file_name  = 'photo_' . $num_apogee . '.' . $ext;
            $dest       = $upload_dir . $file_name;

            if (move_uploaded_file($photo['tmp_name'], $dest)) {
                $photo_path = 'photos/' . $file_name; // chemin relatif stocké en BDD
            } else {
                $error = "Impossible de sauvegarder la photo.";
            }
        }
    }

    /* ────── INSERTION DE L’ÉTUDIANT + MODULES ────── */
    if (empty($error)) {
        /* 1️⃣ étudiant */
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins  = $pdo->prepare(
            "INSERT INTO etudiants
             (num_apogee, nom, prenom, email, password_hash, filiere_id, photo)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $ins->execute([
            $num_apogee, $nom, $prenom, $email, $hash, $filiere_id, $photo_path
        ]);
        $etudiant_id = $pdo->lastInsertId();

        /* 2️⃣ modules sélectionnés */
        $in   = implode(',', array_fill(0, count($modulesChoisis), '?'));
        $mods = $pdo->prepare("SELECT id, semestre FROM modules WHERE id IN ($in)");
        $mods->execute($modulesChoisis);
        $modsData = $mods->fetchAll(PDO::FETCH_KEY_PAIR);

        $link = $pdo->prepare(
            "INSERT INTO etudiant_modules (etudiant_id, module_id, semestre)
             VALUES (?, ?, ?)"
        );
        foreach ($modsData as $module_id => $semestre) {
            $link->execute([$etudiant_id, $module_id, $semestre]);
        }

        $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    }
}

/* ───────── RÉCUPÉRATION FILIÈRES ───────── */
$filieres = $pdo->query("SELECT id, code, nom FROM filieres ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>

<!--  ******* FORMULAIRE HTML ******* -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header"><h4>Inscription Étudiant</h4></div>
        <div class="card-body">

<?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
<?php elseif (!empty($success)): ?>
          <div class="alert alert-success"><?= $success ?></div>
          <a href="index.php" class="btn btn-primary">Se connecter</a>
<?php else: ?>
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="register" value="1">

            <!-- champs classiques -->
            <div class="form-group">
              <label>Numéro Apogée</label>
              <input type="text" name="num_apogee" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Nom</label>
              <input type="text" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Prénom</label>
              <input type="text" name="prenom" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>

            <!-- photo -->
            <div class="form-group">
              <label>Photo d'identité (JPG ou PNG)</label>
              <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="form-control" required>
            </div>

            <!-- filière -->
            <div class="form-group">
              <label>Filière</label>
              <select name="filiere_id" id="filiere-select" class="form-control" required>
                <option value="">-- Sélectionnez --</option>
<?php foreach ($filieres as $f): ?>
                <option value="<?= $f['id'] ?>">
                  <?= htmlspecialchars($f['code'].' - '.$f['nom']) ?>
                </option>
<?php endforeach; ?>
              </select>
            </div>

            <!-- modules (boutons dynamiques) -->
            <div class="form-group">
              <label>Modules</label>
              <div id="module-buttons" class="d-flex flex-wrap gap-2"></div>
              <small class="form-text text-muted">Cliquez pour sélectionner un ou plusieurs modules.</small>
            </div>

            <!-- mots de passe -->
            <div class="form-group">
              <label>Mot de passe</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Confirmer le mot de passe</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">S’inscrire</button>
            <a href="index.php" class="btn btn-link">Déjà inscrit ? Se connecter</a>
          </form>
<?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS boutons modules (inchangé) -->
<script>
const filiereSelect = document.getElementById('filiere-select');
const moduleButtons = document.getElementById('module-buttons');

filiereSelect.addEventListener('change', function () {
    const fid = this.value;
    moduleButtons.innerHTML = '';

    if (!fid) return;

   fetch('/ProjetPHP/get_modules_by_filiere.php?filiere_id=' + encodeURIComponent(fid))
        .then(r => r.json())
        .then(data => {
            data.forEach(m => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-primary btn-sm';
                btn.textContent = m.semestre + ' · ' + m.code + ' – ' + m.nom;
                btn.dataset.id = m.id;

                btn.addEventListener('click', () => {
                    btn.classList.toggle('active');
                    const existing = document.querySelector('input[name="modules[]"][value="' + m.id + '"]');
                    if (existing) {
                        existing.remove();
                    } else {
                        const hidden = document.createElement('input');
                        hidden.type  = 'hidden';
                        hidden.name  = 'modules[]';
                        hidden.value = m.id;
                        moduleButtons.appendChild(hidden);
                    }
                });
                moduleButtons.appendChild(btn);
            });
        })
        .catch(console.error);
});
</script>

<style>
.btn.active {
  background-color:#0d6efd; color:#fff; border-color:#0d6efd;
}
</style>

<?php require_once 'includes/footer.php'; ?>




