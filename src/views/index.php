<?php
session_start(); // Start the session to store the welcome message
include 'db.php'; // Include the database connection file
require '../functions/crud.php'; // to include sql stmt

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Harry Potter Themed To Do List Web application!!</title>
    <link rel="stylesheet" href="../styles/index.css">

</head>

<body class="bg">
<!-- start of my grid container -->
<div class="grid-container">   
<img class="bg" src="../media/HP_banner.webp" alt="hogwarts" width="auto">

<header class="header">
</header>

<!-- log in function -->
<nav class="login">
<?php if (!isset($_SESSION['user_logged_in'])) : ?>
        <form method="POST" action="submit.php">
            <input type="text" id="name" name="name" placeholder="Enter your username" required>
            <button type="submit">Log In</button>
        </form>
<?php else : ?>
        <form method="POST" action="logout.php">
            <button type="submit">Log Out</button>
        </form>
<?php endif; ?>

    <?php
    if (isset($_SESSION['welcome_message'])) {
        echo "<h3>" . $_SESSION['welcome_message'] . "</h3>";
    }
    if (!isset($_SESSION['UserId'])) {
        echo "<p>Error: You must be logged in to view this content.</p>";
        exit;
    }

// Handle POST requests for forms
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST request for assigning tasks to user
        if (isset($_POST['assignTask']) && isset($_POST['taskId']) && isset($_SESSION['UserId'])) {
            $TaskID = htmlspecialchars($_POST['taskId']);
            $UserID = $_SESSION['UserId']; // Get the logged-in user's ID from the session
            $message = assignTaskToUser($conn, $TaskID, $UserID); // Call the function to assign the task
            echo "<p>$message</p>"; // Display success or error message
        } elseif (isset($_POST['completeTask']) && isset($_POST['taskId']) && isset($_SESSION['UserId'])) {
            $TaskID = htmlspecialchars($_POST['taskId']);
            $UserID = $_SESSION['UserId'];
            $message = markTaskAsComplete($conn, $TaskID, $UserID);
            echo "<p>$message</p>";  // POST request for marking tasks as completed
        } elseif (isset($_POST['deleteTask']) && isset($_POST['taskId']) && isset($_SESSION['UserId'])) {
            $TaskID = htmlspecialchars($_POST['taskId']);
            $UserID = $_SESSION['UserId'];
            $message = deleteTask($conn, $TaskID, $UserID); // Call the function to delete the task
            echo "<p>$message</p>"; // POST request for deleting a task
        } elseif (isset($_POST['reassignTask']) && isset($_POST['taskId']) && isset($_SESSION['UserId'])) {
            $TaskID = htmlspecialchars($_POST['taskId']);
            $UserID = $_SESSION['UserId']; // post request for reassigning tasks

            try {
                // Update the Status in the user_tasks table to reset it
                $stmt = $conn->prepare(
                    "UPDATE user_tasks 
                SET Status = 'not completed' 
                WHERE TaskID = :taskId AND UserID = :userId"
                );
                $stmt->execute([
                    'taskId' => $TaskID,
                    'userId' => $UserID
                ]);
                echo "<p>Task reassigned successfully!</p>";
            } catch (PDOException $e) {
                echo "<p>Error reassigning task: " . $e->getMessage() . "</p>";
            }
        } elseif (isset($_POST['saveTask'])) { // post request to save amended tasks
            // Capture the task details from the form
            $taskId = htmlspecialchars($_POST['taskId']);
            $category = htmlspecialchars($_POST['category']);
            $house = htmlspecialchars($_POST['house']);
            $taskType = htmlspecialchars($_POST['taskType']);
            $description = htmlspecialchars($_POST['description']);
            $daily = isset($_POST['daily']) ? intval($_POST['daily']) : 0;
            $christmas = isset($_POST['christmas']) ? intval($_POST['christmas']) : 0;
            $userId = $_SESSION['UserId'];
            ;

            try {
                // prepare UPDATE query
                $stmt = $conn->prepare(
                    "UPDATE user_tasks ut
                JOIN Tasks t ON ut.TaskID = t.TaskID
                SET 
                    t.Category = :category,
                    t.House = :house,
                    t.TaskType = :taskType,
                    t.Description = :description,
                    t.Daily = :daily,
                    t.Christmas = :christmas
                WHERE ut.TaskID = :taskId AND ut.UserID = :userId"
                );

                $stmt->execute([
                    'taskId' => $taskId,
                    'category' => $category,
                    'house' => $house,
                    'taskType' => $taskType,
                    'description' => $description,
                    'daily' => $daily,
                    'christmas' => $christmas,
                    'userId' => $userId
                ]);
                echo "<p>Task amended successfully!</p>";
            } catch (PDOException $e) {
                echo "<p>Error amending task: " . $e->getMessage() . "</p>";
            }
        }
    }
    ?>         
</nav>

<aside class="menu-list">
    <h2>What do you want<br> to do today?</h2><br>
    <!-- <h2><a href="?view_database=true">View database</a></h2> <br> -->
    <h2><a href="?view_tasksToAdd">View tasks to add</a></h2> <br>
    <h2><a href="?view_editTasks">Dobby's Today List</a></h2> <br>
    <h2><a href="?view_completedTasks">View Completed</a></h2> <br>
    <h2><a href="?view_createTask">Create Own Tasks</a></h2> <br> 
    <!-- <h2><a href="?view_xmas=true">Christmas Themed</a></h2> <br> -->
    <!-- <h2><a href="#">Sorting Hat</a></h2> <br> -->
</aside>

<main class="main-display">
    <h1>Instructions</h1>
    <h3>Under <b>View tasks to add</b> you can view and select the premade tasks from the database 
        and assign them to yourself.<br>
        You can mark them as complete and also delete them from your private to do list.  <br>
        Don't worry, they will reappear in the <b>View tasks to add</b> list so you can also find them again. <br>
        If you want to amend a specific task, add it first and then under your 
        <b>Dobby's Today list</b> you can edit it.<br>
        Once you've completed a task it will automatically return to view list for use again later !
    </h3>

    <!-- the crud funtions based on menu selection -->
<?php
    // Ensure the user is logged in before showing tasks
if (isset($_SESSION['UserId'])) {
        $UserID = $_SESSION['UserId'];

    if (isset($_GET['view_database'])) {
        echo displayTasks($conn);   // Call function to view all tasks
    } elseif (isset($_GET['view_tasksToAdd'])) {
        echo displayTasksToAdd($conn, $UserID); // call function to display tasks to add
    } elseif (isset($_GET['view_editTasks'])) {
        echo displayEditTasks($conn, $UserID); // call function to display tasks to add
    } elseif (isset($_GET['view_completedTasks'])) {
        echo displayCompletedTasks($conn, $UserID); // call function to display completed tasks
    } elseif (isset($_GET['view_xmas'])) {
        echo displayXmas($conn); // call function to display xmas task
    }
    ?> 
        
    <?php
} if (isset($_GET['view_createTask'])) {
    // show form to create new tasks
    ?>
        <form method="POST" action="index.php?view_createTask">
            <fieldset>
                <legend>Create New Task</legend>

                <label for="category">Select Category:</label>
                <select id="category" name="category">
                    <option value="wand practice">Wand Practice</option>
                    <option value="wizard dual">Wizard Dual</option>
                    <option value="owl post">Owl Post</option>
                    <option value="enchanted books">Enchanted Books</option>
                    <option value="potions master">Potions Master</option>
                    <option value="magical creatures">Magical Creatures</option>
                    <option value="marauder's map">Marauder's Map</option>
                    <option value="quidditch training">Quidditch Training</option>
                    <option value="own">Own</option>
                </select>
                <br><br>

                <label for="house">Select House:</label>      
                <select id="house" name="house">
                    <option value="griffindor">Griffindor</option>
                    <option value="ravenclaw">Ravenclaw</option>
                    <option value="huffelpuff">Huffelpuff</option>
                    <option value="slytherin">Slytherin</option>
                    <option value="own">Own</option>
                </select> 
                <br><br> 

                <label for="taskType">Task Type:</label>
                <select id="taskType" name="taskType">
                    <option value="learn a new skill">Learn a new skill</option>
                    <option value="take care of plants and pets">Take care of plants and pets</option>
                    <option value="challenge yourself">Challenge Yourself</option>
                    <option value="keep in touch">Keep in touch</option>
                    <option value="read a book or blog">Read a book or blog</option>
                    <option value="cooking and baking">Cooking and baking</option>
                    <option value="planning trips and outings">Planning trips and outings</option>
                    <option value="stay active">Stay Active</option>
                    <option value="help someone">Help Someone</option>
                    <option value="own">Own</option>
                </select> 
                <br><br>
                
                <label for="description">Description:</label>
                <textarea name="description" required></textarea><br><br>

                <label for="daily">Daily Task:</label>
                <input type="radio" name="daily" value="1" required checked> Yes
                <input type="radio" name="daily" value="0" required> No<br><br>

                <label for="christmas">Christmas Task:</label>
                <input type="radio" name="christmas" value="1" required checked> Yes
                <input type="radio" name="christmas" value="0" required> No<br><br>

                <input type="submit" name="createTask" value="Create Task">
            </fieldset>
        </form>
    <?php

 // Handle form submission for creating task
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createTask'])) {
       // Gather form data
        $category = htmlspecialchars($_POST['category']);
        $house = htmlspecialchars($_POST['house']);
        $taskType = htmlspecialchars($_POST['taskType']);
        $description = htmlspecialchars($_POST['description']);
        $daily = intval($_POST['daily']);
        $christmas = intval($_POST['christmas']);

       // Insert into database - this creates into tasks and user_tasks now
       // displayCreateTask();
        try {
            $stmt = $conn->prepare(
                "INSERT INTO Tasks (Category, House, TaskType, Description, Daily, Christmas, CreatedBy) 
        VALUES (:category, :house, :taskType, :description, :daily, :christmas, :createdBy)"
            );
            $stmt->execute([
             'category' => $category,
             'house' => $house,
             'taskType' => $taskType,
             'description' => $description,
             'daily' => $daily,
             'christmas' => $christmas,
             'createdBy' => $UserID
            ]);

 // get the new TaskID
            $TaskID = $conn->lastInsertId();

 // insert new taskId into user_tasks table
            $stmt = $conn->prepare(
                "INSERT INTO user_tasks (UserID, TaskID, Status)
        VALUES (:userId, :taskId, 'Not Completed')
        "
            );
            $stmt->execute([
            'userId' => $UserID,
            'taskId' => $TaskID,
            ]);

            echo "<p>Task created successfully!</p>";
        } catch (PDOException $e) {
            echo "<p>Error creating task: " . $e->getMessage() . "</p>";
        }
    }
}

// Handle form submission for amending task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amendTask'])) {
    // Debug: Dump POST data
    var_dump($_POST);

    $taskId = $_POST['taskId'] ?? null;
    echo "Task ID: $taskId, User ID: $UserID";

    if ($taskId) {
        $stmt = $conn->prepare(
            "SELECT * 
             FROM Tasks t 
             JOIN user_tasks ut ON t.TaskID = ut.TaskID
             WHERE ut.TaskID = :taskId AND ut.UserID = :userId"
        );
        $stmt->execute(['taskId' => $taskId, 'userId' => $UserID]);
        $taskDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($taskDetails) {
            // Debug: Check task details
            // var_dump($taskDetails);

            // Check if displayAmendForm is available
            if (function_exists('displayAmendForm')) {
                // echo "<p>displayAmendForm function available.</p>";
                // displayAmendForm($taskDetails);
                // Ensure the form HTML is being echoed
                $formHtml = displayAmendForm($taskDetails);
                echo $formHtml;
            } else {
                echo "<p>displayAmendForm function not found.</p>";
            }
        } else {
            echo "<p>Task not found or you don't have permission to amend it.</p>";
        }
    } else {
        echo "<p>No task ID specified.</p>";
    }
}
?>
   
</main>

<footer>
<!-- <audio class="song"
    controls
    width="200"
    height="100"
    autoplay
    loop
    preload="off">
  <source src="../media/Harry_Potter_Themesong.mp3" type="audio/mp3" /> -->
</footer>

</div> 
<!-- end of grid container -->

</body>
</html>
