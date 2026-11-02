<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Récupérer les filières
$filières = $pdo->query("SELECT nom FROM filieres")->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les modules
$modules = $pdo->query("SELECT nom FROM modules")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container mt-4">
    <h3>Générer un rapport d'absences</h3>
    <form action="../gestion-absences/generer_rapport.php" method="POST">

        <div class="mb-3">
            <label for="filiere" class="form-label">Filière</label>
            <select name="filiere" id="filiere" class="form-control" required>
                <option value="">-- Choisir une filière --</option>
                <?php foreach ($filières as $f): ?>
                    <option value="<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="module" class="form-label">Module</label>
            <select name="module" id="module" class="form-control" required>
                <option value="">-- Choisir un module --</option>
                <?php foreach ($modules as $m): ?>
                    <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Générer le PDF</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
