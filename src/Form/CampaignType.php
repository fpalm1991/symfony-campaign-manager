<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\Client;
use App\Entity\Platform;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('budget')
            ->add('start_date')
            ->add('end_date')
            ->add('platform', EntityType::class, [
                'class' => Platform::class,
                'choice_label' => 'name',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
            ])
            ->add('project_manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('campaign_owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
        ]);
    }
}
