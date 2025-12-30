<?php

namespace App\Enum;

enum CampaignLifecycle: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-success',
            self::ARCHIVED => 'bg-secondary',
        };
    }

    public function asValue(): string
    {
        return match ($this) {
            self::ACTIVE => '1',
            self::ARCHIVED => '0',
        };
    }

    public function asLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktiv',
            self::ARCHIVED => 'Archiviert',
        };
    }
}
