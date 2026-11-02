<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);

$req = $pdo->prepare("
    SELECT e.photo, e.num_apogee, f.code AS filiere_code
    FROM etudiants e
    JOIN filieres f ON e.filiere_id = f.id
    WHERE e.id = ?
");
$req->execute([$id]);
$data = $req->fetch(PDO::FETCH_ASSOC);

if (!$data || empty($data['photo']) || !file_exists('../'.$data['photo'])) {
    die("Fichier introuvable.");
}

$src = '../'.$data['photo'];                       // chemin source
$ext = pathinfo($src, PATHINFO_EXTENSION);
$destDir = '../photos_par_filiere/'.$data['filiere_code'].'/';
if (!is_dir($destDir)) mkdir($destDir, 0755, true);

$dest = $destDir.'photo_'.$data['num_apogee'].'.'.$ext;
if (!file_exists($dest)) {
    copy($src, $dest);                             // copie pour archivage
}

/* ─── Téléchargement ─── */
header('Content-Type: '.mime_content_type($src));
header('Content-Disposition: attachment; filename="'.basename($dest).'"');
readfile($src);
exit;
