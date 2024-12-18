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
    if (strlen($input) > 100) {
        return "Invalid format - Name is too long";
    }

    return "Welcome to the site, " . $input;
}

// if (!session_start()) {
//     die("Session failed to start");
// }

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name']);
    $welcome_message = validate_name($name);
    
    // Store the welcome message in the session
    $_SESSION['welcome_message'] = $welcome_message;
    $_SESSION['user_logged_in'] = true; // Set the user as logged in

    // Redirect back to index.php
    header("Location: index.php");
    exit(); // Ensure no further code is executed
}

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     if (isset($_POST['name'])) {
//         echo "Received Name: " . $_POST['name'];
//     } else {
//         echo "Name input is missing.";
//     }
//     exit;
// }

?>