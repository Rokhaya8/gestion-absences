<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/db.php';   // <-- ta connexion PDO $pdo

// 1) Vérifications de base
if (!isset($_SESSION['etudiant_id'], $_SESSION['apogee'])) {
    http_response_code(401);
    exit('Non autorisé');
}

$etudiantId = (int)$_SESSION['etudiant_id'];
$apogee     = preg_replace('/\D/', '', $_SESSION['apogee']); // on garde seulement les chiffres/lettres utiles

// 2) Validation des champs
$dateAbs   = $_POST['date_absence'] ?? '';
$moduleId  = $_POST['module_id']   ?? '';
$fichier   = $_FILES['justificatif'] ?? null;

$errors = [];

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateAbs)) {
    $errors[] = "Date d’absence invalide.";
}
if (!ctype_digit($moduleId)) {
    $errors[] = "Module manquant.";
}
if (!$fichier || $fichier['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Problème lors de l’upload du fichier.";
}

// 3) Vérification du type MIME / extension
$allowedMime = [
    'application/pdf' => 'pdf',
    'image/png'       => 'png',
    'image/jpeg'      => 'jpg'
];
$mimeType = mime_content_type($fichier['tmp_name'] ?? '');
if (!isset($allowedMime[$mimeType])) {
    $errors[] = "Format de fichier non autorisé.";
}

if ($errors) {
    // À toi de gérer l’affichage plus joliment :)
    foreach ($errors as $e) echo "<p>$e</p>";
    exit;
}

// 4) Génération du chemin : justificatifs/{apogee}/justif_YYYY-MM-DD.ext
$dir = __DIR__ . '/../justificatifs/' . $apogee;
if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
    exit('Impossible de créer le dossier de destination.');
}

$extension = $allowedMime[$mimeType];
$baseName  = "justif_{$dateAbs}.$extension";
$destPath  = $dir . '/' . $baseName;

// Si un justificatif pour la même date existe déjà, on ajoute un suffixe horodaté
if (file_exists($destPath)) {
    $destPath = $dir . "/justif_{$dateAbs}_" . time() . ".$extension";
}

// 5) Déplacement du fichier
if (!move_uploaded_file($fichier['tmp_name'], $destPath)) {
    exit('Erreur lors de la sauvegarde du fichier.');
}

// 6) Enregistrement en BDD
$sql = "INSERT INTO justificatifs (etudiant_id, module_id, date_absence, fichier_path)
        VALUES (:etudiant, :module, :date_abs, :fichier)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':etudiant' => $etudiantId,
    ':module'   => $moduleId,
    ':date_abs' => $dateAbs,
    ':fichier'  => str_replace(__DIR__ . '/../', '', $destPath) // chemin relatif
]);

// 7) Retour vers le dashboard avec message de succès
header('Location: ../dashboard_etudiant.php?upload=success');
exit;
