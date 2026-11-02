<?php
session_start();
require_once(__DIR__ . '/../includes/auth.php');
requireAdmin();
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/header.php');


$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID étudiant manquant.";
    exit;
}

// Requête pour récupérer l'étudiant
$etudiant = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id = ?");
$etudiant->execute([$id]);
$info = $etudiant->fetch();

if (!$info) {
    echo "Étudiant non trouvé.";
    exit;
}

// Requête pour récupérer ses modules
$modules = $pdo->prepare("
    SELECT m.code, m.nom, em.semestre
    FROM etudiant_modules em
    JOIN modules m ON em.module_id = m.id
    WHERE em.etudiant_id = ?
    ORDER BY em.semestre, m.nom
");
$modules->execute([$id]);
$liste = $modules->fetchAll();
?>

<div class="container mt-4">
  <h4>Modules de <?= htmlspecialchars($info['prenom'] . ' ' . $info['nom']) ?></h4>

  <?php if (empty($liste)): ?>
    <div class="alert alert-warning">Aucun module enregistré pour cet étudiant.</div>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($liste as $m): ?>
        <li class="list-group-item">
          <strong><?= htmlspecialchars("S{$m['semestre']}") ?></strong> – 
          <?= htmlspecialchars($m['code']) ?> : 
          <?= htmlspecialchars($m['nom']) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <a href="gestion_etudiants.php" class="btn btn-secondary mt-3">Retour</a>
</div>

<?php require_once(__DIR__ . '/../includes/footer.php'); 
?>
