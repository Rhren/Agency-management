<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require '../includes/db.php';

    if(!isset($_SESSION['user_id'] ))
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
  <title>Mon espace · MissionFlow</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/profil.css">
</head>
<body class="dash-body">

  <?php include('../includes/sidebar_membre.php'); ?>

    <!-- CORPS -->
    <main class="dash-content">

      <p class="welcome">Bienvenue, <strong><?= htmlspecialchars($prenom) ?></strong> — voici votre espace membre.</p>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-card__label">Missions validées</span>
          <span class="stat-card__value">—</span>
        </div>
        <div class="stat-card">
          <span class="stat-card__label">Candidatures envoyées</span>
          <span class="stat-card__value">—</span>
        </div>
        <div class="stat-card">
          <span class="stat-card__label">Offres disponibles</span>
          <span class="stat-card__value">—</span>
        </div>
        <div class="stat-card">
          <span class="stat-card__label">Statut dossier</span>
          <span class="stat-card__value stat-card__value--badge">Actif</span>
        </div>
      </div>

      <!-- ACTIONS RAPIDES -->
      <div class="quick-actions">
        <h2 class="section-heading">Actions rapides</h2>
        <div class="actions-row">
          <a href="offres.php"       class="action-btn">Voir les offres</a>
          <a href="profil.php"       class="action-btn action-btn--ghost">Mon dossier</a>
          <a href="candidatures.php" class="action-btn action-btn--ghost">Mes candidatures</a>
        </div>
      </div>

      <!-- MISSIONS VALIDÉES -->
      <div class="dash-section">
        <h2 class="section-heading">Missions validées</h2>
        <div class="empty-state">
          <p>Aucune mission validée pour le moment.</p>
          <a href="offres.php" class="action-btn">Parcourir les offres</a>
        </div>
      </div>

    </main>

  </div>

</body>
</html>