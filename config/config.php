<?php
function connexionBaseDeDonnees() {
    $host = 'localhost';
    $dbname = 'PackageManagement';
    $utilisateur = 'root';
    $motDePasse = '';

    try {
        $connexion = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8", 
            $utilisateur, 
            $motDePasse
        );
        
        // Configuration pour afficher les erreurs SQL
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $connexion;
    } catch(PDOException $e) {
        // Gestion d'erreur simple
        die("Erreur de connexion : " . $e->getMessage());
    }
}