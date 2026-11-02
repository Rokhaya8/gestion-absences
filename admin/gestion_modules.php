<?php
/* admin/gestion_modules.php */
session_start();

require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

try {
    /* ───── AJOUT D’UN MODULE ───── */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
        $stmt = $pdo->prepare(
            "INSERT INTO modules (code, nom, semestre, filiere_id, responsable_id)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $_POST['code'],
            $_POST['nom'],
            $_POST['semestre'],
            $_POST['filiere_id'],
            $_POST['responsable_id']
        ]);

        header('Location: gestion_modules.php?msg=ajout_ok');
        exit;
    }

    /* ───── SUPPRESSION D’UN MODULE ───── */
    if (isset($_GET['supprimer'])) {
        $stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
        $stmt->execute([$_GET['supprimer']]);

        header('Location: gestion_modules.php?msg=suppr_ok');
        exit;
    }

    /* ───── RÉCUPÉRATION DES DONNÉES ───── */
    $modules = $pdo->query(
        "SELECT m.*, f.nom AS filiere_nom,
                r.nom AS responsable_nom, r.prenom AS responsable_prenom
         FROM modules m
         JOIN filieres f ON m.filiere_id = f.id
         JOIN responsables r ON m.responsable_id = r.id
         ORDER BY f.nom, m.semestre, m.nom"
    )->fetchAll(PDO::FETCH_ASSOC);

    $filieres      = $pdo->query("SELECT * FROM filieres ORDER BY nom")->fetchAll();
    $responsables  = $pdo->query("SELECT * FROM responsables ORDER BY nom")->fetchAll();

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<div class="container mt-4">
    <h2>Gestion des modules</h2>

    <?php if (!empty($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'ajout_ok'): ?>
            <div class="alert alert-success">Module ajouté avec succès.</div>
        <?php elseif ($_GET['msg'] === 'suppr_ok'): ?>
            <div class="alert alert-success">Module supprimé avec succès.</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ───────── FORMULAIRE D’AJOUT ───────── -->
    <div class="card mb-4">
        <div class="card-header"><h4>Ajouter un module</h4></div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Semestre</label>
                        <select name="semestre" class="form-control" required>
                            <option value="S1">Semestre 1</option>
                            <option value="S2">Semestre 2</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Filière</label>
                        <select name="filiere_id" class="form-control" required>
                            <?php foreach ($filieres as $f): ?>
                                <option value="<?= $f['id'] ?>">
                                    <?= htmlspecialchars($f['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Responsable</label>
                        <select name="responsable_id" class="form-control" required>
                            <?php foreach ($responsables as $r): ?>
                                <option value="<?= $r['id'] ?>">
                                    <?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- ───────── LISTE DES MODULES ───────── -->
    <div class="card">
        <div class="card-header"><h4>Liste des modules</h4></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Semestre</th>
                        <th>Filière</th>
                        <th>Responsable</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['code']) ?></td>
                            <td><?= htmlspecialchars($m['nom']) ?></td>
                            <td><?= htmlspecialchars($m['semestre']) ?></td>
                            <td><?= htmlspecialchars($m['filiere_nom']) ?></td>
                            <td><?= htmlspecialchars($m['responsable_prenom'] . ' ' . $m['responsable_nom']) ?></td>
                            <td>
                                 <a href="edit_module.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">
                                     Modifier
                                     
                                </a> 

                                <a href="?supprimer=<?= $m['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Confirmer la suppression ?');">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
