<?php
require_once '../views/db.php';
// echo "Hello world!!!"; 
// echo "<br>";

// Function to render tasks table
function renderTasksTable($rows, $headers, $actionButtons = []) {
  $html = '<table border="1" style="width:100%; border-collapse: collapse;">';
  
  // Add table headers
  $html .= '<thead><tr>';
  foreach ($headers as $header) {
      $html .= '<th>' . htmlspecialchars($header) . '</th>';
  }
  if (!empty($actionButtons)) {
    $html .= '<th>Actions</th>'; // Add the 'Actions' header
  }
  $html .= '</tr></thead>';

  // Add table body with rows
  $html .= '<tbody>';
  foreach ($rows as $row) {
      $html .= '<tr>';

  // render cells in the table body $headers
  foreach ($headers as $header) {
      $key = match (strtolower($header)) { // header to match column data
          'category' => 'Category',
          'house' => 'House',
          'task type' => 'TaskType',
          'description' => 'Description',
          'status' => 'Status',
          default => null,
      };

      $value = $key && isset($row[$key]) ? $row[$key] : 'N/A';

// handling for status column
      if ($key === 'Status') {
        if (isset($row['UserStatus'])) {
          $value = $row['UserStatus']; // takes status from user status on tasks
        } elseif (empty($value)) {
          $value = 'Not Completed'; // fallback for empty status
        }
      }
      $html .= '<td>' . htmlspecialchars($value) . '</td>';
    }
      // if ($key === 'status' && empty ($row['status'])) {
      //     $value = 'Not Completed';
      //   }
      //       $html .= '<td>' . htmlspecialchars($value) . '</td>';
      // }
      

   // Add action buttons to Action column
   if (!empty($actionButtons)) {
          $html .= '<td style="text-align: center;">';
                foreach ($actionButtons as $button) {
                // Check if the condition to display this button is met
                if (!isset($button['condition']) || $button['condition']($row)) {
                    $html .= 
                      '<form method="POST" action="index.php" style="display:inline-block; margin-right: 5px;">
                        <input type="hidden" name="taskId" value="' . htmlspecialchars($row['TaskID']) . '">
                        <button type="submit" name="' . htmlspecialchars($button['name']) . '">' . htmlspecialchars($button['label']) . '</button>
                      </form>';
                }
            }
            $html .= '</td>';
          }
  
          $html .= '</tr>';
      }
      $html .= '</tbody></table>';
  
      return $html;
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
    $stmt = $conn->prepare("SELECT t.* 
            FROM Tasks t
            WHERE t.TaskID NOT IN (
                SELECT ut.TaskID
                FROM user_tasks ut
                WHERE ut.UserID = :UserID
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
     // Filter out tasks that are already completed in user_tasks
     $stmt = $conn->prepare
          ("SELECT t.*, ut.Status AS UserStatus
          FROM Tasks t
          JOIN user_tasks ut ON t.TaskID = ut.TaskID
          WHERE ut.UserID = :userId AND ut.Status !='completed'"
          );
     $stmt->execute(['userId' => $UserID]);
     $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //  print_r($rows); //debug

     if (empty($rows)) {
        return "<p>No tasks available to edit.</p>";
     }

    // Define table headers
    $headers = ['Category', 'House', 'Task Type', 'Description', 'Status'];

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
        // ("UPDATE Tasks t
        // INNER JOIN user_tasks ut ON t.TaskID = ut.TaskID
        // SET t.Status = 'completed'
        // WHERE ut.TaskID = :taskId AND ut.UserID = :userId");
        ("UPDATE user_tasks
        SET STATUS = 'completed'
        WHERE TaskID = :taskId AND UserID = :userId"
        );
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
        ("SELECT t.*, ut.Status as UserStatus
        FROM Tasks t
        JOIN user_tasks ut ON t.TaskID = ut.TaskID
        WHERE ut.UserID = :userId AND ut.Status = 'Completed'");
    // -- AND Status = :completed
    $stmt->execute(['userId' => $UserID]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
      return "<p>No completed tasks available.</p>";
  }

   // Define table headers
   $headers = ['Category', 'House', 'Task Type', 'Description', 'Status'];

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
    VALUE (:category, :house, :taskType, :description, :daily, :christmas, 'Not Completed')"
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