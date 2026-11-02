<?php
function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit();
    }
}

function requireEtudiant() {
    if (!isset($_SESSION['etudiant_id'])) {
        header('Location: index.php');
        exit();
    }
}

// Déplacer ces fonctions en haut du fichier pour qu'elles soient disponibles partout
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function isEtudiant() {
    return isset($_SESSION['etudiant_id']);
}

// Initialiser la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>