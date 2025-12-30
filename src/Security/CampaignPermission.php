<?php

namespace App\Security;

use App\Entity\Campaign;
use App\Entity\User;

final class CampaignPermission
{

    public function canEditCampaign(User $user, Campaign $campaign): bool
    {
        // Only project manager and campaign owner can edit campaign
        $isCampaignOwner = $campaign->getCampaignOwner()?->getId() === $user->getId();
        $isProjectManager = $campaign->getProjectManager()?->getId() === $user->getId();

        return $isCampaignOwner || $isProjectManager;
    }

    public function canDeleteCampaign(User $user, Campaign $campaign): bool
    {
        return $this->canEditCampaign($user, $campaign);
    }
}
