<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
require_once '../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$qrImage = null;
$qrUrl   = null;
$message = '';
$seance_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id   = $_POST['module_id'];
    $date_seance = $_POST['date_seance'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin   = $_POST['heure_fin'];

    $qr_token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("
        INSERT INTO seances (module_id, date_seance, heure_debut, heure_fin, qr_token)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$module_id, $date_seance, $heure_debut, $heure_fin, $qr_token]);

    $seance_id = $pdo->lastInsertId(); // ✅ on récupère l’ID de la séance

    $qrUrl = APP_URL . "etudiant/presence_direct.php?token=$qr_token";

    $qrCode = new QrCode($qrUrl);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    $qrImage = 'data:image/png;base64,' . base64_encode($result->getString());
    $message = "Séance créée et QR Code généré avec succès.";
}

// Modules pour le menu déroulant
$modules = $pdo->query("SELECT id, nom FROM modules")->fetchAll();
?>

<?php require_once '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Créer une séance et générer un QR code</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="module_id" class="form-label">Module</label>
            <select name="module_id" id="module_id" class="form-control" required>
                <option value="">-- Choisir un module --</option>
                <?php foreach ($modules as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="date_seance" class="form-label">Date de la séance</label>
            <input type="date" name="date_seance" id="date_seance" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="heure_debut" class="form-label">Heure de début</label>
            <input type="time" name="heure_debut" id="heure_debut" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="heure_fin" class="form-label">Heure de fin</label>
            <input type="time" name="heure_fin" id="heure_fin" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Générer le QR Code</button>
    </form>

    <?php if ($qrImage): ?>
        <div class="mt-4">
            <h4>QR Code généré</h4>
            <img src="<?= $qrImage ?>" alt="QR Code" class="img-thumbnail">
            <p class="mt-2">
                <strong>URL scannée :</strong>
                <code><?= htmlspecialchars($qrUrl) ?></code>
            </p>

            <!-- ✅ Bouton pour terminer la séance -->
            <form action="terminer_seance.php" method="POST">
                <input type="hidden" name="seance_id" value="<?= $seance_id ?>">
                <button type="submit" class="btn btn-danger mt-3">
                    Terminer la séance
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
