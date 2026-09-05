<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
    
    public function label(): string
    {
        return match($this) {
            self::PLANNING => 'Planning',
            self::ACTIVE => 'Active',
            self::ON_HOLD => 'On Hold',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::PLANNING => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            self::ACTIVE => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            self::ON_HOLD => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            self::COMPLETED => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            self::ARCHIVED => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        };
    }
}
