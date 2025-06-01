<?php

class Task
{
    public function __construct(
        public string $title,
        public TaskStatus $status = TaskStatus::TODO,
        public string $id = '',
        public DateTime $createdAt = new DateTime(),
        public ?DateTime $updatedAt = null
    ) {
        if (empty($this->id)) {
            $this->id = uniqid('task_', true);
        }
    }

    public function withStatus(TaskStatus $newStatus): self
    {
        return new self(
            title: $this->title,
            status: $newStatus,
            id: $this->id,
            createdAt: $this->createdAt,
            updatedAt: new DateTime()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status->value,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): self
    {
        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $data['created_at']) ?: new DateTime();
        $updatedAt = isset($data['updated_at']) ? 
            DateTime::createFromFormat('Y-m-d H:i:s', $data['updated_at']) : null;

        return new self(
            title: $data['title'],
            status: TaskStatus::from($data['status']),
            id: $data['id'],
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    public function getTimeAgo(): string
    {
        $now = new DateTime();
        $diff = $now->diff($this->createdAt);
        
        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }
}