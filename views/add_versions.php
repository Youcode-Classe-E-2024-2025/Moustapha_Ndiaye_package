<?php
require_once __DIR__ . '/../config/db_connect.php';

// Récupérer l'ID du package
$package_id = $_GET['id'] ?? 0;
if ($package_id == 0) {
    die("Aucun package spécifié.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $version = $_POST['version'] ?? '';
    $release_date = $_POST['release_date'] ?? '';

    // Validation des champs
    $erreurs = [];
    if (empty($version))
        $erreurs[] = "La version est obligatoire";
    if (empty($release_date))
        $erreurs[] = "La date de publication est obligatoire";

    if (empty($erreurs)) {
        // Ajouter la version à la base de données
        $connexion = connectDatabase();
        try {
            // Ajouter la version
            $requete_version = $connexion->prepare("
                INSERT INTO Package_Versions (package_id, version, release_date) 
                VALUES (?, ?, ?)
            ");
            $requete_version->execute([$package_id, $version, $release_date]);

            $message_succes = "Version ajoutée avec succès !";
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de l'ajout de la version : " . $e->getMessage();
        }
    }
}

// Récupérer les informations du package pour affichage
$connexion = connectDatabase();
$requete_package = $connexion->prepare("SELECT * FROM Packages WHERE id = ?");
$requete_package->execute([$package_id]);
$package = $requete_package->fetch();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter une Version - <?php echo htmlspecialchars($package['name']); ?></title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="views/all_packages.php" class="btn">Packages</a>
            <a href="">Login</a>

        </nav>
        <h1>Ajouter une Version au Package : <?php echo htmlspecialchars($package['name']); ?></h1>
    </header>

    <main>
        <?php if (!empty($message_succes)): ?>
            <div class="message-succes">
                <?php echo htmlspecialchars($message_succes); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="message-erreur">
                <?php foreach ($erreurs as $erreur): ?>
                    <p><?php echo htmlspecialchars($erreur); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <fieldset>
                <legend>Informations de la Version</legend>

                <div class="form-groupe">
                    <label for="version">Numéro de Version *</label>
                    <input type="text" id="version" name="version" required placeholder="Ex: 1.0.0">
                </div>

                <div class="form-groupe">
                    <label for="release_date">Date de Publication *</label>
                    <input type="date" id="release_date" name="release_date" required>
                </div>
            </fieldset>

            <button type="submit" class="bouton-principal">Ajouter la Version</button>
        </form>

        <a href="all_packages.php?id=<?php echo $package_id; ?>" class="bouton-secondaire">Retour aux Détails du
            Package</a>
    </main>
</body>

</html>