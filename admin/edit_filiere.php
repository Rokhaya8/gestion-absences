<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: gestion_filieres.php');
    exit;
}

$id = (int) $_GET['id'];

/* Récupération des données */
$stmt = $pdo->prepare('SELECT * FROM filieres WHERE id = ?');
$stmt->execute([$id]);
$filiere = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$filiere) {
    header('Location: gestion_filieres.php?msg=notfound');
    exit;
}

/* Mise à jour */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $nom  = $_POST['nom'];
    $desc = $_POST['description'];

    $up = $pdo->prepare(
        'UPDATE filieres SET code = ?, nom = ?, description = ? WHERE id = ?'
    );
    $up->execute([$code, $nom, $desc, $id]);

    header('Location: gestion_filieres.php?msg=edit_ok');
    exit;
}
?>

<div class="container mt-4">
    <h2>Modifier la filière</h2>
    <form method="POST">
        <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" class="form-control"
                   value="<?= htmlspecialchars($filiere['code']) ?>" required>
        </div>
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control"
                   value="<?= htmlspecialchars($filiere['nom']) ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"
                      rows="3"><?= htmlspecialchars($filiere['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="gestion_filieres.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
