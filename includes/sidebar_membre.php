<?php

  $stmt = $pdo->prepare("SELECT photo FROM doc_membres WHERE id_utilisateur=? AND type_doc='photo_profil'");
  $stmt->execute([$_SESSION['user_id']]);
  $row = $stmt->fetch();
  if($row) 
    $photoProfil = $row['photo'];


?>
 <!-- SIDEBAR -->
 <aside class="sidebar">

<div class="sidebar-logo">MissionFlow</div>

<nav class="sidebar-nav">
  <span class="nav-label">Mon espace</span>
  <a href="dashboard.php"    class="nav-item"><span class="nav-icon">&#9675;</span> Tableau de bord</a>
  <a href="profil.php"       class="nav-item"><span class="nav-icon">&#9675;</span> Mon profil</a>
  <a href="offres.php"       class="nav-item"><span class="nav-icon">&#9675;</span> Offres disponibles</a>
  <a href="missions.php"     class="nav-item"><span class="nav-icon">&#9675;</span> Mes missions</a>

  <span class="nav-label">Suivi</span>
  <a href="candidatures.php" class="nav-item"><span class="nav-icon">&#9675;</span> Mes candidatures</a>
  <a href="paie.php"         class="nav-item"><span class="nav-icon">&#9675;</span> Mes paiements</a>
</nav>

<a href="../auth/logout.php" class="sidebar-logout">Déconnexion</a>

</aside>

<!-- CONTENU PRINCIPAL -->
<div class="dash-main">

<!-- TOPBAR -->
<header class="topbar">
  <div class="topbar-left">
    <h1 class="page-title">Mon espace</h1>
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
        <div class="avatar">
            <?php if(!empty($photoProfil)): ?>
                <img src="../uploads/<?= htmlspecialchars($photoProfil) ?>" alt="Photo profil">
            <?php else: ?>
                <?= strtoupper(substr($prenom,0,1)) ?>
            <?php endif; ?>
        </div>

        <div class="profile-info">
            <span class="profile-name">
                <?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?>
            </span>
            <span class="profile-role">Membre</span>
        </div>
    </div>
  </div>
</header>