<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require '../includes/db.php';

if(isset($_POST['inscrire']))
{
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
    $check->execute([$email]);

    if($check->rowCount() > 0)
    {
         $error = "Cet email est déjà inscrit.";
    }
    else if(!empty($nom) && !empty($prenom) && !empty($email) && !empty($mdp))
    {
        $id_role = 1;
        $sql = "INSERT INTO utilisateur(nom, prenom, email, mot_de_passe, id_role)
        VALUES(?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $mdp, $id_role]);
        header("location:login.php");
        exit;
    }
    else
    {
        $error = "Veuillez remplir tous les champs.";
    }  
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription · MissionFlow</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="auth-body">

  <div class="auth-card">

    <a href="../index.php" class="auth-logo">MissionFlow</a>

    <h1 class="auth-title">Créer un compte.</h1>
    <p class="auth-sub">Rejoignez votre agence sur MissionFlow.</p>

    <?php if(isset($error)): ?>
      <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">

      <div class="fields-row">
        <div class="field">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" placeholder="Rakoto" required>
        </div>
        <div class="field">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" placeholder="Soa" required>
        </div>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="votre@email.com" required>
      </div>

      <div class="field">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••" required>
      </div>

      <button class="btn-submit" name="inscrire">S'inscrire</button>

    </form>

    <p class="auth-switch">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </p>

  </div>

</body>
</html>