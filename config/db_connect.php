<?php
function connectDatabase() {
    try {
        // Assure-toi que les identifiants ici sont corrects
        $dsn = 'mysql:host=localhost;dbname=packageManagement';
        $username = 'root';  // Remplace avec ton utilisateur
        $password = '';  // Remplace avec ton mot de passe
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>
