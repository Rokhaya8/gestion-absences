<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: gestion_responsables.php'); exit; }

/* Récupération */
$stmt = $pdo->prepare("SELECT * FROM responsables WHERE id = ?");
$stmt->execute([$id]);
$r = $stmt->fetch();
if (!$r) { header('Location: gestion_responsables.php'); exit; }

/* Mise à jour */
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $up = $pdo->prepare(
        "UPDATE responsables
         SET nom=?, prenom=?, email=?, telephone=?
         WHERE id=?"
    );
    $up->execute([
        $_POST['nom'], $_POST['prenom'],
        $_POST['email'], $_POST['telephone'], $id
    ]);
    header('Location: gestion_responsables.php?msg=edit_ok');
    exit;
}
?>

<div class="container mt-4">
  <h2>Modifier responsable</h2>
  <form method="POST">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Nom</label>
        <input class="form-control" name="nom" value="<?= htmlspecialchars($r['nom']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label>Prénom</label>
        <input class="form-control" name="prenom" value="<?= htmlspecialchars($r['prenom']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($r['email']) ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label>Téléphone</label>
        <input class="form-control" name="telephone" value="<?= htmlspecialchars($r['telephone']) ?>">
      </div>
    </div>
    <button class="btn btn-primary">Enregistrer</button>
    <a href="gestion_responsables.php" class="btn btn-secondary">Annuler</a>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
