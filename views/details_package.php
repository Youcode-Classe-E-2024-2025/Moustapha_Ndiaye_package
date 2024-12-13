<?php
require_once __DIR__ . '/../functions/packages.php';

// Vérifier si un ID de package est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de package invalide");
}

$package_id = intval($_GET['id']);
$package = obtenirDetailsPackage($package_id);

if (!$package) {
    die("Package non trouvé");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <title>Détails du Package</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body>
    <header>
        <nav>
            <a href="../index.php">Home</a>
            <a href="all_packages.php" class="btn">Packages</a>
            <a href="">Login</a>

        </nav>
        <h1>Détails du Package</h1>
    </header>

    <main>
        <section class="package-details">
            <h2><?php echo htmlspecialchars($package['name']); ?></h2>

            <div class="package-info">
                <p><strong>Description :</strong> <?php echo htmlspecialchars($package['description']); ?></p>

                <p><strong>Auteur :</strong>
                    <?php echo htmlspecialchars($package['author_name'] ?? 'Non spécifié'); ?>
                    (<?php echo htmlspecialchars($package['author_email'] ?? 'Email non disponible'); ?>)
                </p>

                <p><strong>Date de création :</strong>
                    <?php echo date('d/m/Y H:i', strtotime($package['created_at'])); ?>
                </p>

                <p><strong>Dernière mise à jour :</strong>
                    <?php echo date('d/m/Y H:i', strtotime($package['updated_at'])); ?>
                </p>

                <?php if (!empty($package['tags'])): ?>
                    <p><strong>Tags :</strong>
                        <?php
                        $tags = explode(',', $package['tags']);
                        echo implode(', ', array_map('htmlspecialchars', $tags));
                        ?>
                    </p>
                <?php endif; ?>

                <p><strong>Nombre de versions :</strong> <?php echo $package['version_count']; ?></p>
            </div>
        </section>

        <div class="actions">
            <a href="edit_package.php?id=<?php echo $package_id; ?>" class="btn">Modifier</a>
        </div>
    </main>
</body>

</html>