<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Place;
use App\Repository\CityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewPlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du lieu : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom pour votre lieu',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de votre lieu est trop long',
                    ])
                ]
            ])

            ->add('street', TextType::class, [
                'label' => 'Nom de la rue : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom pour votre rue',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de votre rue est trop long',
                    ])
                ]
            ])

            ->add('latitude', TextType::class, [
                'label' => 'Latitude : '
            ])

            ->add('longitude', TextType::class, [
                'label' => 'Longitude : '
            ])

            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville : ',
                'query_builder' => function(CityRepository $cityRepository) {
                    $qb = $cityRepository->createQueryBuilder("c");
                    $qb->addOrderBy("c.name");
                    return $qb;
                }
            ])

            ->add('create', SubmitType::class, [
                'label' => 'Ajouter'])

            ->add('cancel', SubmitType::class, [
                'label' => 'Annuler'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
