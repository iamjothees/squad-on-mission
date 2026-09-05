<?php

namespace App\Enums;

enum TagColor: string
{
    case GRAY = 'gray';
    case RED = 'red';
    case ORANGE = 'orange';
    case YELLOW = 'yellow';
    case GREEN = 'green';
    case TEAL = 'teal';
    case BLUE = 'blue';
    case INDIGO = 'indigo';
    case PURPLE = 'purple';
    case PINK = 'pink';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function bgClass(): string
    {
        return match($this) {
            self::GRAY => 'bg-gray-100 dark:bg-gray-800',
            self::RED => 'bg-red-100 dark:bg-red-900/30',
            self::ORANGE => 'bg-orange-100 dark:bg-orange-900/30',
            self::YELLOW => 'bg-yellow-100 dark:bg-yellow-900/30',
            self::GREEN => 'bg-green-100 dark:bg-green-900/30',
            self::TEAL => 'bg-teal-100 dark:bg-teal-900/30',
            self::BLUE => 'bg-blue-100 dark:bg-blue-900/30',
            self::INDIGO => 'bg-indigo-100 dark:bg-indigo-900/30',
            self::PURPLE => 'bg-purple-100 dark:bg-purple-900/30',
            self::PINK => 'bg-pink-100 dark:bg-pink-900/30',
        };
    }

    public function textClass(): string
    {
        return match($this) {
            self::GRAY => 'text-gray-700 dark:text-gray-300',
            self::RED => 'text-red-700 dark:text-red-400',
            self::ORANGE => 'text-orange-700 dark:text-orange-400',
            self::YELLOW => 'text-yellow-700 dark:text-yellow-400',
            self::GREEN => 'text-green-700 dark:text-green-400',
            self::TEAL => 'text-teal-700 dark:text-teal-400',
            self::BLUE => 'text-blue-700 dark:text-blue-400',
            self::INDIGO => 'text-indigo-700 dark:text-indigo-400',
            self::PURPLE => 'text-purple-700 dark:text-purple-400',
            self::PINK => 'text-pink-700 dark:text-pink-400',
        };
    }
}
