<?php

function url_to_id(): string {
    $url = "https://www.airbnb.fr/rooms/33463196?adults=1&category_tag=Tag%3A7769&children=0&enable_m3_private_room=true&infants=0&pets=0&photo_id=912108901&search_mode=flex_destinations_search&check_in=2024-03-21&check_out=2024-03-26&source_impression_id=p3_1709314663_PmgiNVipMjFVLzrO&previous_page_section_name=1000&federated_search_id=9aa5d38a-9053-47c3-b9eb-07a99885591c";

    // Utilisation d'une expression régulière pour extraire l'ID
    if (preg_match('/airbnb\.fr\/rooms\/(\d+)/', $url, $matches)) {
        // L'ID est le premier groupe capturé
        return $matches[1]; // Retourne l'ID si trouvé
    } else {
        return ''; // Retourne une chaîne vide si aucun ID n'est trouvé
    }
}

$result = url_to_id();

$url = "http://127.0.0.1:5000/api/" . $result . "/7";

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

// Exemple d'utilisation de la fonction
echo fetch_url_content($url); // Utilisez $url au lieu de $result

?>
