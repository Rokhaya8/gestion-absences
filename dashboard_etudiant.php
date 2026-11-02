<?php
/* ------------------------------------------------------------------
   Tableau de bord étudiant
-------------------------------------------------------------------*/
session_start();
require_once 'includes/auth.php';
requireEtudiant();                 // Vérifie que l'utilisateur est un étudiant
require_once 'config/db.php';
require_once 'includes/header.php';

/* ── Récupérer les informations de l'étudiant ───────────────────── */
$etudiant_id = $_SESSION['etudiant_id'];

$stmt = $pdo->prepare("
    SELECT nom, prenom, photo, num_apogee
    FROM etudiants
    WHERE id = ?
");
$stmt->execute([$etudiant_id]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

/* Chemin de la photo ou image par défaut */
$photo = (!empty($profil['photo']) && file_exists($profil['photo']))
         ? $profil['photo']
         : 'assets/defaut.jpg';

/* ── Récupérer la liste des modules ─────────────────────────────── */
$modulesStmt = $pdo->query("SELECT id, nom FROM modules ORDER BY nom");
$modules     = $modulesStmt->fetchAll(PDO::FETCH_ASSOC);

/* ── Récupérer les absences ─────────────────────────────────────── */
$absencesStmt = $pdo->prepare("
    SELECT a.*, s.date_seance, s.heure_debut, s.heure_fin, m.nom AS module_nom
    FROM absences a
    JOIN seances  s ON a.seance_id = s.id
    JOIN modules  m ON s.module_id = m.id
    WHERE a.etudiant_id = ?
    ORDER BY s.date_seance DESC
");
$absencesStmt->execute([$etudiant_id]);
$absences = $absencesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Tableau de bord - Étudiant</h2>

    <!-- Bloc de bienvenue + photo -->
    <div class="d-flex align-items-center mb-3">
        <img src="<?= htmlspecialchars($photo) ?>"
             alt="Photo d'identité"
             style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
        <p class="mb-0 fs-5">
            Bienvenue, <?= htmlspecialchars($profil['prenom']) ?> <?= htmlspecialchars($profil['nom']) ?>
        </p>
    </div>

    <!-- Message après upload -->
    <?php if (isset($_GET['upload']) && $_GET['upload'] === 'success'): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>
            Votre justificatif a bien été envoyé et sera traité par l’administration.
        </div>
    <?php elseif (isset($_GET['upload']) && $_GET['upload'] === 'error'): ?>
        <div class="alert alert-danger"><i class="bi bi-x-circle me-1"></i>
            Une erreur est survenue lors de l’envoi du justificatif.
        </div>
    <?php endif; ?>

    <!-- Formulaire d’upload de justificatif -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="bi bi-file-earmark-plus me-2 fs-4"></i>
            <h5 class="mb-0">Déposer un justificatif d’absence</h5>
        </div>
        <div class="card-body">
            <form action="gestion-absences/upload_justificatif.php"
                  method="post"
                  enctype="multipart/form-data"
                  class="row g-3 needs-validation"
                  novalidate>
                <!-- Date -->
                <div class="col-md-4">
                    <label for="date_absence" class="form-label">Date d’absence</label>
                    <input type="date"
                           name="date_absence"
                           id="date_absence"
                           class="form-control"
                           required>
                </div>

                <!-- Module -->
                <div class="col-md-4">
                    <label for="module_id" class="form-label">Module concerné</label>
                    <select name="module_id"
                            id="module_id"
                            class="form-select"
                            required>
                        <option value="" disabled selected>-- Sélectionner --</option>
                        <?php foreach ($modules as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= htmlspecialchars($m['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fichier -->
                <div class="col-md-4">
                    <label for="justificatif" class="form-label">
                        Fichier justificatif (PDF ou image)
                    </label>
                    <input type="file"
                           name="justificatif"
                           id="justificatif"
                           class="form-control"
                           accept=".pdf,image/png,image/jpeg"
                           required>
                </div>

                <!-- Bouton -->
                <div class="col-12 mt-3 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-1"></i> Envoyer le justificatif
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des absences -->
    <h4 class="mt-5">Vos absences</h4>

    <?php if (empty($absences)): ?>
        <div class="alert alert-info">
            Aucune absence enregistrée pour le moment.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Module</th>
                        <th>Statut</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($absences as $a): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($a['date_seance'])) ?></td>
                        <td><?= substr($a['heure_debut'], 0, 5) ?> - <?= substr($a['heure_fin'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($a['module_nom']) ?></td>
                        <td>
                            <?php
                            $badgeClass = [
                                'Présent'  => 'success',
                                'Absent'   => 'danger',
                                'Justifié' => 'warning'
                            ][$a['statut']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= $a['statut'] ?>
                            </span>
                        </td>
                        <td><?= $a['commentaire'] ?? '---' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
