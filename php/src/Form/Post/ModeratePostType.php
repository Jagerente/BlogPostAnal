<?php

namespace App\Form\Post;

use App\Entity\Post;
use App\Enum\PostStatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeratePostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Pending' => PostStatusEnum::Pending,
                    'Declined' => PostStatusEnum::Declined,
                    'Approved' => PostStatusEnum::Approved,
                ],
                'required' => true,
            ])
            ->add('moderator_note', TextareaType::class, [
                'label' => 'Moderator Note',
                'required' => true,
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}