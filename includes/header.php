<?php
// Inclure auth.php au début pour avoir accès aux fonctions d'authentification
require_once __DIR__ . '/auth.php';

// Définir le chemin de base (adapte-le selon ton arborescence)
require_once __DIR__ . '/../config/config.php';
$base_url = BASE_URL; // calculé automatiquement dans config/config.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des absences</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $base_url ?>assets/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Gestion des absences</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>dashboard_admin.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>logout.php">Déconnexion</a>
                    </li>
                <?php elseif (isEtudiant()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>dashboard_etudiant.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>logout.php">Déconnexion</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>index.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>register.php">Inscription</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
