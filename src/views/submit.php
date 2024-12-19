<?php
session_start(); // Start the session to manage session variables
require 'db.php';

function sanitize_input($input) {
    $sanitized_input = htmlspecialchars($input);
    $sanitized_input = trim($sanitized_input);
    $sanitized_input = stripslashes($sanitized_input);
    return $sanitized_input;
}

function validate_name($input) {
    if (strlen($input) > 50) {
        return "Invalid format - Name is too long";
    }
    return "Welcome to the site, " . $input;
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name']);

    if (empty($name)) {
        die("Error: Username cannot be empty");
    }

    $welcome_message = validate_name($name);

    try {
        // Insert the user into the database
        $stmt = $conn->prepare("INSERT INTO user_data (username) VALUES (:username)");
        $stmt->execute(['username' => $name]);

        // Fetch the userId of the newly inserted user
        $UserId = $conn->lastInsertId();
    
    // Store the welcome message in the session
    $_SESSION['user_logged_in'] = true; 
    $_SESSION['welcome_message'] = $welcome_message;
    $_SESSION['UserId'] = $UserId;

    // Redirect back to index.php
    header("Location: index.php");
    exit(); // Ensure no further code is executed
}   catch (PDOException $e) {
    // Handle duplicate username or database errors
    if ($e->getCode() == 23000) {
        die("Error: Username already exists. Please choose a different name.");
    } else {
        die("Database error: " . $e->getMessage());
    }
}
}

 // to add user to database   
// Create a new data list in the database
// $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
// $stmt = $pdo->prepare("INSERT INTO user_data (username) VALUES (:username)");
// $stmt->execute(['username' => $name]);




// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     if (isset($_POST['name'])) {
//         echo "Received Name: " . $_POST['name'];
//     } else {
//         echo "Name input is missing.";
//     }
//     exit;
// }

?>