<?php
require_once '../views/db.php';
// echo "Hello world!!!"; 
// echo "<br>";

// Start of VIEW DATABASE function
// CRUD READ - View - this function views full database
function displayTasks($conn) {

// Check if a column is selected for sorting
$sortColumn = isset($_GET['column']) ? $_GET['column'] : 'Daily'; // Default sorting column
$stmt = $conn->query("SELECT * FROM Tasks ORDER BY $sortColumn"); // Sort by selected column
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = '<h2>All Database Tasks</h2><br>
            <table>
              <thead>
                <tr>
                  <th>Category</th>
                  <th>House</th>
                  <th>Task Type</th>
                  <th>Description</th>
                  <th>Daily</th>
                  <th>Christmas</th>
                </tr>
              </thead>
 
 <tb>'; // Start a table

// Loop to display all the tasks in the database
foreach ($rows as $row) {
  $output .= '<tr>
                <td>' . htmlspecialchars($row['Category']) . '</td>
                <td>' . htmlspecialchars($row['House']) . '</td>
                <td>' . htmlspecialchars($row['TaskType']) . '</td>
                <td>' . htmlspecialchars($row['Description']) . '</td>
                <td>' . htmlspecialchars($row['Daily']) . '</td>
                <td>' . htmlspecialchars($row['Christmas']) . '</td>
                </tr>';
                // Example of displaying task name
  }

  $output .= '</tbody><table>'; 
  return $output; // Return the output string

} 
// End of VIEW DATABASE function

// Start of VIEW XMAS Themed tasks function
function displayXmas($conn) {

$sortColumn = isset($_GET['column']) ? htmlspecialchars($_GET['column']) : 'Christmas'; // Default sorting column
$stmt = $conn->query("SELECT * FROM Tasks WHERE Christmas = 1") ; // Assuming 'Christmas' is a boolean field
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

 // Output variable with a header
 $output = '<h2>Christmas Themed Tasks</h2><br>
            <table>
              <thead>
                <tr>
                  <th>Category</th>
                  <th>House</th>
                  <th>Task Type</th>
                  <th>Description</th>
                </tr>
              </thead>
 
 <tb>'; // Start a table

// Loop to display all the tasks in the database in table format
foreach ($rows as $row) {
  $output .= '<tr>
                  <td>' . htmlspecialchars($row['Category']) . '</td>
                  <td>' . htmlspecialchars($row['House']) . '</td> 
                  <td>' . htmlspecialchars($row['TaskType']) . '</td> 
                  <td>' . htmlspecialchars($row['Description']) . '</td>  
               </tr>';   
  }

$output .= '</tbody><table>'; 
return $output; // Return the output string

}
// End of VIEW XMAS function

// view function to add tasks to UserId

function displayTasksToAdd($conn) {

  $sortColumn = isset($_GET['column']) ? htmlspecialchars($_GET['column']) : 'UserID'; // Default sorting column
  $stmt = $conn->query("SELECT * FROM Tasks WHERE UserID = 0") ; // 
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

 // Output variable with a header
 $output = '<h2>Tasks To Add</h2><br>
            <table>
              <thead>
                <tr>
                  <th>Category</th>
                  <th>House</th>
                  <th>Task Type</th>
                  <th>Description</th>
                </tr>
              </thead>
 
 <tb>'; // Start a table

// Loop to display all the tasks in the database in table format
foreach ($rows as $row) {
  $output .= '<tr>
                  <td>' . htmlspecialchars($row['Category']) . '</td>
                  <td>' . htmlspecialchars($row['House']) . '</td> 
                  <td>' . htmlspecialchars($row['TaskType']) . '</td> 
                  <td>' . htmlspecialchars($row['Description']) . '</td>  
                  <td>
                    <form method="POST" action="">
                        <input type="hidden" name="taskId" value="' . htmlspecialchars($row['TaskID']) . '">
                        <button type="submit" name="assignTask">Add Task</button>
                    </form>
                  </td>
               </tr>';   
  }

$output .= '</tbody><table>'; 
return $output; // Return the output string

}

// function to add tasks to a userid
function assignTaskToUser($conn, $taskId, $userId) {
  try {
      $stmt = $conn->prepare("UPDATE Tasks SET UserID = :userId WHERE TaskID = :taskId");
      $stmt->execute(['userId' => $userId, 'taskId' => $taskId]);
      return "Task successfully assigned to your account!";
  } catch (PDOException $e) {
      return "Error assigning task: " . $e->getMessage();
  }
}


// View and amend saved tasks to user
function displayEditTasks($conn, $UserID) {  
  try {
     // Prepare the query to fetch tasks assigned to the specific user
     $stmt = $conn->prepare("SELECT * FROM Tasks WHERE UserID = :userId");
     $stmt->execute(['userId' => $UserID]);
     $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

 // Output variable with a header
 $output = '<h2>Mark as completed</h2><br>
            <table>
              <thead>
                <tr>
                  <th>Category</th>
                  <th>House</th>
                  <th>Task Type</th>
                  <th>Description</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>'; // Start a table

// Loop to display all the tasks in the database in table format
foreach ($rows as $row) {
  $output .= '<tr>
                  <td>' . htmlspecialchars($row['Category']) . '</td>
                  <td>' . htmlspecialchars($row['House']) . '</td> 
                  <td>' . htmlspecialchars($row['TaskType']) . '</td> 
                  <td>' . htmlspecialchars($row['Description']) . '</td>  
                  <td>
                    <form method="POST" action="">
                        <input type="hidden" name="taskId" value="' . htmlspecialchars($row['TaskID']) . '">
                        <button type="submit" name="completeTask">Mark task as complete</button>
                    </form>
                  </td>
               </tr>';   
  }

  $output .= '</tbody><table>'; 
  return $output; // Return the output string
  } catch (PDOException $e) {
  return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
  }
}

function markTaskAsComplete($conn, $taskId, $userId) {
  try {
      // Update the task's status to "completed"
      $stmt = $conn->prepare("UPDATE Tasks SET Completed = 1 WHERE TaskID = :taskId AND UserID = :userId");
      $stmt->execute(['taskId' => $taskId, 'userId' => $userId]);

      return "Task marked as completed successfully!";
  } catch (PDOException $e) {
      return "Error marking task as completed: " . $e->getMessage();
  }
}


?>