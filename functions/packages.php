<?php
require_once __DIR__ . '/../config/db_connect.php';

function ajouterPackage($nom, $description, $auteur_id, $tags = []) {
    $connexion = connectDatabase();

    try {
        // Début de la transaction
        $connexion->beginTransaction();

        // Ajout du package
        $requete = $connexion->prepare("INSERT INTO Packages (name, description, author_id) VALUES (?, ?, ?)");
        $requete->execute([$nom, $description, $auteur_id]);
        $package_id = $connexion->lastInsertId();

        // Gestion des tags
        if (!empty($tags)) {
            ajouterTagsAuPackage($package_id, $tags, $connexion);
        }

        // Validation de la transaction
        $connexion->commit();

        return $package_id;
    } catch(PDOException $e) {
        // Annulation en cas d'erreur
        $connexion->rollBack();
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}

function ajouterTagsAuPackage($package_id, $tags, $connexion) {
    foreach ($tags as $tag) {
        // Ajouter le tag s'il n'existe pas
        $requete = $connexion->prepare("INSERT IGNORE INTO Tags (name) VALUES (?)");
        $requete->execute([$tag]);

        // Récupérer l'ID du tag
        $requete = $connexion->prepare("SELECT id FROM Tags WHERE name = ?");
        $requete->execute([$tag]);
        $tag_id = $requete->fetchColumn();

        // Lier le tag au package
        $requete = $connexion->prepare("INSERT IGNORE INTO Packages_Tags (package_id, tag_id) VALUES (?, ?)");
        $requete->execute([$package_id, $tag_id]);
    }
}

function rechercherPackages($recherche) {
    $connexion = connectDatabase();

    $requete = $connexion->prepare("
        SELECT p.*, a.name as author_name 
        FROM Packages p
        LEFT JOIN Authors a ON p.author_id = a.id
        WHERE p.name LIKE :recherche 
        OR p.description LIKE :recherche 
        OR a.name LIKE :recherche
    ");
    $requete->execute(['recherche' => "%$recherche%"]);
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function obtenirDetailsPackage($package_id) {
    $connexion = connectDatabase();

    $requete = $connexion->prepare("
        SELECT p.*, a.name as author_name, a.email as author_email,
               GROUP_CONCAT(DISTINCT t.name) as tags,
               (SELECT COUNT(*) FROM Versions v WHERE v.package_id = p.id) as version_count
        FROM Packages p
        LEFT JOIN Authors a ON p.author_id = a.id
        LEFT JOIN Packages_Tags pt ON p.id = pt.package_id
        LEFT JOIN Tags t ON pt.tag_id = t.id
        WHERE p.id = ?
        GROUP BY p.id
    ");
    $requete->execute([$package_id]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function listerTousLesPackages() {
    $connexion = connectDatabase();

    $requete = $connexion->query("
        SELECT p.*, a.name as author_name 
        FROM Packages p
        LEFT JOIN Authors a ON p.author_id = a.id
    ");
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}
?>