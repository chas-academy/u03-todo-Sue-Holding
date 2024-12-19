<?php
// PDO-anslutning
$servername = "mariadb";
$username = "root";
$password = "mariadb";
$dbname = "harry_potter_todo_app";

// PDO-anslutning
try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname", 
        $username, 
        $password, 
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    // echo "Connection successful to Harry Potter To Do database !";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// to add user to database
// $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>