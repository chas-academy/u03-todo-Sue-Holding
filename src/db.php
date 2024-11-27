<?php
// PDO-anslutning
$servername = "mariadb";
$username = "root";
$password = "mariadb";
$dbname = "harry_potter_todo_app";

// PDO-anslutning
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, 
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connection succeded to Harry Potter To Do database !";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>