<?php
session_start();
require_once 'includes/auth.php';
requireAdmin(); // Vérifie que l'utilisateur est un admin
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<div class="container mt-4">
    <h2>Tableau de bord - Administrateur</h2>
    <p>Bienvenue, <?= $_SESSION['admin_prenom'] ?> <?= $_SESSION['admin_nom'] ?></p>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestion des filières</h5>
                    <p class="card-text">Ajouter, modifier ou supprimer des filières.</p>
                    <a href="admin/gestion_filieres.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestion des modules</h5>
                    <p class="card-text">Gérer les modules et leurs responsables.</p>
                    <a href="admin/gestion_modules.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
           <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestion des responsables</h5>
                    <p class="card-text">Ajouter, modifier ou supprimer des resp.</p>
                    <a href="admin/gestion_responsables.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Rapports PDF</h5>
            <p class="card-text">Générer les rapports d'absences au format PDF.</p>
            <a href="admin/rapport_form.php" class="btn btn-outline-primary">📝 Générer un rapport PDF</a>
        </div>
    </div>
</div>



        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestion des étudiants</h5>
                    <p class="card-text">Voir et gérer les étudiants inscrits.</p>
                    <a href="admin/gestion_etudiants.php" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Gestion des absences</h5>
            <p class="card-text">Consulter et gérer les absences.</p>
            <a href="admin/gestion_absences.php" class="btn btn-primary mb-2">Accéder</a><br>
            <a href="admin/generer_qr_view.php" class="btn btn-outline-secondary btn-sm">Générer un QR Code pour une séance</a>
        </div>
    </div>
</div>



<?php require_once 'includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';


// Récupération des filières et modules
$stmt = $pdo->query("
    SELECT f.id AS filiere_id, f.nom AS filiere_nom, m.nom AS module_nom
    FROM filieres f
    LEFT JOIN modules m ON f.id = m.filiere_id
    ORDER BY f.nom, m.nom
");

$data = $stmt->fetchAll();

// Organiser par filière
$filieresModules = [];
foreach ($data as $row) {
    $filieresModules[$row['filiere_nom']][] = $row['module_nom'];
}
?>

<div class="container mt-4">
    <h2>Filières et Modules</h2>

    <?php foreach ($filieresModules as $filiere => $modules): ?>
        <div class="card my-3">
            <div class="card-header bg-primary text-white">
                <strong><?= htmlspecialchars($filiere) ?></strong>
            </div>
            <div class="card-body">
                <?php if (!empty(array_filter($modules))): ?>
                    <ul class="list-group">
                        <?php foreach ($modules as $module): ?>
                            <li class="list-group-item"><?= htmlspecialchars($module) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun module associé.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
