<?php

// PDO-anslutning
$servername = "mariadb";
$username = "root";
$password = "mariadb";
$dbname = "harry_potter_todo_app";
// PDO-anslutning
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful to Harry Potter To Do database !";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
