<?php

namespace App\Security\Voter;

use App\Entity\Campaign;
use App\Entity\User;
use App\Security\CampaignPermission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CampaignVoter extends Voter
{

    public const string EDIT = 'CAMPAIGN_EDIT';
    public const string DELETE = 'CAMPAIGN_DELETE';

    public function __construct(private readonly CampaignPermission $campaignPermission)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Campaign;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->campaignPermission->canEditCampaign($user, $subject),
            self::DELETE => $this->campaignPermission->canDeleteCampaign($user, $subject),
            default => false
        };
    }
}
