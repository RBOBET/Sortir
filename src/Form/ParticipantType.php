<?php

namespace App\Form;

use App\Entity\Participant;
use phpDocumentor\Reflection\PseudoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('lastName')
            ->add('firstName')
            ->add('phone')
            ->add('isEnabled')
            ->add('photo')
            ->add('outings')
            ->add('campus')
            ->add('pseudo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
