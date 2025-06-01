<div id="task-board" class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach (TaskStatus::getAll() as $status): ?>
        <div class="<?= $status->getColumnColor() ?> rounded-lg border-2 border-dashed p-4 min-h-96 drop-zone" 
             data-status="<?= $status->value ?>">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-700"><?= $status->getLabel() ?></h2>
                <span class="<?= $status->getColor() ?> px-3 py-1 rounded-full text-sm font-medium">
                    <?= $taskBoard->getTaskCountByStatus($status) ?>
                </span>
            </div>
            
            <div class="space-y-3">
                <?php foreach ($taskBoard->getTasksByStatus($status) as $task): ?>
                    <div class="task-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-move" 
                         draggable="true" 
                         data-task-id="<?= $task->id ?>" 
                         data-task-status="<?= $task->status->value ?>">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 mb-2"><?= htmlspecialchars($task->title) ?></h3>
                                <p class="text-xs text-gray-500"><?= $task->getTimeAgo() ?></p>
                            </div>
                            <div class="flex items-center space-x-2 ml-2">
                                <!-- Move buttons -->
                                <?php if ($task->status !== TaskStatus::TODO): ?>
                                    <button 
                                        hx-post="index.php" 
                                        hx-target="#task-board" 
                                        hx-swap="outerHTML"
                                        hx-vals='{"action": "move", "task_id": "<?= $task->id ?>", "new_status": "<?= TaskStatus::TODO->value ?>"}'
                                        class="text-gray-400 hover:text-gray-600 text-sm font-medium"
                                        title="Move to To Do"
                                    >
                                        ←
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($task->status === TaskStatus::TODO): ?>
                                    <button 
                                        hx-post="index.php" 
                                        hx-target="#task-board" 
                                        hx-swap="outerHTML"
                                        hx-vals='{"action": "move", "task_id": "<?= $task->id ?>", "new_status": "<?= TaskStatus::IN_PROGRESS->value ?>"}'
                                        class="text-blue-400 hover:text-blue-600 text-sm font-medium"
                                        title="Move to In Progress"
                                    >
                                        →
                                    </button>
                                <?php elseif ($task->status === TaskStatus::IN_PROGRESS): ?>
                                    <button 
                                        hx-post="index.php" 
                                        hx-target="#task-board" 
                                        hx-swap="outerHTML"
                                        hx-vals='{"action": "move", "task_id": "<?= $task->id ?>", "new_status": "<?= TaskStatus::DONE->value ?>"}'
                                        class="text-green-400 hover:text-green-600 text-sm font-medium"
                                        title="Move to Done"
                                    >
                                        →
                                    </button>
                                <?php endif; ?>
                                
                                <!-- Delete button -->
                                <button 
                                    hx-post="index.php" 
                                    hx-target="#task-board" 
                                    hx-swap="outerHTML"
                                    hx-vals='{"action": "delete", "task_id": "<?= $task->id ?>"}'
                                    hx-confirm="Are you sure you want to delete this task?"
                                    class="text-red-400 hover:text-red-600 text-sm"
                                    title="Delete task"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                        
                        <!-- Status badge -->
                        <div class="mt-3">
                            <span class="<?= $task->status->getColor() ?> px-2 py-1 rounded-full text-xs font-medium">
                                <?= $task->status->getLabel() ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($taskBoard->getTasksByStatus($status))): ?>
                    <div class="text-center text-gray-400 py-8">
                        <div class="text-4xl mb-2">📝</div>
                        <p class="text-sm">No tasks yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>