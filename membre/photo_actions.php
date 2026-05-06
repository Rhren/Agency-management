<?php

   session_start();
   require '../includes/db.php';
   
   $user = $_SESSION['user_id'];
   
   /* récupérer le type envoyé */
   $type = $_GET['type'] ?? '';
   
   $stmt = $pdo->prepare("
       SELECT photo FROM doc_membres
       WHERE id_utilisateur = ? AND type_doc = ?
   ");

   $stmt->execute([$user, $type]);
   
   $photo = $stmt->fetchColumn();

    if($photo)
    {
        if(file_exists("../uploads/".$photo))
        {
            unlink("../uploads/".$photo);
        }

        $stmt = $pdo->prepare("
            DELETE FROM doc_membres
            WHERE id_utilisateur = ? AND type_doc = ?
        ");
        $stmt->execute([$user, $type]);

    }
    header("Location: profil.php");
    exit;

?>

