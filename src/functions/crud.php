<?php

require_once '../views/db.php';
// echo "Hello world!!!";
// echo "<br>";

// Function to render tasks table
function renderTasksTable($rows, $headers, $actionButtons = [])
{

    $html = '<table border="1" style="width:100%; border-collapse: collapse;">';
// Add table headers
    $html .= '<thead><tr>';
    foreach ($headers as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }
    if (!empty($actionButtons)) {
        $html .= '<th>Actions</th>';
// Add the 'Actions' header
    }
    $html .= '</tr></thead>';
// Add table body with rows
    $html .= '<tbody>';
    foreach ($rows as $row) {
        $html .= '<tr>';
    // render cells in the table body $headers
        foreach ($headers as $header) {
            $key = match (strtolower($header)) {
                // header to match column data
                    'category' => 'Category',
                    'house' => 'House',
                    'task type' => 'TaskType',
                    'description' => 'Description',
                    'status' => 'UserTaskStatus',
                    default => null,
            };
            $value = $key && isset($row[$key]) ? $row[$key] : 'N/A';
    // Ensure 'Status' defaults to 'Not Completed' if empty or missing
            if ($key === 'UserTaskStatus' && (empty($value) || $value === 'N/A')) {
                $value = 'Not Completed';
            }

            $html .= '<td>' . htmlspecialchars($value) . '</td>';
        }

         // Add action buttons to Action column
        if (!empty($actionButtons)) {
            $html .= '<td style="text-align: center;">';
            foreach ($actionButtons as $button) {
            // Check if the condition to display this button is met
                if (!isset($button['condition']) || $button['condition']($row)) {
                    $html .=
                    '<form method="POST" action="index.php" style="display:inline-block; margin-right: 5px;">
                        <input type="hidden" name="taskId" value="' . htmlspecialchars($row['TaskID']) . '">
                        <button type="submit" name="' . htmlspecialchars($button['name']) . '">' .
                        htmlspecialchars($button['label']) . '</button>
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
function displayTasksToAdd($conn, $UserID)
{

    try {
        $stmt = $conn->prepare(
            "SELECT t.*
      FROM Tasks t
      WHERE t.CreatedBy IN (0, :UserID)
      AND t.TaskID NOT IN (
        SELECT ut.TaskID
        FROM user_tasks ut
        WHERE ut.UserID = :UserID) 
      "
        );
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

              return true;
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
function assignTaskToUser($conn, $TaskID, $UserID)
{

    try {
        $stmt = $conn->prepare(
            "INSERT INTO user_tasks (UserID, TaskID) 
        SELECT :userId, :taskId
        WHERE NOT EXISTS (
        SELECT 1 FROM user_tasks 
        WHERE UserID = :userId AND TaskID = :taskId)
    "
        );
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
function displayEditTasks($conn, $UserID)
{
    try {
// Prepare the query to fetch tasks assigned to the specific user
       // Filter out tasks that are already completed in user_tasks
        $stmt = $conn->prepare(
            "SELECT t.*, ut.Status AS UserStatus
          FROM Tasks t
          JOIN user_tasks ut ON t.TaskID = ut.TaskID
          WHERE ut.UserID = :userId AND ut.Status !='completed'
          "
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

              return true;
          },
        ],
        [
          'label' => 'Amend Task',
          'name' => 'amendTask',
          'condition' => function ($row) {

              return true;
          },
        ],
        ];
// Use the renderTasksTable function to generate the table
        return renderTasksTable($rows, $headers, $actionButtons);
    } catch (PDOException $e) {
        return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
    }
}

function markTaskAsComplete($conn, $TaskID, $UserID)
{
    try {
// Update the task's status to "completed"
        $stmt = $conn->prepare(
            "UPDATE user_tasks
        SET STATUS = 'completed'
        WHERE TaskID = :taskId AND UserID = :userId
        "
        );
        $stmt->execute(['taskId' => $TaskID, 'userId' => $UserID]);
        return "Task marked as completed successfully!";
    } catch (PDOException $e) {
        return "Error marking task as completed: " . $e->getMessage();
    }
}

// function to view all completed tasks assigned to a user id
function displayCompletedTasks($conn, $UserID)
{
    try {
// Prepare the query to fetch tasks assigned to the specific user
        $stmt = $conn->prepare(
            "SELECT t.*, ut.Status AS UserTaskStatus
        FROM Tasks t
        INNER JOIN user_tasks ut ON t.TaskID = ut.TaskID
        WHERE ut.UserID = :userId AND ut.Status = 'completed'
        "
        );
        $stmt->execute(['userId' => $UserID]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) {
            return "<p>No completed tasks available.</p>";
        }

     // Define table headers
        $headers = ['Category', 'House', 'Task Type', 'Description', 'Status'];
// Define action buttons
        $actionButtons = [
        [
          'label' => 'Reassign Task',
          'name' => 'reassignTask',
          'condition' => function ($row) {

              return true;
          },
        ],
        ];
// Use the renderTasksTable function to generate the table
        return renderTasksTable($rows, $headers, $actionButtons);
    } catch (PDOException $e) {
        return "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
    }
}

function deleteTask($conn, $TaskID, $UserID)
{
    try {
// Delete the task assigned to the user
        $stmt = $conn->prepare(
            "DELETE FROM user_tasks
      WHERE TaskID = :taskId AND UserID = :userId
      "
        );
        $stmt->execute(['taskId' => $TaskID, 'userId' => $UserID]);
        return "Task deleted successfully!";
    } catch (PDOException $e) {
        return "Error deleting task: " . $e->getMessage();
    }
}

// amend own tasks function unique to UserID
function displayAmendForm($taskDetails)
{
    $Categories = array("wand practice", "wizard dual", "owl post", "enchanted books", "potions master",
        "magical creatures", "marauder's map", "quidditch training", "own");
    $Houses = array("griffindor", "ravenclaw", "huffelpuff", "slytherin", "own");
    $TaskTypes = array("learn a new skill", "take care of plants and pets", "challenge yourself", "keep in touch",
        "read a book or blog", "cooking and baking", "planning trips and outings",
        "stay active", "help someone", "own");
    $categoryOptions = "";
    $houseOptions = "";
    $taskTypeOptions = "";
    foreach ($Categories as $Category) {
        $selected = $taskDetails['Category'] === $Category ? 'selected' : '';
        $categoryOptions .= "<option value='$Category' $selected>$Category</option>";
    }

    foreach ($Houses as $House) {
        $selected = $taskDetails['House'] === $House ? 'selected' : '';
        $houseOptions .= "<option value='$House' $selected>$House</option>";
    }

    foreach ($TaskTypes as $TaskType) {
        $selected = $taskDetails['TaskType'] === $TaskType ? 'selected' : '';
        $taskTypeOptions .= "<option value='$TaskType' $selected>$TaskType</option>";
    }

    $description = htmlspecialchars($taskDetails['Description'] ?? '', ENT_QUOTES);
    $dailyCheckedYes = isset($taskDetails['Daily']) && $taskDetails['Daily'] == 1 ? 'checked' : '';
    $dailyCheckedNo = isset($taskDetails['Daily']) && $taskDetails['Daily'] == 0 ? 'checked' : '';
    $christmasCheckedYes = isset($taskDetails['Christmas']) && $taskDetails['Christmas'] == 1 ? 'checked' : '';
    $christmasCheckedNo = isset($taskDetails['Christmas']) && $taskDetails['Christmas'] == 0 ? 'checked' : '';
// Print the form using echo with PHP variables
    echo '
<form method="POST" action="index.php?view_amendTask">
  <fieldset>
      <legend>Amend Task</legend>

      <!-- Hidden task ID field -->
      <input type="hidden" name="taskId" value="' . htmlspecialchars($taskDetails['TaskID']) . '">

      <label for="category">Select Category:</label>
      <select id="category" name="category">
        ' . $categoryOptions . '
      </select>
      <br><br>

      <label for="house">Select House:</label>      
      <select id="house" name="house">
        ' . $houseOptions . '
      </select> 
      <br><br> 

      <label for="taskType">Task Type:</label>
      <select id="taskType" name="taskType">
        ' . $taskTypeOptions . '  
      </select> 
      <br><br>

      <label for="description">Description:</label>
      <textarea name="description" required>' . htmlspecialchars($description) . '</textarea>
      <br><br>

      <label for="daily">Daily Task:</label>
      <input type="radio" name="daily" value="1" ' . $dailyCheckedYes . '> Yes
      <input type="radio" name="daily" value="0" ' . $dailyCheckedNo . '> No
      <br><br>

      <label for="christmas">Christmas Task:</label>
      <input type="radio" name="christmas" value="1" ' . $christmasCheckedYes . '> Yes
      <input type="radio" name="christmas" value="0" ' . $christmasCheckedNo . '> No
      <br><br>

      <input type="submit" name="saveTask" value="Save Task">
  </fieldset>
</form>
';
}
