<?php   
$dbPath = 'C:\xampp\htdocs\My_project\Bootstrapp\azerty.db';

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
    $sql = "INSERT INTO infos (mail, link1, link2) VALUES (:mail, :link1, :link2)";
    $stmt = $conn->prepare($sql);
   // Insert data
   $mail = $_POST['mail'];
   $link1 = $_POST['link1'];
   $link2 = $_POST['link2'];   

    // Bind parameters to statement variables
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':link1', $link1);
    $stmt->bindParam(':link2', $link2);

    $stmt->execute();

} 
    catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;

echo json_encode(["message" => "Le formulaire a été soumis avec succès."]);


?>
