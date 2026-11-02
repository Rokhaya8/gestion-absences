<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seance_id'])) {
    $seance_id = $_POST['seance_id'];

    // 1. Récupérer tous les étudiants du module concerné
    $stmt = $pdo->prepare("
        SELECT e.id
        FROM etudiants e
        INNER JOIN modules m ON e.filiere_id = m.filiere_id
        INNER JOIN seances s ON s.module_id = m.id
        WHERE s.id = ?
    ");
    $stmt->execute([$seance_id]);
    $etudiants = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 2. Récupérer les présents
    $stmt2 = $pdo->prepare("SELECT etudiant_id FROM presences_temp WHERE seance_id = ?");
    $stmt2->execute([$seance_id]);
    $presents = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // 3. Calculer les absents
    $absents = array_diff($etudiants, $presents);

    // 4. Insérer dans absences
    $stmt3 = $pdo->prepare("INSERT INTO absences (etudiant_id, seance_id) VALUES (?, ?)");
    foreach ($absents as $etudiant_id) {
        $stmt3->execute([$etudiant_id, $seance_id]);
    }

    // 5. Rediriger avec un message
    header("Location: generer_qr_view.php?success=1");
    exit;
} else {
    exit('Accès refusé');
}
