<?php
$directoryPath = 'C:\xampp\htdocs\insert';

// Appel de la fonction pour obtenir les tokens de l'URL
$url_tokens = url_tokenizer();

// Créer le motif de recherche, en ajoutant le préfixe attendu au début du nom du fichier
$pattern = $directoryPath . "\\" . $url_tokens. '*.csv';
echo $pattern;
echo $url_tokens;

// Utiliser glob pour obtenir tous les fichiers CSV qui correspondent au motif
$csvFiles = glob($pattern);

// Vérifier si des fichiers correspondants ont été trouvés
if (!empty($csvFiles)) {
    // Prendre le premier fichier du tableau
    $csvFilePath = $csvFiles[0];
    // Extraire juste le nom du fichier à partir du chemin complet
    $fileName = basename($csvFilePath);
    // Extraire les parties à partir du nom du fichier
    $parts = explode('_', $fileName);

    // Assignation correcte des parties extraites
    $part1 = isset($parts[0]) ? $parts[0] : '';
    $part2 = isset($parts[1]) ? $parts[1] : '';
    $part3 = isset($parts[2]) ? explode('.', $parts[2])[0] : ''; // '2024-03-05', en retirant l'extension .csv
} else {
    echo "Aucun fichier CSV correspondant trouvé.";
    return; // Arrêter l'exécution si aucun fichier n'est trouvé
}

$sqliteDbPath = 'C:\xampp\htdocs\insert\donnees.db';

$url_tokens = url_tokenizer();

// Afficher les tokens de l'URL
echo '<pre>';
print_r($url_tokens);
echo '</pre>';

try {
    // Création d'une nouvelle connexion SQLite avec l'objet PDO
    $pdo = new PDO("sqlite:$sqliteDbPath");
    // Configuration des attributs PDO pour lancer des exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS prix (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prix DECIMAL,
        date_prix DATE,
        day_min INT,
        airbnb_id INT,
        date_releve DATE
    )";
    $pdo->exec($sql);

    // Ouverture du fichier CSV en lecture
    if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
        // Lire l'en-tête du fichier CSV et l'ignorer si nécessaire
        fgetcsv($handle, 1000, ",");

        // Préparation de la requête SQL pour insérer les données
        $stmt = $pdo->prepare("INSERT INTO prix (prix, date_prix, day_min, airbnb_id, date_releve) VALUES (?, ?, ?, ?, ?)");

        // Lire chaque ligne du fichier CSV
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Exécution de la requête SQL avec les données du fichier CSV et les parties du nom de fichier
            $stmt->execute([$data[0], $data[1], $data[2], $part1, $part3]);
        
        }
        // Fermeture du fichier CSV
        fclose($handle);
    }
} catch (PDOException $e) {
    // Affichage de l'erreur en cas d'exception
    echo "Erreur : " . $e->getMessage();
}

function url_tokenizer() {
    // Récupérer l'URL actuelle
    $current_url = $_SERVER['REQUEST_URI']; // Utilisez 'REQUEST_URI' pour obtenir l'URI de la requête

    // Supprimer les éventuels paramètres GET de l'URL
    $current_url = strtok($current_url, '?');

    // Découper l'URL en tokens en utilisant le délimiteur "/"
    $tokens = explode('/', $current_url);

    // Supprimer le premier élément du tableau, car il sera vide en raison du slash initial dans l'URL
    array_shift($tokens);

    return $tokens[2];
}


?>
