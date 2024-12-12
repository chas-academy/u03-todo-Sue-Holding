<?php
require_once '../views/db.php';
echo "Hello world!!!"; 
echo "<br>";

// function getTasks($conn) {
//     $stmt = $conn->query('SELECT id, title, description, is_completed FROM tasks');
//     return $stmt->fetchAll();
// }

// CRUD READ - View - this function views full database
function displayTasks($conn) {

// Check if a column is selected for sorting
$sortColumn = isset($_GET['column']) ? $_GET['column'] : 'Daily'; // Default sorting column
$stmt = $conn->query("SELECT * FROM Tasks ORDER BY $sortColumn"); // Sort by selected column
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = ''; // Initialize output variable

// Display the tasks
foreach ($rows as $row) {
  $output .= "Task: " . htmlspecialchars($row['Description']) . "<br>"; // Example of displaying task name
  }
  return $output; // Return the output string

} 
// Call function to view all tasks when 'view database' is pressed
// if (isset($_GET['view_database'])) {
//   displayTasks($conn);
// }




function getTasks($conn) {
  $allowedColumns = ['Category', 'Daily', 'House', 'Christmas', 'Own'];
    $sortColumn = isset($_GET['column']) && in_array($_GET['column'], $allowedColumns) ? $_GET['column'] : 'Daily'; // Default sorting column
    
    $stmt = $conn->prepare("SELECT * FROM Tasks ORDER BY $sortColumn"); // Sort by selected column
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getLists($conn) {
    $stmt = $conn->query('SELECT id, title FROM lists');
    return $stmt->fetchAll();
}





//  CREATE
// to add a new customers
// $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name) VALUES (:first_name, :last_name)");
// $stmt->bindParam(':first_name', $firstName);
// $stmt->bindParam(':last_name', $lastName);

// $firstName = "Anna";
// $lastName = "Svensson";
// $stmt->execute();

// echo "Ny kund har lagts till!";


// READ
// to view all in the list
// $sql = "SELECT * FROM customers";
// $stmt = $conn->query("SELECT * FROM customers");
// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// foreach ($rows as $row) {
//     echo $row['first_name'] . " " . $row['last_name'] . "<br>";
// }


// UPDATE
// // to update the list
// $stmt = $conn->prepare("UPDATE customers SET first_name = :first_name WHERE id = :id");
// $stmt->bindParam(':first_name', $firstName);
// $stmt->bindParam(':id', $id);

// $firstName = "Susanna";
// $id = 1;  // updates no 1 in the list
// $stmt->execute();

// echo "Kundens namn har uppdaterats!";

// DELETE
// to delete an entry in the list
// $stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
// $stmt->bindParam(':id', $id);

// $id = 1;
// $stmt->execute();

// echo "Kunden har tagits bort!";


?>