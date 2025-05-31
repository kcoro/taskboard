<?php

class TaskBoard
{
    private array $tasks = [];

    public function __construct(array $tasksData = [])
    {
        foreach ($tasksData as $taskData) {
            $this->tasks[] = Task::fromArray($taskData);
        }
    }

    public function addTask(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function deleteTask(string $taskId): void
    {
        $this->tasks = array_filter(
            $this->tasks,
            fn(Task $task) => $task->id !== $taskId
        );
    }

    public function moveTask(string $taskId, TaskStatus $newStatus): void
    {
        foreach ($this->tasks as $index => $task) {
            if ($task->id === $taskId) {
                $this->tasks[$index] = $task->withStatus($newStatus);
                break;
            }
        }
    }

    public function getTasksByStatus(TaskStatus $status): array
    {
        return array_filter(
            $this->tasks,
            fn(Task $task) => $task->status === $status
        );
    }

    public function getTasks(): array
    {
        return array_map(fn(Task $task) => $task->toArray(), $this->tasks);
    }

    public function getTaskCount(): int
    {
        return count($this->tasks);
    }

    public function getTaskCountByStatus(TaskStatus $status): int
    {
        return count($this->getTasksByStatus($status));
    }
}