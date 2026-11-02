<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

try {
    // Ajout d'une filière
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_filiere'])) {
        $code = $_POST['code'];
        $nom = $_POST['nom'];
        $description = $_POST['description'];

        $stmt = $pdo->prepare("INSERT INTO filieres (code, nom, description) VALUES (?, ?, ?)");
        $stmt->execute([$code, $nom, $description]);

        header('Location: gestion_filieres.php?msg=ajout_ok');
        exit;
    }

    // Suppression d'une filière
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM filieres WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: gestion_filieres.php?msg=suppr_ok');
        exit;
    }

    // Récupération des filières
    $filieres = $pdo->query("SELECT * FROM filieres ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<div class="container mt-4">
    <h2>Gestion des filières</h2>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'ajout_ok'): ?>
            <div class="alert alert-success">Filière ajoutée avec succès.</div>
        <?php elseif ($_GET['msg'] === 'suppr_ok'): ?>
            <div class="alert alert-success">Filière supprimée avec succès.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Ajouter une filière</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit" name="add_filiere" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Liste des filières</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filieres as $filiere): ?>
                            <tr>
                                <td><?= htmlspecialchars($filiere['code']) ?></td>
                                <td><?= htmlspecialchars($filiere['nom']) ?></td>
                                <td><?= htmlspecialchars($filiere['description'] ?? '---') ?></td>
                                <td>
                                    <a href="<?= $base_url ?>admin/edit_filiere.php?id=<?= $filiere['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <a href="?delete=<?= $filiere['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ?')">Supprimer</a>
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
