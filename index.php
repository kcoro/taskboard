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
            if (!empty($title)) {
                $task = new Task($title);
                $taskBoard->addTask($task);
                $_SESSION['tasks'] = $taskBoard->getTasks();
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
        include 'partials/board.php';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Board</title>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .task-card {
            transition: all 0.2s ease-in-out;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Task Board</h1>
        
        <!-- Add Task Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form hx-post="index.php" hx-target="#task-board" hx-swap="outerHTML" class="flex gap-4">
                <input type="hidden" name="action" value="create">
                <input 
                    type="text" 
                    name="title" 
                    placeholder="Enter task title..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    Add Task
                </button>
            </form>
        </div>

        <!-- Task Board -->
        <div id="task-board">
            <?php include 'partials/board.php'; ?>
        </div>
    </div>
</body>
</html>