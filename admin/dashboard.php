<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require '../includes/db.php';

    if(!isset($_SESSION['user_id']))
    {
        header("location: ../auth/login.php");
        exit();
    }
    $role=$_SESSION['role'];
    $nom=$_SESSION['nom'];
    $prenom=$_SESSION['prenom'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard · MissionFlow</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
 
</head>
<body class="dash-body">

  <!-- SIDEBAR -->
  <aside class="sidebar">

    <div class="sidebar-logo">MissionFlow</div>

    <nav class="sidebar-nav">
      <span class="nav-label">Gestion</span>
      <a href="membres.php"      class="nav-item"><span class="nav-icon">&#9675;</span> Membres</a>
      <a href="offres.php"       class="nav-item"><span class="nav-icon">&#9675;</span> Nouvelle offre</a>
      <a href="missions.php"     class="nav-item"><span class="nav-icon">&#9675;</span> Suivi des missions</a>
      <a href="candidatures.php" class="nav-item"><span class="nav-icon">&#9675;</span> Candidatures</a>

      <span class="nav-label">Finance</span>
      <a href="paie.php"         class="nav-item"><span class="nav-icon">&#9675;</span> Paie</a>
    </nav>

    <a href="../auth/logout.php" class="sidebar-logout">Déconnexion</a>

  </aside>

  <!-- CONTENU PRINCIPAL -->
  <div class="dash-main">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <h1 class="page-title">Tableau de bord</h1>
      </div>
      <div class="topbar-right">
        <a href="messages.php"     class="topbar-icon" title="Messages">
            <img src="../assets/icons/message.png" alt="Messages" width="20" height="20">
        </a>
        <a href="notifications.php" class="topbar-icon" title="Notifications">
            <img src="../assets/icons/notification.png" alt="Messages" width="20" height="20">
        </a>
        <a href="parametres.php"   class="topbar-icon" title="Paramètres">
          <img src="../assets/icons/parametre.png" alt="Messages" width="20" height="20">
        </a>

        <div class="topbar-profile">
          <div class="avatar"><?= strtoupper(substr($prenom,0,1)) ?></div>
          <div class="profile-info">
            <span class="profile-name"><?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?></span>
            <span class="profile-role">Administrateur</span>
          </div>
        </div>
      </div>
    </header>

    <!-- CORPS -->
    <main class="dash-content">

      <p class="welcome">Bienvenue, <strong><?= htmlspecialchars($prenom) ?></strong> — voici un aperçu de votre agence.</p>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-card__label">Membres actifs</span>
          <span class="stat-card__value">—</span>
        </div>
        <div class="stat-card">
          <span class="stat-card__label">Missions en cours</span>
          <span class="stat-card__value">—</span>
        </div>
        <div class="stat-card">
          <span class="stat-card__label">Candidatures</span>
          <span class="stat-card__value">—</span>
        </div>
        <div class="stat-card">
          <span class="stat-card__label">Offres publiées</span>
          <span class="stat-card__value">—</span>
        </div>
      </div>

      <!-- ACTIONS RAPIDES -->
      <div class="quick-actions">
        <h2 class="section-heading">Actions rapides</h2>
        <div class="actions-row">
          <a href="offres.php"       class="action-btn">+ Nouvelle offre</a>
          <a href="membres.php"      class="action-btn action-btn--ghost">Voir les membres</a>
          <a href="candidatures.php" class="action-btn action-btn--ghost">Candidatures</a>
        </div>
      </div>

    </main>

  </div>

</body>
</html>