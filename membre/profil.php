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

    $nom    = $_SESSION['nom'];
    $prenom = $_SESSION['prenom'];
    
    /*BARRE DE PROGRESSION*/

    $champs_total = 9;
    $champs_remplis = 0;

    $stmt = $pdo->prepare("SELECT * FROM membre WHERE id_utilisateur=?");
    $stmt->execute([$_SESSION['user_id']]);
    $membre = $stmt->fetch();

    if($membre)
    {
        if(!empty($membre['adresse'])) $champs_remplis++;
        if(!empty($membre['taille'])) $champs_remplis++;
    }

    /* téléphone principal */
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM numero_telephone nt
        INNER JOIN membre m ON nt.id_membre = m.id_membre
        WHERE m.id_utilisateur=? AND type_numero='principal'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    if($stmt->fetchColumn() > 0) 
      $champs_remplis++;

    /* photos */
    $types = 
    [
        'photo_profil',
        'cin_recto',
        'cin_verso',
        'photo_buste',
        'photo_pied',
        'cv'
    ];

    foreach($types as $type)
    {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM doc_membres
            WHERE id_utilisateur=? AND type_doc=?
        ");
        $stmt->execute([$_SESSION['user_id'], $type]);

        if($stmt->fetchColumn() > 0)
        {
            $champs_remplis++;
        }
    }

    $progression = round(($champs_remplis / $champs_total) * 100);

  /*  RECUPERATION DE DONNEES */

    $adresse = '';
    $metre = '';
    $cm = '';
    $telephone = '';
    $whatsapp = '';
    $telephone2 = '';

    if($membre)
    {
        $adresse = $membre['adresse'];

        $taille_parts = explode('.', $membre['taille']);
        $metre = $taille_parts[0] ?? '';
        $cm = $taille_parts[1] ?? '';

        $stmt = $pdo->prepare("
            SELECT type_numero, numero
            FROM numero_telephone
            WHERE id_membre=?
        ");
        $stmt->execute([$membre['id_membre']]);

        while($tel = $stmt->fetch())
        {
            if($tel['type_numero'] == 'principal')
                $telephone = $tel['numero'];

            if($tel['type_numero'] == 'whatsapp')
                $whatsapp = $tel['numero'];

            if($tel['type_numero'] == 'secondaire')
                $telephone2 = $tel['numero'];
        }
    }

    $photos = [];
    if($membre) 
    {
        $stmt = $pdo->prepare("SELECT type_doc, photo FROM doc_membres WHERE id_utilisateur=?");
        $stmt->execute([$_SESSION['user_id']]);
        while($row = $stmt->fetch()) 
        {
            $photos[$row['type_doc']] = $row['photo'];
        }
    }

    $photo_profil = null;
    if($membre) 
    {
        $stmt = $pdo->prepare("SELECT photo FROM doc_membres WHERE id_utilisateur=? AND type_doc='photo_profil' ORDER BY date_ajout DESC LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if($row) 
          $photo_profil = $row['photo'];
    }

    if(isset($_POST['sauvegarder']))
    {
        $user = $_SESSION['user_id'];
        /* 
          1. INFOS TEXTE
        */
        $adresse = $_POST['adresse'];
        $metre = $_POST['metre'];
        $cm = $_POST['centimetre'];

        $taille = $metre . "." . $cm;

        /* 
          2. CREER OU UPDATE MEMBRE
        */
        $stmt = $pdo->prepare("SELECT id_membre FROM membre WHERE id_utilisateur = ?");
        $stmt->execute([$user]);
        $id_membre = $stmt->fetchColumn();

        if(!$id_membre)
        {
            $stmt = $pdo->prepare("
                INSERT INTO membre(adresse, taille, id_utilisateur)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$adresse, $taille, $user]);

            $id_membre = $pdo->lastInsertId();
        }
        else
        {
            $stmt = $pdo->prepare("
                UPDATE membre 
                SET adresse = ?, taille = ?
                WHERE id_membre = ?
            ");
            $stmt->execute([$adresse, $taille, $id_membre]);
        }

        /* 
          3. UPLOAD FUNCTION (sécurisé)
        */
        function uploadFile($file)
        {
            if(!empty($file['name']))
            {
                $name = uniqid()."_".$file['name'];
                move_uploaded_file($file['tmp_name'], "../uploads/".$name);
                return $name;
            }
            return null;
        }

        /* 
          4. PHOTO PROFIL
        */

        $photo = uploadFile($_FILES['photo_profil']);

        if($photo)
        {
           $stmt = $pdo->prepare("
              SELECT COUNT(*) FROM doc_membres WHERE id_utilisateur = ? AND type_doc='photo_profil'
          ");
          $stmt->execute([$user]);

          if($stmt->fetchColumn() > 0) 
          {
              $stmt = $pdo->prepare("
                UPDATE doc_membres SET photo = ? WHERE id_utilisateur= ? AND type_doc='photo_profil'
            ");
            $stmt->execute([$photo,$user]);


          }
          else
          {
            $stmt = $pdo->prepare("
              INSERT INTO doc_membres(id_utilisateur, photo, type_doc, date_ajout)
              VALUES (?, ?, 'photo_profil', CURRENT_DATE)
            ");
            $stmt->execute([$user, $photo]);

          }
        }

        /* 
          5. CIN RECTO
        */
        $cin_recto = uploadFile($_FILES['cin_recto']);
        if($cin_recto)
        {
            $stmt = $pdo->prepare("
              SELECT COUNT(*) FROM doc_membres WHERE id_utilisateur = ? AND type_doc='cin_recto'
          ");
          $stmt->execute([$user]);

          if($stmt->fetchColumn() > 0) 
          {
              $stmt = $pdo->prepare("
                UPDATE doc_membres SET photo = ? WHERE id_utilisateur= ? AND type_doc='cin_recto'
            ");
            $stmt->execute([$cin_recto,$user]);
          }
          else
          {
            $stmt = $pdo->prepare("
              INSERT INTO doc_membres(id_utilisateur, photo, type_doc, date_ajout)
              VALUES (?, ?, 'cin_recto', CURRENT_DATE)
            ");
            $stmt->execute([$user, $cin_recto]);

          }
        }

        /* 
          6. CIN VERSO
        */
        $cin_verso = uploadFile($_FILES['cin_verso']);
        if($cin_verso)
        {
            $stmt = $pdo->prepare("
              SELECT COUNT(*) FROM doc_membres WHERE id_utilisateur = ? AND type_doc='cin_verso'
          ");
          $stmt->execute([$user]);

          if($stmt->fetchColumn() > 0) 
          {
              $stmt = $pdo->prepare("
                UPDATE doc_membres SET photo = ? WHERE id_utilisateur= ? AND type_doc='cin_verso'
            ");
            $stmt->execute([$cin_verso,$user]);
          }
          else
          {
            $stmt = $pdo->prepare("
              INSERT INTO doc_membres(id_utilisateur, photo, type_doc, date_ajout)
              VALUES (?, ?, 'cin_verso', CURRENT_DATE)
            ");
            $stmt->execute([$user, $cin_verso]);

          }
        }

        /* 
          7. BUSTE
        */
        $buste = uploadFile($_FILES['photo_buste']);
        if($buste)
        {
            $stmt = $pdo->prepare("
              SELECT COUNT(*) FROM doc_membres WHERE id_utilisateur = ? AND type_doc='photo_buste'
          ");
          $stmt->execute([$user]);

          if($stmt->fetchColumn() > 0) 
          {
              $stmt = $pdo->prepare("
                UPDATE doc_membres SET photo = ? WHERE id_utilisateur= ? AND type_doc='photo_buste'
            ");
            $stmt->execute([$buste,$user]);
          }
          else
          {
            $stmt = $pdo->prepare("
              INSERT INTO doc_membres(id_utilisateur, photo, type_doc, date_ajout)
              VALUES (?, ?, 'photo_buste', CURRENT_DATE)
            ");
            $stmt->execute([$user, $buste]);

          }
        }

        /* 
          8. PIED
        */
        $pied = uploadFile($_FILES['photo_pied']);
        if($pied)
        {
              $stmt = $pdo->prepare("
              SELECT COUNT(*) FROM doc_membres WHERE id_utilisateur = ? AND type_doc='photo_pied'
          ");
          $stmt->execute([$user]);

          if($stmt->fetchColumn() > 0) 
          {
              $stmt = $pdo->prepare("
                UPDATE doc_membres SET photo = ? WHERE id_utilisateur= ? AND type_doc='photo_pied'
            ");
            $stmt->execute([$pied,$user]);
          }
          else
          {
            $stmt = $pdo->prepare("
              INSERT INTO doc_membres(id_utilisateur, photo, type_doc, date_ajout)
              VALUES (?, ?, 'photo_pied', CURRENT_DATE)
            ");
            $stmt->execute([$user, $pied]);

          }
         }

        /* 
          9. CV
        */
        $cv = uploadFile($_FILES['cv']);
        if($cv)
        {
              $stmt = $pdo->prepare("
              SELECT COUNT(*) FROM doc_membres WHERE id_utilisateur = ? AND type_doc='cv'
          ");
          $stmt->execute([$user]);

          if($stmt->fetchColumn() > 0) 
          {
              $stmt = $pdo->prepare("
                UPDATE doc_membres SET photo = ? WHERE id_utilisateur= ? AND type_doc='cv'
            ");
            $stmt->execute([$cv,$user]);
          }
          else
          {
            $stmt = $pdo->prepare("
              INSERT INTO doc_membres(id_utilisateur, photo, type_doc, date_ajout)
              VALUES (?, ?, 'cv', CURRENT_DATE)
            ");
            $stmt->execute([$user, $cv]);

          }
        }

      function savePhone($pdo, $id_membre, $type, $numero)
      {
          $stmt = $pdo->prepare("
              SELECT COUNT(*) 
              FROM numero_telephone 
              WHERE id_membre = ? AND type_numero = ?
          ");
          $stmt->execute([$id_membre, $type]);

          if($stmt->fetchColumn() > 0)
          {
              $stmt = $pdo->prepare("
                  UPDATE numero_telephone 
                  SET numero = ?
                  WHERE id_membre = ? AND type_numero = ?
              ");
              $stmt->execute([$numero, $id_membre, $type]);
          }
          else
          {
              $stmt = $pdo->prepare("
                  INSERT INTO numero_telephone(type_numero, id_membre, numero)
                  VALUES (?, ?, ?)
              ");
              $stmt->execute([$type, $id_membre, $numero]);
          }
      }

        /* 
          10. TELEPHONE PRINCIPAL
        */
        $tel = $_POST['telephone'];

        savePhone($pdo, $id_membre, 'principal', $tel);
        

        /* 
          11. WHATSAPP (optionnel)
        */
        $whatsapp = $_POST['whatsapp'];
        if(!empty($_POST['whatsapp']))
        {
            savePhone($pdo, $id_membre, 'whatsapp', $_POST['whatsapp']);
        }

        /* 
          12. TELEPHONE 2 (optionnel)
        */
        $telephone2 = $_POST['telephone2'];
        if(!empty($_POST['telephone2']))
        {    
          savePhone($pdo, $id_membre, 'telephone2', $_POST['telephone2']);
        }

        
    header("Location: profil.php?success=1");
    exit();

    }

    function docCard($id, $name, $label, $hint, $accept, $photos) 
    {
      $existe = isset($photos[$id]);
      $fichier = $existe ? $photos[$id] : null;
      ?>
      <div class="doc-card">
          <div class="doc-card__label"><?= $label ?></div>
          <div class="doc-card__info">
              <span class="doc-card__name"><?= $name ?></span>
              <span class="doc-card__hint"><?= $hint ?></span>
          </div>

          <label for="<?= $id ?>" class="doc-card__btn" id="btn-<?= $id ?>">
              <?= $existe ? 'Modifier' : 'Ajouter' ?>
          </label>
          <input type="file" id="<?= $id ?>" name="<?= $id ?>" accept="<?= $accept ?>" class="upload-input" >

  
          <div class="doc-actions">
              <?php if($existe): ?>
                  <a href="../uploads/<?= $fichier ?>" target="_blank" class="doc-card__btn">Voir</a>
                  <a href="photo_actions.php?type=<?= $id ?>" class="doc-card__delete">Supprimer</a>
              <?php endif; ?>
          </div>
      </div>
      <?php
  }
 
 
    $telephone_principal = '';
    $telephone2 = '';
    $whatsapp = '';

    if($membre)
    {
        $stmt = $pdo->prepare("
            SELECT type_numero, numero
            FROM numero_telephone
            WHERE id_membre = ?
        ");
        $stmt->execute([$membre['id_membre']]);

        while($row = $stmt->fetch())
        {
            if($row['type_numero'] == 'principal')
            {
                $telephone_principal = $row['numero'];
            }

            if($row['type_numero'] == 'telephone2')
            {
                $telephone2 = $row['numero'];
            }

            if($row['type_numero'] == 'whatsapp')
            {
                $whatsapp = $row['numero'];
            }
        }
    }
    
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon profil · MissionFlow</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/profil.css">

  <script src="../assets/js/profil.js" ></script>
</head>
<body class="dash-body">

  <?php include('../includes/sidebar_membre.php'); ?>

  <div class="">

    <main class="dash-content">

  <!-- BANNIÈRE PROFIL INCOMPLET -->
       
    <?php if($progression < 100 || isset($_GET['modifier'])):?>

          <?php if($progression < 100 ):?>
            <div class="profil-banner">
                <div class="profil-banner__text">
                    <span class="profil-banner__title">Votre dossier est incomplet</span>
                    <span class="profil-banner__sub">
                        Complétez votre profil pour pouvoir postuler aux offres de missions.
                    </span>
                </div>
                <a href="#completer" class="action-btn">Compléter mon profil</a>
            </div>
         <?php endif; ?>

        <!-- PROGRESSION -->
        <div class="profil-progress">
          <div class="progress-header">
            <span class="section-heading">Complétion du dossier</span>
            <span class="progress-pct"><?= $progression ?>%</span>
          </div>
          <div class="progress-bar">
            <div class="progress-bar__fill" style="width: <?= $progression ?>%"></div>        
        </div>
        </div>

          <!-- FORMULAIRE DOSSIER -->
          <form method="post" enctype="multipart/form-data" class="profil-form" id="completer">

            <!-- PHOTO DE PROFIL -->
            <div class="profil-section">
              <h2 class="profil-section__title">Photo de profil</h2>
              <div class="upload-zone--avatar">

                <!-- Avatar : photo existante OU initiale -->
                <?php if($photo_profil): ?>
                  <div class="avatar-preview avatar-preview--img" id="avatarPreview"
                      data-initial="<?= strtoupper(substr($prenom,0,1)) ?>"
                      style="background-image: url('../uploads/<?= htmlspecialchars($photo_profil) ?>')">
                  </div>
                <?php else: ?>
                  <div class="avatar-preview" id="avatarPreview"
                      data-initial="<?= strtoupper(substr($prenom,0,1)) ?>">
                    <?= strtoupper(substr($prenom,0,1)) ?>
                  </div>
                <?php endif; ?>

                <div class="upload-zone__info">

                  <label for="photo_profil" class="upload-label" id="photoLabel"
                        <?= $photo_profil ? 'style="display:none"' : '' ?>>
                    Choisir une photo
                  </label>

                  <input type="file" id="photo_profil" name="photo_profil" accept="image/*" class="upload-input">

                  <!-- Icônes : visibles si photo existe déjà -->
                  <div id="photoActions" style="display: <?= $photo_profil ? 'flex' : 'none' ?>;">
                    <label for="photo_profil" class="icon-btn" title="Modifier">
                      <img src="../assets/icons/edit.png" alt="Modifier" width="18">
                    </label>
                    <button type="button" id="deletePhoto" class="icon-btn" title="Supprimer">
                      <img src="../assets/icons/supprimer.png" alt="Supprimer" width="18">
                    </button>
                  </div>

                  <span class="upload-hint">JPG, PNG — max 2 Mo</span>
                </div>

              </div>
            </div>

            <!-- INFORMATIONS PERSONNELLES -->
            <div class="profil-section">
              <h2 class="profil-section__title">Informations personnelles</h2>
              <div class="form-grid">

                <div class="field">
                  <label for="telephone">Téléphone principal</label>
                  <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    value="<?= htmlspecialchars($telephone) ?>"
                    pattern="^(\+261|0)\s?(32|33|34|38)\s?[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}$"
                    title="Exemple : 0341234567 ou 034 12 345 67"
                    required
                >
                </div>

                <div class="field">
                  <label for="whatsapp">Numéro WhatsApp <span class="optional">optionnel</span></label>
                  <input
                    type="tel"
                    id="whatsapp"
                    name="whatsapp"
                    value="<?= htmlspecialchars($whatsapp) ?>"
                    pattern="^(\+261|0)\s?(32|33|34|38)\s?[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}$"
                    title="Exemple : 0341234567 ou 034 12 345 67"
                    
                  >
                </div>

                <div class="field">
                  <label for="telephone2">Téléphone secondaire <span class="optional">optionnel</span></label>
                  <input
                    type="tel"
                    id="telephone2"
                    name="telephone2"
                    value="<?= htmlspecialchars($telephone2) ?>"
                    pattern="^(\+261|0)\s?(32|33|34|38)\s?[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}$"
                    title="Exemple : 0341234567 ou 034 12 345 67"
                    
                  >
                </div>

                <div class="field field--full">
                  <label for="adresse">Adresse</label>
                  <input type="text" name="adresse" value="<?= htmlspecialchars($adresse) ?>" id="adresse" required>
                </div>

                <div class="field">
                  <label for="centimetre">Taille</label>
                  <div class="taille-input">
                    <select name="metre" id="metre">
                      <option value="1" <?= ($metre=="1") ? "selected" : "" ?>>1 m</option>
                      <option value="2" <?= ($metre=="2") ? "selected" : "" ?>>2 m</option>
                    </select>
                    <input type="number" name="centimetre" id="centimetre" min="0" max="99" value="<?= htmlspecialchars($cm) ?>" required>
                    <span class="taille-unit">cm</span>
                  </div>
                </div>

              </div>
            </div>

            <!-- DOCUMENTS -->
            <div class="profil-section">
              <h2 class="profil-section__title">Documents du dossier</h2>
              <div class="docs-grid">

                <?php docCard('cin_recto',  'CIN Recto','CIN', 'Photo nette obligatoire',  'image/*',$photos) ?>
                <?php docCard('cin_verso',  'CIN Verso',                    'CIN', 'Photo nette obligatoire',  'image/*',$photos) ?>
                <?php docCard('photo_buste',     'Photo buste',                  'IMG', 'Fond clair recommandé',    'image/*',$photos) ?>
                <?php docCard('photo_pied',      'Photo en pied — tenue élégante','IMG','Fond clair recommandé',    'image/*',$photos) ?>
                <?php docCard('cv',         'Curriculum Vitae',             'CV',  'PDF recommandé — max 5 Mo','.pdf,.doc,.docx',$photos) ?>
    

              </div>
            </div>

            <!-- SUBMIT -->
            <div class="profil-submit">
              <button type="submit" name="sauvegarder" class="btn-submit" style="width:auto; padding: 12px 36px;">
                Sauvegarder le dossier
              </button>
            </div>

          </form>

          <?php else: ?>

      <div class="profil-view">

        <!-- HEADER -->
        <div class="profil-view__header">
          <div class="profil-view__avatar"
              style="background-image:url('../uploads/<?= htmlspecialchars($photos['photo_profil']) ?>')">
          </div>
          <div class="profil-view__identity">
            <h2 class="profil-view__name"><?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?></h2>
            <span class="profil-view__badge">Dossier complet</span>
          </div>
        </div>

        <!-- BLOC INFOS -->
        <div class="profil-view__card">

          <div class="profil-view__card-section">
            <span class="card-section-title">Adresse</span>
            <span class="card-section-value"><?= htmlspecialchars($membre['adresse']) ?></span>
          </div>

          <div class="profil-view__divider"></div>

          <div class="profil-view__card-section">
            <span class="card-section-title">Taille</span>
            <span class="card-section-value"><?= htmlspecialchars($membre['taille']) ?> m</span>
          </div>

          <div class="profil-view__divider"></div>

          <div class="profil-view__card-section">
            <span class="card-section-title">Contact</span>
            <span class="card-section-value"><?= htmlspecialchars($telephone_principal) ?></span>
            <?php if(!empty($whatsapp)): ?>
              <span class="card-section-value">WhatsApp : <?= htmlspecialchars($whatsapp) ?></span>
            <?php endif; ?>
            <?php if(!empty($telephone2)): ?>
              <span class="card-section-value"><?= htmlspecialchars($telephone2) ?></span>
            <?php endif; ?>
          </div>

        </div>
 
        <!-- DOCUMENTS -->
        <h3 class="profil-view__subtitle">Documents</h3>

        <div class="docs-preview">

        <div class="doc-preview">
          <span class="doc-preview__label">CIN Recto</span>
          <a href="../uploads/<?= htmlspecialchars($photos['cin_recto']) ?>" target="_blank">
            <img src="../uploads/<?= htmlspecialchars($photos['cin_recto']) ?>" alt="CIN Recto">
          </a>
        </div>

        <div class="doc-preview">
          <span class="doc-preview__label">CIN Verso</span>
          <a href="../uploads/<?= htmlspecialchars($photos['cin_verso']) ?>" target="_blank">
            <img src="../uploads/<?= htmlspecialchars($photos['cin_verso']) ?>" alt="CIN Verso">
          </a>
        </div>

        <div class="doc-preview">
          <span class="doc-preview__label">Photo buste</span>
          <a href="../uploads/<?= htmlspecialchars($photos['photo_buste']) ?>" target="_blank">
            <img src="../uploads/<?= htmlspecialchars($photos['photo_buste']) ?>" alt="Photo buste">
          </a>
        </div>

        <div class="doc-preview">
          <span class="doc-preview__label">Photo pied</span>
          <a href="../uploads/<?= htmlspecialchars($photos['photo_pied']) ?>" target="_blank">
            <img src="../uploads/<?= htmlspecialchars($photos['photo_pied']) ?>" alt="Photo pied">
          </a>
        </div>

      </div>

        <!-- CV -->
        <div class="cv-preview">
          <div class="cv-preview__label">
            <span>Curriculum Vitae</span>
            <small>PDF · Cliquez pour consulter</small>
          </div>
          <a href="../uploads/<?= htmlspecialchars($photos['cv']) ?>" target="_blank">Voir le CV</a>
        </div>

        <!-- ACTION -->
        <div class="profil-view__actions">
          <a href="profil.php?modifier=1" class="btn-submit" style="width:auto; padding:12px 36px;">
            Modifier mon profil
          </a>
        </div>
     </div>


    <?php endif; ?>
  </main>
  </div>

</body>
</html>