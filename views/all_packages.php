<?php
require_once __DIR__ . '/../config/db_connect.php';

// Connexion à la base de données
$connexion = connectDatabase();

// Récupérer tous les packages
$requete_packages = $connexion->query("SELECT * FROM Packages");
$packages = $requete_packages->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Packages</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body>
    <header>
    <nav>
            <a href="../index.php">Home</a>
            <a href="all_packages.php" class="btn">Packages</a>
            <a href="">Login</a>

        </nav>
        <h1>Liste des Packages</h1>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Nom du Package</th>
                    <th>Description</th>
                    <th>Auteur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($package['name']); ?></td>
                        <td><?php echo htmlspecialchars($package['description']); ?></td>
                        <td>
                            <?php
                            // Récupérer l'auteur du package
                            $requete_auteur = $connexion->prepare("SELECT name FROM Authors WHERE id = ?");
                            $requete_auteur->execute([$package['author_id']]);
                            $auteur = $requete_auteur->fetch();
                            echo htmlspecialchars($auteur['name']);
                            ?>
                        </td>
                        <td>
                            <!-- Lien pour voir les détails du package -->
                            <a href="../views/details_package.php?id=<?php echo $package['id']; ?>">
                                Voir les détails
                            </a> |
                            <!-- Lien pour ajouter une version -->
                            <a href="../views/add_versions.php?id=<?php echo $package['id']; ?>">Ajouter une Version</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        
    </main>
</body>

</html>