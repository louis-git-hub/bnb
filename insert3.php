<?php

require 'C:\xampp\php\vendor\autoload.php';  
use GuzzleHttp\Client;

function url_tokenizer() {
    $current_url = $_SERVER['REQUEST_URI']; // Récupération de l'URL de la requête
    $current_url = strtok($current_url, '?'); // Suppression des paramètres GET
    $tokens = explode('/', $current_url); // Découpage de l'URL en segments
    array_shift($tokens); // Suppression du segment vide dû au premier '/'
    return $tokens[2]; // Retour du troisième segment de l'URL
}

$url_tokens = url_tokenizer();
echo $url_tokens;

$client = new Client();

// Définissez l'ID que vous souhaitez passer à l'API.
$id = $url_tokens;  // Changez cela pour l'ID réel que vous souhaitez utiliser.

// Construisez l'URL de l'API en incluant l'ID dans le chemin.
$apiUrl = "http://127.0.0.1:8089/api/csv/" . $id;

try {
    // Faites une requête GET à l'URL de l'API.
    $response = $client->request('GET', $apiUrl);
    // Obtenez le corps de la réponse et le contenu
    $body = $response->getBody();
    $content = $body->getContents();

    // Spécifiez le chemin où vous voulez sauvegarder le fichier
    $filePath = 'C:\Dos'; // Modifiez le chemin et le nom du fichier selon vos besoins
    
    // Sauvegardez le contenu dans un fichier
    $result = file_put_contents(C:\Dos\cvcv.csv);
    if ($result === false) {
        echo "Erreur : Impossible de sauvegarder le fichier.";
    } else {
        echo "Le fichier a été téléchargé avec succès.";
    }
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    // Attrapez et affichez toute erreur survenue lors de l'appel de l'API.
    echo "Une erreur est survenue lors de l'appel de l'API : " . $e->getMessage();
}

?>