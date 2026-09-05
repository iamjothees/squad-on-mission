<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
    
    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::LOW => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            self::MEDIUM => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            self::HIGH => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            self::URGENT => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        };
    }
}
