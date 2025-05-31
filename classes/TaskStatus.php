<?php

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function getLabel(): string
    {
        return match($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::TODO => 'bg-gray-200 text-gray-800',
            self::IN_PROGRESS => 'bg-blue-200 text-blue-800',
            self::DONE => 'bg-green-200 text-green-800',
        };
    }

    public function getColumnColor(): string
    {
        return match($this) {
            self::TODO => 'bg-gray-50 border-gray-200',
            self::IN_PROGRESS => 'bg-blue-50 border-blue-200',
            self::DONE => 'bg-green-50 border-green-200',
        };
    }

    public static function getAll(): array
    {
        return [
            self::TODO,
            self::IN_PROGRESS,
            self::DONE,
        ];
    }
}