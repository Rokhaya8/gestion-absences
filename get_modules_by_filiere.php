<?php
require_once 'config/db.php';

if (!isset($_GET['filiere_id'])) {
    http_response_code(400);
    exit('filiere_id manquant');
}

$filiere_id = (int) $_GET['filiere_id'];

$stmt = $pdo->prepare(
    "SELECT id, code, nom, semestre
     FROM modules
     WHERE filiere_id = ?
     ORDER BY semestre, nom"
);
$stmt->execute([$filiere_id]);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));