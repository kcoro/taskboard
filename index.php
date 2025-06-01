<?php
require_once 'classes/TaskStatus.php';
require_once 'classes/Task.php';
require_once 'classes/TaskBoard.php';

session_start();

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

$taskBoard = new TaskBoard($_SESSION['tasks']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $title = trim($_POST['title'] ?? '');
            error_log("Create request received - Title: '$title', Empty check: " . (empty($title) ? 'true' : 'false'));
            if (!empty($title)) {
                $task = new Task($title);
                $taskBoard->addTask($task);
                $_SESSION['tasks'] = $taskBoard->getTasks();
                error_log("Task created: $title, Total tasks: " . count($_SESSION['tasks']));
            } else {
                error_log("Task creation failed - empty title");
            }
            break;
            
        case 'move':
            $taskId = $_POST['task_id'] ?? '';
            $newStatus = TaskStatus::tryFrom($_POST['new_status'] ?? '');
            if ($taskId && $newStatus) {
                $taskBoard->moveTask($taskId, $newStatus);
                $_SESSION['tasks'] = $taskBoard->getTasks();
            }
            break;
            
        case 'delete':
            $taskId = $_POST['task_id'] ?? '';
            if ($taskId) {
                $taskBoard->deleteTask($taskId);
                $_SESSION['tasks'] = $taskBoard->getTasks();
            }
            break;
    }
    
    if (isset($_SERVER['HTTP_HX_REQUEST'])) {
        error_log("HTMX request detected, including board with " . count($_SESSION['tasks']) . " tasks");
        $taskBoard = new TaskBoard($_SESSION['tasks']); // Recreate taskBoard for the partial
        include 'views/partials/board.php';
        exit;
    }
}
?>

<?php include 'views/index.view.php'; ?>