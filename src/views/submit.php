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
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT UserID FROM user_data WHERE username = :username");
        $stmt->execute(['username' => $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Username exists, retrieve the existing UserID
            $UserId = $user['UserID'];
            $welcome_message = "Welcome back, " . $name . "!";
        } else {
            // Username does not exist, insert a new user
            $stmt = $conn->prepare("INSERT INTO user_data (username) VALUES (:username)");
            $stmt->execute(['username' => $name]);
            $UserId = $conn->lastInsertId(); // Get the ID of the newly inserted user
            $welcome_message = "Welcome to the site, " . $name . "!";
        }
    
    // Store the welcome message in the session
    $_SESSION['user_logged_in'] = true; 
    $_SESSION['welcome_message'] = $welcome_message;
    $_SESSION['UserId'] = $UserId;
    
    // Redirect back to index.php
    header("Location: index.php");
    exit(); // Ensure no further code is executed

}   catch (PDOException $e) {
         // Handle database errors
        die("Database error: " . $e->getMessage());
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