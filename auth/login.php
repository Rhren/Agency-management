<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require '../includes/db.php';


if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $mdp = $_POST['mot_de_passe'];
    $sql = "SELECT * FROM utilisateur WHERE email=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($mdp, $user['mot_de_passe']))
    {
        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['role'] = $user['id_role'];
        if($user['id_role'] == 1)
        {
             header("Location: ../membre/dashboard.php");
             exit;
        }
        else if($user['id_role'] == 2)
        {
            header("Location: ../admin/dashboard.php");
            exit;
        }
    }
    else 
    {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion · MissionFlow</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="auth-body">

  <div class="auth-card">

    <a href="../index.php" class="auth-logo">MissionFlow</a>

    <h1 class="auth-title">Bon retour.</h1>
    <p class="auth-sub">Connectez-vous à votre espace.</p>

    <?php if(isset($_GET['success'])): ?>
      <div class="alert alert--success">Inscription réussie, connectez-vous.</div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
      <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="votre@email.com" required>
      </div>

      <div class="field">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" required>
      </div>

      <button class="btn-submit" name="login">Connexion</button>

    </form>

    <p class="auth-switch">
      Pas encore de compte ? <a href="register.php">S'inscrire</a>
    </p>

  </div>

</body>
</html>