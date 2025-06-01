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
        .task-card.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }
        .drop-zone.drag-over {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: rgb(59, 130, 246);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let draggedElement = null;

            // Use event delegation to avoid re-adding listeners
            document.body.addEventListener('dragstart', function(e) {
                if (e.target.classList.contains('task-card')) {
                    draggedElement = e.target;
                    e.target.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', e.target.outerHTML);
                }
            });

            document.body.addEventListener('dragend', function(e) {
                if (e.target.classList.contains('task-card')) {
                    e.target.classList.remove('dragging');
                    draggedElement = null;
                    // Clear any remaining drag-over states
                    document.querySelectorAll('.drop-zone').forEach(zone => {
                        zone.classList.remove('drag-over');
                    });
                }
            });

            document.body.addEventListener('dragover', function(e) {
                if (e.target.closest('.drop-zone')) {
                    e.preventDefault();
                    e.target.closest('.drop-zone').classList.add('drag-over');
                }
            });

            document.body.addEventListener('dragleave', function(e) {
                if (e.target.classList.contains('drop-zone')) {
                    // Only remove if we're actually leaving the drop zone
                    if (!e.target.contains(e.relatedTarget)) {
                        e.target.classList.remove('drag-over');
                    }
                }
            });

            document.body.addEventListener('drop', function(e) {
                const dropZone = e.target.closest('.drop-zone');
                if (dropZone && draggedElement) {
                    e.preventDefault();
                    dropZone.classList.remove('drag-over');
                    
                    const taskId = draggedElement.dataset.taskId;
                    const newStatus = dropZone.dataset.status;
                    const currentStatus = draggedElement.dataset.taskStatus;
                    
                    if (currentStatus !== newStatus) {
                        // Use HTMX to move the task
                        htmx.ajax('POST', 'index.php', {
                            values: {
                                action: 'move',
                                task_id: taskId,
                                new_status: newStatus
                            },
                            target: '#task-board',
                            swap: 'outerHTML'
                        });
                    }
                }
            });
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Task Board</h1>
        
        <!-- Add Task Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form hx-post="index.php" hx-target="#task-board" hx-swap="outerHTML" hx-on::after-request="this.reset()" class="flex gap-4">
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