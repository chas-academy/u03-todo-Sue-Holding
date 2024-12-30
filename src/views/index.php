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
<!-- <h1>Welcome to the Harry Potter Themed To Do List Web application!!</h1> -->

</header>

<!-- log in function -->
<nav class="login">
<?php if (!isset($_SESSION['user_logged_in'])): ?>
        <form method="POST" action="submit.php">
            <input type="text" id="name" name="name" placeholder="Enter your username" required>
            <button type="submit">Log In</button>
        </form>
    <?php else: ?>
        <form method="POST" action="logout.php">
            <button type="submit">Log Out</button>
        </form>
    <?php endif; ?>
    <?php
if (isset($_SESSION['welcome_message'])) {
    echo "<h3>" . $_SESSION['welcome_message'] . "</h3>";
    // unset($_SESSION['welcome_message']); // Clear the message after displaying
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
    } 
    // POST request for marking tasks as completed
    elseif (isset($_POST['completeTask']) && isset($_POST['taskId']) && isset($_SESSION['UserId'])) {
        $TaskID = htmlspecialchars($_POST['taskId']);
        $UserID = $_SESSION['UserId'];
        $message = markTaskAsComplete($conn, $TaskID, $UserID);
        echo "<p>$message</p>";
    } 
    // POST request for deleting a task
    elseif (isset($_POST['deleteTask']) && isset($_POST['taskId']) && isset($_SESSION['UserId'])) {
        $TaskID = htmlspecialchars($_POST['taskId']);
        $UserID = $_SESSION['UserId'];
        $message = deleteTask($conn, $TaskID, $UserID); // Call the function to delete the task
        echo "<p>$message</p>";
    } 
    // Handle errors for missing session or invalid actions
    else {
        echo "<p>Error: You must be logged in to perform this action.</p>";
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
    <h2><a href="#">Sorting Hat</a></h2> <br>
    <h2><a href="?view_xmas=true">Christmas Themed</a></h2> <br>
    <h2><a href="#">Create Own Tasks</a></h2> <br> 
</aside>

<main class="main-display">
    <!-- the crud funtions based on menu selection -->
<?php
    // Ensure the user is logged in before showing tasks
    if (isset($_SESSION['UserId'])) {
        $UserID = $_SESSION['UserId'];

    if (isset($_GET['view_database'])) {
        echo displayTasks($conn);   // Call function to view all tasks 
    } elseif (isset($_GET['view_tasksToAdd'])) { 
        echo displayTasksToAdd($conn, $UserID); // call function to dispay tasks to add
    } elseif (isset($_GET['view_editTasks'])) {
        echo displayEditTasks($conn, $UserID); // call function to dispay tasks to add
    } elseif (isset($_GET['view_completedTasks'])) {
        echo displayCompletedTasks($conn, $UserID); // call function to dispay completed tasks
    } elseif (isset($_GET['view_xmas'])) {
        echo displayXmas($conn); // call function to dispay xmas task
    } 
}
  ?>
    
</main>


  
<br>
<div class="extra">

    <div class="task-info">
    
    </div> 
    
    <!-- <div class="edit-containter">
        <button>edit</button>
        <button>delete</button>
        <a href="./views/edit.php">edit</a>  -->
    </section>


    <!-- <form method="GET" action="submit.php">
        <label for="column">Sort by:</label>
        <select id="Column" name="column">
            <option value="Category">Category</option>
            <option value="Daily">Daily</option>
            <option value="House">House</option>
            <option value="Christmas">Christmas</option>
            <option value="Own">Own</option>
        </select>
        <input type="submit">
        </form> -->


        <!-- this function views full database -->
<?php

?>

<!-- <input type="radio" id="task1" name="harry_potter_task" value="task1">
<label for="task1">task 1</label><br>
<input type="radio" id="task2" name="harry_potter_task" value="task2">
<label for="task2">task 2</label><br>
<input type="radio" id="task3" name="harry_potter_task" value="task3">
<label for="task3">task 3</label><br>
<input type="radio" id="task4" name="harry_potter_task" value="task4">
<label for="task4">task 4</label><br>
<input type="radio" id="task5" name="harry_potter_task" value="task5">
<label for="task5">task 5</label><br>
<input type="radio" id="task6" name="harry_potter_task" value="task6">
<label for="task6">task 6</label> -->

    </section>



<aside class="sorting-hat">
    <!-- <h2>Sorting Hat</h2>
    <p>Add new tasks by House</p>
    <form method="GET" action="submit.php">
        <label for="house">Select House:</label>
        <select id="house" name="house">
            <option value="griffindor">Griffindor</option>
            <option value="ravenclaw">Ravenclaw</option>
            <option value="huffelpuff">Huffelpuff</option>
            <option value="slytherin">Slytherin</option>
            <option value="all">All</option>
        </select>
        <input type="submit">
        </form> -->
</aside>

<section class="create-your-own">
    <!-- <h2>Create your own daily Dobby chores</h2>
    
    <form method="GET" action="submit.php">
        <label for="Category">Select Category:</label>
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
    <br>
        <label for="house">Select House:</label>
        <select id="house" name="house">
            <option value="griffindor">Griffindor</option>
            <option value="ravenclaw">Ravenclaw</option>
            <option value="huffelpuff">Huffelpuff</option>
            <option value="slytherin">Slytherin</option>
            <option value="own">Own</option>
        </select>
    <br>
    <form method="GET" action="submit.php">
        <label for="task">Task:</label>
        <input type="text" id="task" name="task">
    <br>
        <input type="submit">
    </form> -->

</section>

<section class="christmas">
    <!-- <h2>Search and add our Christmas themed tasks to your <br>personalised to do list!!</h2> -->
</section>
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
</div>
<!-- end of grid container -->


</body>
</html>