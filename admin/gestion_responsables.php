<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

/* ───── AJOUT ───── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO responsables (nom, prenom, email, telephone)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['nom'], $_POST['prenom'],
        $_POST['email'], $_POST['telephone']
    ]);
    header('Location: gestion_responsables.php?msg=ajout_ok');
    exit;
}

/* ───── SUPPRESSION ───── */
if (isset($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM responsables WHERE id = ?");
    $stmt->execute([$_GET['supprimer']]);
    header('Location: gestion_responsables.php?msg=suppr_ok');
    exit;
}

/* ───── LISTE ───── */
$responsables = $pdo->query(
    "SELECT * FROM responsables ORDER BY nom, prenom"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
  <h2>Gestion des responsables</h2>

  <?php if (!empty($_GET['msg'])): ?>
    <?php if ($_GET['msg']==='ajout_ok'): ?>
      <div class="alert alert-success">Responsable ajouté !</div>
    <?php elseif ($_GET['msg']==='suppr_ok'): ?>
      <div class="alert alert-success">Responsable supprimé.</div>
    <?php elseif ($_GET['msg']==='edit_ok'): ?>
      <div class="alert alert-success">Responsable modifié.</div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- formulaire ajout -->
  <div class="card mb-4">
    <div class="card-header"><h4>Ajouter un responsable</h4></div>
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Nom</label>
            <input class="form-control" name="nom" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Prénom</label>
            <input class="form-control" name="prenom" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="col-md-6 mb-3">
            <label>Téléphone</label>
            <input class="form-control" name="telephone">
          </div>
        </div>
        <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
      </form>
    </div>
  </div>

  <!-- liste responsables -->
  <div class="card">
    <div class="card-header"><h4>Liste des responsables</h4></div>
    <div class="card-body table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($responsables as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['nom']) ?></td>
            <td><?= htmlspecialchars($r['prenom']) ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '—') ?></td>
            <td><?= htmlspecialchars($r['telephone'] ?? '—') ?></td>
            <td>
              <a href="edit_responsable.php?id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
              <a href="?supprimer=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Supprimer ce responsable ?');">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
