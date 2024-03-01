<?php   
$dbPath = 'C:\xampp\htdocs\php\base.db';

try {
    $conn = new PDO('sqlite:' . $dbPath);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS infos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                mail TEXT,
                link1 TEXT,
                link2 TEXT
            )";
    $conn->exec($sql);

    // Prepare INSERT statement
    $sql = "INSERT INTO infos (mail, link1, link2) VALUES (:mail, :nom, :prenom)";
    $stmt = $conn->prepare($sql);

    // Bind parameters to statement variables
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':nom', $link1);
    $stmt->bindParam(':prenom', $link2);

    // Insert data
    $mail = $mail = $_POST['mail'];
    $nom = $_POST['link1'];
    $name = $_POST['link2'];   

    $stmt->execute();

    echo "New record created successfully";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>