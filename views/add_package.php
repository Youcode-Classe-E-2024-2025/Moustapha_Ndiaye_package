<?php
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions/packages.php';

    // Récupération des données du formulaire
    $nom_package = $_POST['nom_package'] ?? '';
    $description_package = $_POST['description_package'] ?? '';
    $nom_auteur = $_POST['nom_auteur'] ?? '';
    $email_auteur = $_POST['email_auteur'] ?? '';
    $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];

    // Validation de base
    $erreurs = [];
    if (empty($nom_package))
        $erreurs[] = "Le nom du package est obligatoire";
    if (empty($description_package))
        $erreurs[] = "La description du package est obligatoire";
    if (empty($nom_auteur))
        $erreurs[] = "Le nom de l'auteur est obligatoire";
    if (empty($email_auteur))
        $erreurs[] = "L'email de l'auteur est obligatoire";

    if (empty($erreurs)) {
        // Ajouter l'auteur
        $connexion = connectDatabase();

        try {
            // Commencer une transaction
            $connexion->beginTransaction();

            // Ajouter l'auteur
            $requete_auteur = $connexion->prepare("INSERT INTO Authors (name, email) VALUES (?, ?)");
            $requete_auteur->execute([$nom_auteur, $email_auteur]);
            $auteur_id = $connexion->lastInsertId();

            // Ajouter le package
            $requete_package = $connexion->prepare("
                INSERT INTO Packages (name, description, author_id) 
                VALUES (?, ?, ?)
            ");
            $requete_package->execute([$nom_package, $description_package, $auteur_id]);
            $package_id = $connexion->lastInsertId();

            // Ajouter les tags si présents
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        // Ajouter le tag s'il n'existe pas
                        $requete_tag = $connexion->prepare("
                            INSERT IGNORE INTO Tags (name) VALUES (?)
                        ");
                        $requete_tag->execute([$tag]);

                        // Récupérer l'ID du tag
                        $requete_tag_id = $connexion->prepare("SELECT id FROM Tags WHERE name = ?");
                        $requete_tag_id->execute([$tag]);
                        $tag_id = $requete_tag_id->fetchColumn();

                        // Lier le tag au package
                        $requete_lien_tag = $connexion->prepare("
                            INSERT IGNORE INTO Packages_Tags (package_id, tag_id) 
                            VALUES (?, ?)
                        ");
                        $requete_lien_tag->execute([$package_id, $tag_id]);
                    }
                }
            }

            // Valider la transaction
            $connexion->commit();

            $message_succes = "Package et auteur ajoutés avec succès !";
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $connexion->rollBack();
            $erreurs[] = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un Package</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body>
    <header>
        <nav>
            <a href="../index.php">Home</a>
            <a href="all_packages.php" class="btn">Packages</a>
            <a href="">Login</a>

        </nav>
        <h1>Ajouter un nouveau Package</h1>
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
                <legend>Informations du Package</legend>

                <div class="form-groupe">
                    <label for="nom_package">Nom du Package *</label>
                    <input type="text" id="nom_package" name="nom_package" required placeholder="Ex: mon-super-package"
                        value="<?php echo isset($nom_package) ? htmlspecialchars($nom_package) : ''; ?>">
                </div>

                <div class="form-groupe">
                    <label for="description_package">Description du Package *</label>
                    <textarea id="description_package" name="description_package" required
                        placeholder="Décrivez brièvement à quoi sert votre package"><?php echo isset($description_package) ? htmlspecialchars($description_package) : ''; ?></textarea>
                </div>

                <div class="form-groupe">
                    <label for="tags">Tags (séparés par des virgules)</label>
                    <input type="text" id="tags" name="tags" placeholder="Ex: javascript, utilitaire, web"
                        value="<?php echo isset($tags) ? htmlspecialchars(implode(', ', $tags)) : ''; ?>">
                </div>
            </fieldset>

            <fieldset>
                <legend>Informations de l'Auteur</legend>

                <div class="form-groupe">
                    <label for="nom_auteur">Nom de l'Auteur *</label>
                    <input type="text" id="nom_auteur" name="nom_auteur" required placeholder="Votre nom complet"
                        value="<?php echo isset($nom_auteur) ? htmlspecialchars($nom_auteur) : ''; ?>">
                </div>

                <div class="form-groupe">
                    <label for="email_auteur">Email de l'Auteur *</label>
                    <input type="email" id="email_auteur" name="email_auteur" required
                        placeholder="votre.email@exemple.com"
                        value="<?php echo isset($email_auteur) ? htmlspecialchars($email_auteur) : ''; ?>">
                </div>
            </fieldset>

            <button type="submit" class="bouton-principal">Ajouter le Package</button>
        </form>

        
    </main>
</body>

</html>