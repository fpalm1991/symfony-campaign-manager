<?php

namespace App\Enum;

enum CampaignLifecycle: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
