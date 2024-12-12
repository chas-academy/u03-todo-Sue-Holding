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
    echo "Connection successful to Harry Potter To Do database !";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

echo "<br>";
$stmt = $conn->query("SELECT * FROM Tasks");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CRUD READ - View
// Displaying the fetched data

// function displayTasks($rows) {
// foreach ($rows as $row) {
//     echo "Task ID: " . $row['TaskID'] . $row['Category'] . $row['House'] . $row['TaskType'] . $row['Description'] . "<br>";

// }
// }


// Define allowed columns for sorting
$allowedColumns = ['Category', 'House', 'Daily', 'Christmas', 'Own'];
$sortColumn = isset($_GET['column']) && in_array($_GET['column'], $allowedColumns) ? $_GET['column'] : 'Daily'; // Default sorting column

$stmt = $conn->prepare("SELECT * FROM Tasks ORDER BY $sortColumn"); // Sort by selected column
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>