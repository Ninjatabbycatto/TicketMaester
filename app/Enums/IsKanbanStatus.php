<?php

namespace App\Enums;

trait IsKanbanStatus
{
    /**
     * Get all enum cases as an array of values.
     *
     * @return array<string>
     */
    public static function statuses(): array
    {
        return collect(self::cases())->map(fn($case) => $case->value);
    }

    /**
     * Get a human-readable label for the enum case.
     *
     * @return string
     */
    public function label(): string
    {
        // Default: convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $this->value));
    }
}
