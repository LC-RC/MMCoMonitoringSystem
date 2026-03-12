<?php
/**
 * Theme Helper - Dynamic Department-Based UI Theming
 * MM&Co Accounting Review Center Management System
 */

namespace App\Core;

class ThemeHelper
{
    /** @var array Department theme configurations */
    private static array $themes = [
        'admin' => [
            'primary' => '#1E3A8A',
            'primary_light' => '#3B82F6',
            'accent' => '#FACC15',
            'accent_light' => '#FDE68A',
            'background' => '#FFFFFF',
            'sidebar_bg' => '#1E3A8A',
            'sidebar_text' => '#FFFFFF',
        ],
        'IT' => [
            'primary' => '#800020',
            'primary_light' => '#A52A2A',
            'accent' => '#FFFFFF',
            'accent_light' => '#F5F5F5',
            'background' => '#FFFFFF',
            'sidebar_bg' => '#800020',
            'sidebar_text' => '#FFFFFF',
        ],
        'Accounting' => [
            'primary' => '#CA8A04',
            'primary_light' => '#EAB308',
            'accent' => '#FFFFFF',
            'accent_light' => '#FEFCE8',
            'background' => '#FFFFFF',
            'sidebar_bg' => '#CA8A04',
            'sidebar_text' => '#FFFFFF',
        ],
        'HR' => [
            'primary' => '#1E3A8A',
            'primary_light' => '#3B82F6',
            'accent' => '#60A5FA',
            'accent_light' => '#93C5FD',
            'background' => '#FFFFFF',
            'sidebar_bg' => '#1E40AF',
            'sidebar_text' => '#FFFFFF',
        ],
    ];

    /**
     * Get theme config for current user
     */
    public static function getTheme(?object $user): array
    {
        if (!$user) {
            return self::$themes['admin'];
        }
        if ($user->role === 'admin') {
            return self::$themes['admin'];
        }
        return self::$themes[$user->department] ?? self::$themes['admin'];
    }

    /**
     * Get OJT badge color by department
     */
    public static function getOjtBadgeColor(?string $department): string
    {
        return match ($department) {
            'IT' => 'bg-blue-600',
            'Accounting' => 'bg-amber-500',
            default => 'bg-blue-600',
        };
    }

}
