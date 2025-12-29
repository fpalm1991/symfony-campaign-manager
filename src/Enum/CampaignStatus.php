<?php

namespace App\Enum;

enum CampaignStatus: string
{
    case DRAFT = 'Entwurf';
    case PLANNED = 'Eingeplant';
    case RUNNING = 'Kampagne läuft';
    case ENDING_SOON = 'Kampagne endet bald';
    case ENDED = 'Kampagne beendet';

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary',
            self::PLANNED => 'bg-info',
            self::RUNNING => 'bg-success',
            self::ENDING_SOON => 'bg-warning text-dark',
            self::ENDED => 'bg-dark',
        };
    }
}
