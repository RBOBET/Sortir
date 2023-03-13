<?php

namespace App\Form\Model;


use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class OutingFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus',

            ])
            ->add('nameContains', TextType::class, [
                'label' => 'Le nom de la sortie contient',
                'empty_data' => ""
            ])
            ->add('startDate', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre'
            ])
            ->add('endDate', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'et'
            ])
            ->add('isPlanner', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice'
            ])
            ->add('isRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e'
            ])
            ->add('isNotRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e'
            ])
            ->add('outingIsPast', CheckboxType::class, [
                'label' => 'Sorties passées'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutingFilterModel::class,
            'required' => false
        ]);
    }
}