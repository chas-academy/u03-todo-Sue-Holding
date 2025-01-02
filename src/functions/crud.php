<?php
require_once '../views/db.php';
// echo "Hello world!!!"; 
// echo "<br>";

// Function to render tasks table
function renderTasksTable($rows, $headers, $actionButtons = []) {
  $output = '<table>';
  
  // Add table headers
  $output .= '<thead><tr>';
  foreach ($headers as $header) {
      $output .= '<th>' . htmlspecialchars($header) . '</th>';
  }
  if (!empty($actionButtons)) {
    $output .= '<th>Actions</th>'; // Add a header for the action column if there are action buttons
  }
  $output .= '</tr></thead>';
  $output .= '<tbody>';

  // Add table body with rows
  foreach ($rows as $row) {
      $output .= '<tr>';
      foreach ($row as $key => $value) {
        if ($key != 'TaskID' && $key != 'Daily' && $key != 'Christmas' && $key != 'created_at') {  // Prevent TaskID from being displayed directly
            $value = $value ?? '';  // Use empty string if value is null
            $output .= '<td>' . htmlspecialchars($value) . '</td>';
          }
      }

   // Add action buttons
   if (!empty($actionButtons)) {
          $output .= '<td>';
                foreach ($actionButtons as $button) {
                // Check if the condition to display this button is met
                if (!isset($button['condition']) || $button['condition']($row)) {
                    $output .= 
                      '<form method="POST" action="index.php" style="display:inline-block;">
                        <input type="hidden" name="taskId" value="' . htmlspecialchars($row['TaskID']) . '">
                        <button type="submit" name="' . htmlspecialchars($button['name']) . '">' . htmlspecialchars($button['label']) . '</button>
                      </form>';
                }
            }
            $output .= '</td>';
          }
  
          $output .= '</tr>';
      }
      $output .= '</tbody></table>';
  
      return $output;
  }

// view function to add tasks to UserId
function displayTasksToAdd($conn, $UserID) {
  try {
    // Get the logged-in user's ID
    // $UserID = $_SESSION['UserID'];
    // Retrieve tasks where UserID is 0 (unassigned tasks)
    // $stmt = $conn->prepare("UPDATE user_tasks SET UserID = :userId WHERE TaskID = :taskId");
    // $stmt->execute(['userId' => $UserID, 'taskId' => $TaskID]);

    // Retrieve tasks not assigned to the current user
    // $stmt = $conn->prepare("SELECT * FROM Tasks WHERE UserID != UserID");
    // $stmt->execute();
    $stmt = $conn->prepare("SELECT t.* FROM Tasks t
            WHERE t.TaskID NOT IN (
              SELECT TaskID 
              FROM user_tasks 
              WHERE UserID = :UserID
            )
        ");

    $stmt->execute(['UserID' => $UserID]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Define table headers
    $headers = ['Category', 'House', 'Task Type', 'Description', 'Status'];

    // Define action buttons
    $actionButtons = [
      [
          'label' => 'Assign Task',
          'name' => 'assignTask',
          'condition' => function ($row) {

            // return $row['Status'] === 'Not Completed';
              return true;
              // $row['UserID'] == 0; // Show only if unassigned
          },
      ],
  ];

    // Use the renderTasksTable function to generate the table
    return renderTasksTable($rows, $headers, $actionButtons);
    } catch (PDOException $e) {
    return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
    }
  }
  

// function to add tasks to a userid
function assignTaskToUser($conn, $TaskID, $UserID) {
  try {
    $stmt = $conn->prepare
        ("INSERT INTO user_tasks (UserID, TaskID) 
        SELECT :userId, :taskId
        WHERE NOT EXISTS (
        SELECT 1 FROM user_tasks 
        WHERE UserID = :userId AND TaskID = :taskId
        )
    ");
    $stmt->bindParam(':userId', $UserID, PDO::PARAM_INT);
    $stmt->bindParam(':taskId', $TaskID, PDO::PARAM_INT);
    $stmt->execute();

    echo "Task successfully assigned to your account!";
    } catch (PDOException $e) {
    echo "Error assigning task: " . $e->getMessage();
  } 
}


// Fetch tasks assigned to the user that are not completed
// Dobby's today list
function displayEditTasks($conn, $UserID) {  
  try {
     // Prepare the query to fetch tasks assigned to the specific user
     // Filter out tasks that are already completed
     $stmt = $conn->prepare
          ("SELECT t.* 
          FROM Tasks t
          JOIN user_tasks ut ON t.TaskID = ut .TaskID
          WHERE ut.UserID = :userId AND t.Status !='completed'"
          );
     $stmt->execute(['userId' => $UserID]);
     $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

     if (empty($rows)) {
        return "<p>No tasks available to edit.</p>";
     }

    // Define table headers
    $headers = ['Category', 'House', 'Task Type', 'Description', 'Action'];

    // Define action buttons
    $actionButtons = [
        [
          'label' => 'Mark as Completed',
          'name' => 'completeTask',
          'condition' => function ($row) {
              return true;
              },
        ],
        [
          'label' => 'Delete Task',
          'name' => 'deleteTask',
          'condition' => function ($row) {
              return true; // Always show
              },
        ],
    ];

    // Use the renderTasksTable function to generate the table
    return renderTasksTable($rows, $headers, $actionButtons);
    } catch (PDOException $e) {
    return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
    }
  }

function markTaskAsComplete($conn, $TaskID, $UserID) {
  try {
      // Update the task's status to "completed"
      $stmt = $conn->prepare
        ("UPDATE Tasks t
        INNER JOIN user_tasks ut ON t.TaskID = ut.TaskID
        SET t.Status = 'completed'
        WHERE ut.TaskID = :taskId AND ut.UserID = :userId");
      $stmt->execute(['taskId' => $TaskID, 'userId' => $UserID]);

      return "Task marked as completed successfully!";
  } catch (PDOException $e) {
      return "Error marking task as completed: " . $e->getMessage();
  }
}

// function to view all completed tasks assigned to a user id
function displayCompletedTasks($conn, $UserID) {
  try {
    // Prepare the query to fetch tasks assigned to the specific user
    $stmt = $conn->prepare
        ("SELECT * FROM Tasks t
        INNER JOIN user_tasks ut ON t.TaskID = ut.TaskID
        WHERE UserID = :userId AND Status = 'completed'");
    // -- AND Status = :completed
    $stmt->execute(['userId' => $UserID]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Define table headers
   $headers = ['Category', 'House', 'Task Type', 'Description', 'Action'];

   // Use the renderTasksTable function to generate the table
   return renderTasksTable($rows, $headers);
   } catch (PDOException $e) {
   return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
   }
 }


 function deleteTask($conn, $TaskID, $UserID) {
  try {
      // Delete the task assigned to the user
      $stmt = $conn->prepare
        ("DELETE FROM user_tasks
        WHERE TaskID = :taskId AND UserID = :userId"
        );
      $stmt->execute(['taskId' => $TaskID, 'userId' => $UserID]);

      return "Task deleted successfully!";
  } catch (PDOException $e) {
      return "Error deleting task: " . $e->getMessage();
  }
}

// create own tasks function unique to UserID
function createTask($conn, $UserID, $Category, $House, $TaskType, $Description, $Daily, $Christmas) {
try {
  //insert new task into Tasks table
  $stmt = $conn->prepare
    ("INSERT INTO Tasks (Category, House, TaskType, Description, Daily, Christmas, Status)
    VALUE (:category, :house, :taskType, :description, 'no', 'no', 'pending')"
    );
  $stmt->execute([
    'category' => $Category,
    'house' => $House,
    'taskType' => $TaskType,
    'description' => $Description,
    'daily' => $Daily,
    'christmas' => $Christmas,
  ]);

  // get the new TaskID
  $stmt = $conn->prepare(
    "INSERT INTO user_tasks (UserID, TaskID)
    VALUES (:userId, :taskId)"
  );
  $stmt->execute([
    'userId' => $UserID,
    'taskId' => $TaskID,
  ]);

  return "Task created and assigned to the user successfully!";
} catch (PDOException $e) {
    return "Error creating task: " . $e->getMessage();
}
}



 // Start of VIEW XMAS Themed tasks function
function displayXmas($conn) {
  try {
  $sortColumn = isset($_GET['column']) ? htmlspecialchars($_GET['column']) : 'Christmas'; // Default sorting column
  $stmt = $conn->query("SELECT * FROM Tasks WHERE Christmas = 1") ; // Assuming 'Christmas' is a boolean field
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
    // Define table headers
    $headers = ['Category', 'House', 'Task Type', 'Description', 'Action'];
  
    // Use the renderTasksTable function to generate the table
    return renderTasksTable($rows, $headers);
    } catch (PDOException $e) {
    return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
    }
  }
  // End of VIEW XMAS function

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
?>