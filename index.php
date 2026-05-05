<?php $app = "MissionFlow"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $app ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <header class="header">
    <div class="logo"><?= $app ?></div>
    <nav class="nav">
      <a href="auth/login.php">Connexion</a>
      <a href="#apropos">À propos</a>
      <a href="#contact">Nous contacter</a>
    </nav>
  </header>

  <!-- CONTENU PRINCIPAL -->
  <main class="main">

    <!-- HERO -->
    <section class="hero">
      <span class="badge">Agence événementielle</span>
      <h1>Gérez vos missions.<br>Coordonnez votre équipe.</h1>
      <p>Une plateforme simple et moderne pour organiser les missions et les membres de votre agence événementielle.</p>
      <a href="auth/register.php" class="btn">Créer un compte</a>
    </section>

    <!-- À PROPOS -->
    <section id="apropos" class="section">
      <h2>À propos</h2>
      <p><?= $app ?> est conçu pour simplifier la gestion des missions et des membres dans un environnement sécurisé et collaboratif.</p>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="section">
      <h2>Nous contacter</h2>
      <p>Email : contact@missionflow.mg</p>
      <p>Téléphone : +261 34 XXX XXX</p>
    </section>

  </main>

</body>
</html>