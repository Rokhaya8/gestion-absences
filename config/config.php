<?php
/*
 * Configuration de l'application.
 * Les valeurs peuvent aussi être définies par des variables d'environnement.
 */

// ── Base de données MySQL ──
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3307');   // port de MySQL (3306 par défaut dans XAMPP)
define('DB_NAME', getenv('DB_NAME') ?: 'gestion_absences');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// ── Chemin de l'application, calculé automatiquement ──
// Exemple : projet dans C:\xampp\htdocs\ProjetPHP  →  BASE_URL = '/ProjetPHP/'
$__projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$__docRoot     = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$__relative    = ($__docRoot && str_starts_with($__projectRoot, $__docRoot))
    ? substr($__projectRoot, strlen($__docRoot))
    : '';
define('BASE_URL', rtrim($__relative, '/') . '/');

// Adresse complète, utilisée dans les QR codes.
// Pour scanner depuis un téléphone, ouvrir l'application avec l'adresse IP du PC
// (ex. http://192.168.1.20/ProjetPHP/) plutôt que localhost.
$__scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('APP_URL', $__scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL);