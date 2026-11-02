<?php
// index.php

require_once 'config/db.php';
require_once 'includes/auth.php'; // Inclure auth.php avant header.php

// auth.php contient déjà session_start() donc pas besoin de le mettre ici

require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login_type'])) {
        if ($_POST['login_type'] === 'admin') {
            // Connexion admin
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $_SESSION['admin_prenom'] = $admin['prenom'];
                header('Location: dashboard_admin.php');
                exit();
            } else {
                $error = "Identifiants incorrects";
            }
        } else {
            // Connexion étudiant
            $num_apogee = $_POST['num_apogee'];
            $password = $_POST['password'];
            
            $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE num_apogee = ?");
            $stmt->execute([$num_apogee]);
            $etudiant = $stmt->fetch();
            
            if ($etudiant && password_verify($password, $etudiant['password_hash'])) {
                $_SESSION['etudiant_id'] = $etudiant['id'];
                $_SESSION['etudiant_nom'] = $etudiant['nom'];
                $_SESSION['etudiant_prenom'] = $etudiant['prenom'];
                 $_SESSION['apogee'] = $etudiant['num_apogee'];
                header('Location: dashboard_etudiant.php');
                exit();
            } else {
                $error = "Identifiants incorrects";
            }
        }
    }
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#admin">Administrateur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#etudiant">Étudiant</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="admin">
                            <form method="POST">
                                <input type="hidden" name="login_type" value="admin">
                                <div class="form-group">
                                    <label for="username">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="etudiant">
                            <form method="POST">
                                <input type="hidden" name="login_type" value="etudiant">
                                <div class="form-group">
                                    <label for="num_apogee">Numéro Apogée</label>
                                    <input type="text" class="form-control" id="num_apogee" name="num_apogee" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                                <a href="register.php" class="btn btn-link">S'inscrire</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>