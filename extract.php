<?php

function url_to_id(string $url): string {
    // Utilisation d'une expression régulière pour extraire l'ID
    if (preg_match('/airbnb.+?\/rooms\/(\d+)/', $url, $matches)) {
        // L'ID est le premier groupe capturé
        return $matches[1]; // Retourne l'ID si trouvé
    } else {
        return ""; // Retourne une chaîne vide si aucun ID n'est trouvé
    }
}

$result = url_to_id();

$url = "http://127.0.0.1:5000/api/" . $result . "/4";

echo $url; // Ajoutez un point-virgule ici

function fetch_url_content($url): string {
    // Initialiser une session cURL
    $ch = curl_init($url);

    // Configurer les options de cURL : retourner la réponse au lieu de l'afficher directement
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Exécuter la session cURL
    $response = curl_exec($ch);

    // Vérifier s'il y a des erreurs
    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch); // Afficher l'erreur
        curl_close($ch); // Fermer la session cURL
        return ''; // Retourner une chaîne vide en cas d'erreur
    }

    // Fermer la session cURL
    curl_close($ch);

    return "true";
}

//decoupe l'URL en petit token
function url_tokenizer($tokens)
{
    // Récupérer l'URL actuelle
    $current_url = $_SERVER['REQUEST_URI'];

    // Supprimer les éventuels paramètres GET de l'URL
    $current_url = strtok($current_url, '?');

    // Découper l'URL en tokens en utilisant le délimiteur "/"
    $tokens = explode('/', $current_url);

    // Supprimer le premier élément du tableau, car il sera vide en raison du slash initial dans l'URL
    array_shift($tokens);
    return $tokens ;
}

// Exemple d'utilisation de la fonction
echo fetch_url_content($url); // Utilisez $url au lieu de $result

?>
