<?php

// Extraction des tokens de l'URL actuelle via la fonction personnalisée
$url_tokens = url_tokenizer();
// echo $url_tokens;  // Affichage des tokens pour le débogage

$directoryPath = 'C:\xampp\htdocs\insert\localfile22';

// Construction de l'URL cible pour télécharger le fichier CSV, basée sur les tokens et un modèle prédéfini
$url = $directoryPath . "\\" . $url_tokens. '*.csv';
echo $url;  // Affichage de l'URL pour le débogage

// Définition du chemin local où le fichier CSV sera sauvegardé
$localPath = 'C:\xampp\htdocs\insert\localfile';

// Téléchargement du fichier CSV depuis l'URL spécifiée
$fileContent = file_get_contents($url);

if ($fileContent !== false) {
    // Si le téléchargement réussit, sauvegarde du contenu dans un fichier local
    file_put_contents($localPath, $fileContent);
    echo "Le fichier a été téléchargé et sauvegardé en tant que $localPath";
} else {
    // Si le téléchargement échoue, affichage d'un message d'erreur
    echo "Impossible de télécharger le fichier";
}

// Chemin vers le répertoire contenant les fichiers CSV
$directoryPath = 'C:\xampp\htdocs\insert\localfile22';

$localCsvFile = $localPath . '\\' . 'filename'; // Nom de fichier local pour sauvegarder le contenu

// Initialisation de la session cURL
$ch = curl_init();

// Configuration des options de cURL
curl_setopt($ch, CURLOPT_URL, $url); // URL du fichier à télécharger
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Retourner le transfert en tant que chaîne
curl_setopt($ch, CURLOPT_FILE, fopen($localCsvFile, 'w')); // Ouvrir le fichier et écrire la sortie

// Exécution de la session cURL pour le téléchargement
$result = curl_exec($ch);

// Fermeture de la session cURL
curl_close($ch);

// Vérification du succès du téléchargement
if ($result) {
    echo "Le fichier a été téléchargé et sauvegardé en tant que $localCsvFile";
} else {
    // Si le téléchargement échoue, affichage d'un message d'erreur
    echo "Impossible de télécharger le fichier";
}

// Chemin vers la base de données SQLite
$sqliteDbPath = 'C:\xampp\htdocs\insert\donnees.db';

// Récupération des tokens de l'URL une deuxième fois (peut-être redondant)
$url_tokens = url_tokenizer();

// Affichage des tokens pour le débogage
echo '<pre>';
print_r($url_tokens);
echo '</pre>';

try {
    // Connexion à la base de données SQLite avec gestion des erreurs
    $pdo = new PDO("sqlite:$sqliteDbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la table 'prix' si elle n'existe pas déjà
    $sql = "CREATE TABLE IF NOT EXISTS prix (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prix DECIMAL,
        date_prix DATE,
        day_min INT,
        airbnb_id INT,
        date_releve DATE
    )";
    $pdo->exec($sql);

    // Ouverture et traitement du fichier CSV
    if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
        // Ignorer l'en-tête du fichier CSV
        fgetcsv($handle, 1000, ",");

        // Préparation de la requête SQL pour insérer les données du CSV
        $stmt = $pdo->prepare("INSERT INTO prix (prix, date_prix, day_min, airbnb_id, date_releve) VALUES (?, ?, ?, ?, ?)");

        // Lecture de chaque ligne du fichier CSV et insertion dans la base de données
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $stmt->execute([$data[0], $data[1], $data[2], $part1, $part3]);
        }
        // Fermeture du fichier après traitement
        fclose($handle);
    }
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    echo "Erreur : " . $e->getMessage();
}

// // Fonction pour extraire des parties spécifiques de l'URL
function url_tokenizer() {
    $current_url = $_SERVER['REQUEST_URI']; // Récupération de l'URL de la requête
    $current_url = strtok($current_url, '?'); // Suppression des paramètres GET
    $tokens = explode('/', $current_url); // Découpage de l'URL en segments
    array_shift($tokens); // Suppression du segment vide dû au premier '/'
    return $tokens[2]; // Retour du troisième segment de l'URL
}
?>
