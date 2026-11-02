<?php
session_start();
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: presence_direct.php?token=' . urlencode($_GET['token']));
    exit;
}

require_once '../config/db.php';

/* ── 1. Récupération du token ─────────────────────────── */
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = "❌ QR code invalide : token manquant.";
    $status  = "error";
}
elseif (!isset($_SESSION['etudiant'])) {
    // Étudiant non connecté : on garde le token puis on redirige
    $_SESSION['pending_token'] = $token;
    header("Location: login_presence.php?token=" . urlencode($token));
    exit;
}
else {
    /* ── 2. Étudiant connecté : on note sa présence ───── */
    $etudiant    = $_SESSION['etudiant'];
    $etudiant_id = $etudiant['id'];

    // Récupérer la séance liée au token
    $stmt = $pdo->prepare("SELECT id FROM seances WHERE qr_token = ?");
    $stmt->execute([$token]);
    $seance = $stmt->fetch();

    if (!$seance) {
        $message = "❌ Séance non trouvée.";
        $status  = "error";
    } else {
        $seance_id = $seance['id'];

        // Vérifier si la présence est déjà enregistrée dans presences_temp
        $stmt = $pdo->prepare("SELECT id FROM presences_temp WHERE seance_id = ? AND etudiant_id = ?");
        $stmt->execute([$seance_id, $etudiant_id]);

        if ($stmt->fetch()) {
            $message = "⚠️ Tu as déjà validé ta présence pour cette séance.";
            $status  = "warning";
        } else {
            // Inscrire la présence dans la table temporaire
            $stmt = $pdo->prepare(
                "INSERT INTO presences_temp (seance_id, etudiant_id) VALUES (?, ?)"
            );
            $stmt->execute([$seance_id, $etudiant_id]);

            $message = "✅ Présence enregistrée avec succès.";
            $status  = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation de présence</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;background:#f9f9f9;
             display:flex;justify-content:center;align-items:center;
             height:100vh;margin:0;}
        .card{background:#fff;padding:30px 40px;border-radius:12px;
              box-shadow:0 0 20px rgba(0,0,0,0.05);text-align:center;
              max-width:400px}
        .card.success{border-left:5px solid #28a745}
        .card.warning{border-left:5px solid #ffc107}
        .card.error  {border-left:5px solid #dc3545}
        .emoji{font-size:48px;margin-bottom:15px}
        h2{font-size:22px;margin:0 0 10px;color:#333}
        p{font-size:16px;color:#555;margin:5px 0 0}
        .footer{margin-top:20px;font-size:13px;color:#aaa}
    </style>
</head>
<body>
    <div class="card <?= $status ?? 'error' ?>">
        <div class="emoji">
            <?= match($status ?? '') {
                'success' => '✅',
                'warning' => '⚠️',
                'error'   => '❌',
                default   => 'ℹ️'
            }; ?>
        </div>
        <h2>Présence</h2>
        <p><?= htmlspecialchars($message ?? "Une erreur inconnue est survenue.") ?></p>
        <div class="footer">Université XYZ • <?= date('Y') ?></div>
    </div>
</body>
</html>
