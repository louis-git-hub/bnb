<?php
$id = "987717838714056473";
$database = $id . '.db';
$db = new SQLite3($database);

// Set up the email alert
$to = 'tom.baudry@gmail.com';
$subject = 'Airbnb Price Alert';
$message = 'The price for the Airbnb listing has changed.';
$headers = 'From: louis.baudry456@gmail.com' . "\r\n" .
    'Reply-To: noreply@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

// Get the date for today
$today = date('d/m/Y');

// Run the SQL query to get the price data for today
$query = $db->query("SELECT * FROM prices WHERE date = '$today'");
$result = $query->fetchArray(SQLITE3_ASSOC);

// Check if there is any data for today
if ($result) {
    // Send an email alert if the price has changed
    if ($result['price'] != "Bloqué") {
        mail($to, $subject, $message, $headers);
    }
} else {
    echo "No data found for today.";
}

// Close the database connection
$db->close();
?>