<?php
require_once '../db.php';
echo "Hello world!!!"; 
echo "<br>";

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