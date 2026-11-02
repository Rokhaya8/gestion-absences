<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

/* ───────── Recherche ───────── */
$search = $_GET['search'] ?? '';
$where  = '';
$params = [];

if ($search) {
    $where  = "WHERE e.nom LIKE ? OR e.prenom LIKE ? OR e.num_apogee LIKE ?";
    $params = array_fill(0, 3, "%$search%");
}

/* ───────── Récupération étudiants + filière ───────── */
$stmt = $pdo->prepare("
    SELECT e.*, f.nom  AS filiere_nom, f.code AS filiere_code
    FROM etudiants e
    JOIN filieres f ON e.filiere_id = f.id
    $where
    ORDER BY e.nom, e.prenom
");
$stmt->execute($params);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Étudiants</h2>

    <!-- Barre de recherche -->
    <form class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher..."
                   value="<?= htmlspecialchars($search) ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Rechercher</button>
                <a href="gestion_etudiants.php" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

    <!-- Liste -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="thead-light">
              <tr>
                <th>Photo</th>
                <th>Num Apogée</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Filière</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
<?php foreach ($etudiants as $e): ?>
              <tr>
                <td class="text-center">
<?php if ($e['photo'] && file_exists('../'.$e['photo'])): ?>
                  <img src="<?= htmlspecialchars('../'.$e['photo']) ?>" alt="photo"
                       style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                  <br>
                  <a href="download_photo.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-secondary mt-1">
                    Télécharger
                  </a>
<?php else: ?>
                  —
<?php endif; ?>
                </td>
                <td><?= $e['num_apogee'] ?></td>
                <td><?= htmlspecialchars($e['nom']) ?></td>
                <td><?= htmlspecialchars($e['prenom']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= htmlspecialchars($e['filiere_nom']) ?></td>
                <td>
                  <a href="etudiant_modules.php?id=<?= $e['id'] ?>" class="btn btn-info btn-sm">Modules</a>
                </td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
