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
<img class="bg" src="../media/Arrival_at_Hogwarts.jpg" alt="arrival at hogwarts" width="auto">

<header class="header">
<h1>Welcome to the Harry Potter Themed To Do List Web application!!</h1>
<h2>What do you want to do today?</h2>

<?php
if (isset($_SESSION['welcome_message'])) {
    echo "<h2>" . $_SESSION['welcome_message'] . "</h2>";
    // unset($_SESSION['welcome_message']); // Clear the message after displaying
}
?>

</header>

<!-- log in function -->
<nav class="nav-bar">
<?php if (!isset($_SESSION['user_logged_in'])): ?>
        <form method="POST" action="submit.php">
            <!-- <label for="name">Username:</label> -->
            <input type="text" id="name" name="name" placeholder="Enter your username" required>
            <button type="submit">Log In</button>
        </form>
    <?php else: ?>
        <form method="POST" action="logout.php">
            <button type="submit">Log Out</button>
        </form>
    <?php endif; ?>
            
</nav>


<aside class="menu-list">
    <h2><a href="#">View database</a></h2> <br>
    <h2><a href="#">Dobby's Today List</a></h2> <br>
    <h2><a href="#">View Completed</a></h2> <br>
    <h2><a href="#">Sorting Hat</a></h2> <br>
    <h2><a href="#">Christmas Themed</a></h2> <br>
    <h2><a href="#">Create Own Tasks</a></h2> <br>
    
</aside>

<main class="main-display">
    <div>
        <p>here will show the crud funtion based from the menu selection</p>
    </div>
</main>

<div class="today">
    <h2>Dobby today list</h2>       
    <?php 
    $tasks = getTasks($conn);

foreach ($tasks as $task); ?>

<section class="today-list">
    <!-- <h2 class="task-title">Dobby the Elf's Chores ?></h2> -->
    <!-- <h2 class="task-title">
        <?php echo htmlspecialchars($task['title']); ?></h2> 


    <div class="task-info">
        <p><?php echo htmlspecialchars($TaskType['id']); ?></p>
        <input type="checkout"> <?php if ($task['is_completed']) echo 'checked'; ?>
        <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
    </div>
    <div class="edit-containter">
        <button>edit</button>
        <button>delete</button>
        <a href="./views/edit.php">edit</a> -->




        <!-- not sure about this code -->
    <!-- // Define allowed columns for sorting
    // $allowedColumns = ['Category', 'Daily', 'House', 'Christmas', 'Own'];
    // $sortColumn = isset($_GET['column']) && in_array($_GET['column'], $allowedColumns) ? $_GET['column'] : 'Daily'; // Default sorting column
    
    // $stmt = $conn->prepare("SELECT * FROM Tasks ORDER BY $sortColumn"); // Sort by selected column
    // $stmt->execute();
    // $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> -->


    <form method="GET" action="submit.php">
        <label for="column">Sort by:</label>
        <select id="Column" name="column">
            <option value="Category">Category</option>
            <option value="Daily">Daily</option>
            <option value="House">House</option>
            <option value="Christmas">Christmas</option>
            <option value="Own">Own</option>
        </select>
        <input type="submit">
        </form>


<?php
$stmt;
$conn;
$rows;

// Check if a column is selected for sorting
$sortColumn = isset($_GET['column']) ? $_GET['column'] : 'Daily'; // Default sorting column
$stmt = $conn->query("SELECT * FROM Tasks ORDER BY $sortColumn"); // Sort by selected column
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


// CRUD READ - View
// call function to view all tasks
displayTasks($rows);

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

<!-- <section class="done-list">
    <h2>View Completed</h2>
    <ul>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    </section> -->

<aside class="sorting-hat">
    <h2>Sorting Hat</h2>
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
        </form>
</aside>

<section class="create-your-own">
    <h2>Create your own daily Dobby chores</h2>
    
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
    </form>

</section>

<section class="christmas">
    <h2>Search and add our Christmas themed tasks to your <br>personalised to do list!!</h2>
</section>
<footer>
<audio class="song"
    controls
    width="200"
    height="100"
    autoplay
    loop
    preload="auto">
  <source src="../media/Harry_Potter_Themesong.mp3" type="audio/mp3" />
</footer>

</div> 
<!-- end of grid container -->

</body>
</html>