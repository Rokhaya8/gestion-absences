<?php
session_start();

require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID du module non valide.');
}

$id = $_GET['id'];

// Récupération du module
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->execute([$id]);
$module = $stmt->fetch();

if (!$module) {
    die('Module introuvable.');
}

// Récupération des filières et responsables pour les listes déroulantes
$filieres = $pdo->query("SELECT * FROM filieres ORDER BY nom")->fetchAll();
$responsables = $pdo->query("SELECT * FROM responsables ORDER BY nom")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $stmt = $pdo->prepare(
        "UPDATE modules
         SET code = ?, nom = ?, semestre = ?, filiere_id = ?, responsable_id = ?
         WHERE id = ?"
    );
    $stmt->execute([
        $_POST['code'],
        $_POST['nom'],
        $_POST['semestre'],
        $_POST['filiere_id'],
        $_POST['responsable_id'],
        $id
    ]);

    header('Location: gestion_modules.php?msg=modif_ok');
    exit;
}
?>

<div class="container mt-4">
    <h2>Modifier le module</h2>

    <form method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Code</label>
                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($module['code']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($module['nom']) ?>" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Semestre</label>
                <select name="semestre" class="form-control" required>
                    <option value="S1" <?= $module['semestre'] === 'S1' ? 'selected' : '' ?>>Semestre 1</option>
                    <option value="S2" <?= $module['semestre'] === 'S2' ? 'selected' : '' ?>>Semestre 2</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Filière</label>
                <select name="filiere_id" class="form-control" required>
                    <?php foreach ($filieres as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= $f['id'] == $module['filiere_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Responsable</label>
                <select name="responsable_id" class="form-control" required>
                    <?php foreach ($responsables as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $module['responsable_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" name="modifier" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="gestion_modules.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
