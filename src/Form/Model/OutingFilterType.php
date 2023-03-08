<?php

namespace App\Form\Model;


use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus',

                'query_builder' => function (CampusRepository $campusRepository) {
                    $qb = $campusRepository->createQueryBuilder("c");
                    $qb->addOrderBy("c.name");
                    return $qb;
                }
            ])
            ->add('nameContains', TextType::class, [
                'label' => 'Le nom de la sortie contient'
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutingFilterModel::class,
        ]);
    }
}