<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/db.php';

$erreur = '';

// Récupération du token (depuis URL ou session)
$token = $_GET['token'] ?? ($_SESSION['pending_token'] ?? '');

if ($token) {
    $_SESSION['pending_token'] = $token; // au cas où on doive le rappeler plus tard
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_apogee   = $_POST['num_apogee'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (!empty($num_apogee) && !empty($mot_de_passe)) {
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE num_apogee = ?");
        $stmt->execute([$num_apogee]);
        $etudiant = $stmt->fetch();

        if ($etudiant && password_verify($mot_de_passe, $etudiant['password_hash'])) {
            $_SESSION['etudiant'] = $etudiant;
            unset($_SESSION['pending_token']);
            header("Location: presence_direct.php?token=" . urlencode($token));
            exit;
        } else {
            $erreur = "Apogée ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Étudiant</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 350px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            background-color: #2e86de;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #1e6fcd;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 0.9em;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Connexion Étudiant</h2>

        <?php if (!empty($erreur)): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="login_presence.php?token=<?= urlencode($token) ?>">
            <label for="num_apogee">Numéro Apogée :</label>
            <input type="text" name="num_apogee" id="num_apogee" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required>

            <button type="submit">Se connecter</button>
        </form>

        <div class="footer">Université XYZ • 2025</div>
    </div>
</body>
</html>
