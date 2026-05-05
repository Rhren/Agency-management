<?php

$host = 'localhost';        
$db   = 'Agency';      
$user = 'nancia';             
$pass = 'mdp';
$charset = 'utf8mb4';      

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options PDO 
$options = 
[
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // afficher erreurs
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch assoc
    PDO::ATTR_EMULATE_PREPARES   => false,                  // sécurité SQL
];

try 
{
    // Création de l'objet PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) 
{
    // Gestion d'erreur si connexion échoue
    die("Connexion échouée : " . $e->getMessage());
}
?>
