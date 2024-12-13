<?php
require_once 'functions/packages.php';

$packages = [];
$recherche = '';

if (isset($_GET['recherche'])) {
    $recherche = trim($_GET['recherche']);
    $packages = rechercherPackages($recherche);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire de Packages JavaScript</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="views/all_packages.php" class="btn">Packages</a>
            <a href="">Login</a>
        </nav>
        <h1>Gestionnaire de Packages JavaScript</h1>
        <form method="GET" action="">
            <input type="text" name="recherche" placeholder="Rechercher un package" 
                   value="<?php echo htmlspecialchars($recherche); ?>">
            <button type="submit">Rechercher</button>
        </form>
    </header>

    <main>
        <?php if (!empty($packages)): ?>
            <section class="package-list">
                <h2>Résultats de recherche</h2>
                <?php foreach ($packages as $package): ?>
                    <article class="package-item">
                        <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                        <p><?php echo htmlspecialchars($package['description']); ?></p>
                        <p>Auteur : <?php echo htmlspecialchars($package['author_name'] ?? 'Non spécifié'); ?></p>
                        <a href="views/details_package.php?id=<?php echo $package['id']; ?>">
                            Voir les détails
                        </a>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php elseif (!empty($recherche)): ?>
            <p>Aucun package trouvé pour "<?php echo htmlspecialchars($recherche); ?>"</p>
        <?php else: ?>
            
        <?php endif; ?>

        <a href="views/add_package.php" class="btn">Ajouter un Package</a>
    </main>

    <script src="public/js/script.js"></script>
</body>
</html>