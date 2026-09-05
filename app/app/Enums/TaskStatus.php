<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case REVIEW = 'review';
    case DONE = 'done';
    
    public function label(): string
    {
        return match($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::REVIEW => 'Review',
            self::DONE => 'Done',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::TODO => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            self::IN_PROGRESS => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            self::REVIEW => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            self::DONE => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        };
    }
}
