<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

/* ───────── FILTRES ───────── */
$module_id   = $_GET['module_id']   ?? '';
$etudiant_id = $_GET['etudiant_id'] ?? '';

$where  = [];
$params = [];

if ($module_id !== '') {
    $where[]  = 's.module_id = ?';
    $params[] = $module_id;
}
if ($etudiant_id !== '') {
    $where[]  = 'a.etudiant_id = ?';
    $params[] = $etudiant_id;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ───────── DONNÉES ABSENCES ───────── */
$stmt = $pdo->prepare("
    SELECT a.*,
           e.nom  AS etudiant_nom,
           e.prenom AS etudiant_prenom,
           m.nom  AS module_nom,
           m.code AS module_code,
           s.date_seance,
           s.heure_debut,
           s.heure_fin
    FROM absences a
    JOIN etudiants  e ON a.etudiant_id = e.id
    JOIN seances    s ON a.seance_id  = s.id
    JOIN modules    m ON s.module_id  = m.id
    $whereClause
    ORDER BY s.date_seance DESC, e.nom
");
$stmt->execute($params);
$absences = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ───────── LISTES MODULES & ÉTUDIANTS ───────── */
$modules    = $pdo->query('SELECT * FROM modules   ORDER BY nom')->fetchAll();
$etudiants  = $pdo->query('SELECT * FROM etudiants ORDER BY nom')->fetchAll();
?>

<div class="container">
    <h2>Gestion des absences</h2>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header"><h4>Filtrer les absences</h4></div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <!-- Module -->
                    <div class="col-md-6 mb-3">
                        <label>Module</label>
                        <select name="module_id" class="form-control">
                            <option value="">Tous les modules</option>
                            <?php foreach ($modules as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= $m['id'] == $module_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['code'] . ' - ' . $m['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Étudiant -->
                    <div class="col-md-6 mb-3">
                        <label>Étudiant</label>
                        <select name="etudiant_id" class="form-control">
                            <option value="">Tous les étudiants</option>
                            <?php foreach ($etudiants as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= $e['id'] == $etudiant_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="gestion_absences.php" class="btn btn-secondary">Réinitialiser</a>
            </form>
        </div>
    </div>

    <!-- Tableau des absences -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Étudiant</th>
                        <th>Module</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences as $a): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($a['date_seance'])) ?></td>
                            <td><?= substr($a['heure_debut'], 0, 5) . ' - ' . substr($a['heure_fin'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($a['etudiant_prenom'] . ' ' . $a['etudiant_nom']) ?></td>
                            <td><?= htmlspecialchars($a['module_code'] . ' - ' . $a['module_nom']) ?></td>
                            <td>
                                <span class="badge badge-<?= 
                                    $a['statut'] === 'Présent'   ? 'success' :
                                    ($a['statut'] === 'Justifié' ? 'warning' : 'danger') ?>">
                                    <?= htmlspecialchars($a['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_absence.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
