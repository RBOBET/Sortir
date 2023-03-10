<?php

namespace App\Form;

use App\Controller\OutingController;
use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Outing;
use App\Entity\Place;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PlaceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Form\FormEvents;

class OutingType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class,[
                'label' => 'Nom de la sortie: ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom pour votre sortie',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de votre sortie est trop long',
                    ])
                ]
            ])

            ->add('dateTimeStart', DateTimeType::class, [
                'label' => 'Date et heure de la sortie: ',
                'html5' => true,
                'widget' => 'single_text'
            ])

            ->add('registrationLimitDate', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'html5' => true,
                'widget' => 'single_text'
            ])

            ->add('nbParticipantsMax',IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                    'max' => 32676,
                    ]
                ]
            )

            ->add('duration', IntegerType::class, [
                'label' => 'Dur??e : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une dur??e pour votre sortie',
                    ]),
                    new Length([
                        'max' => 2147483647,
                        'maxMessage' => '??a fait long l?? quand m??me^^',
                    ])
                ]

            ])

            ->add('overview', TextareaType::class, [
                'label' => 'Description et infos : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une dur??e pour votre sortie',
                    ]),
                    new Length([
                        'max' => 4294967296,
                        'maxMessage' => 'calm down bro(!)',
                    ])
                ]
            ])

            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville : ',
                'mapped' => false,
                'attr' => ['id' => 'outing_city'],
                'query_builder' => function(CityRepository $cityRepository) {
                    $qb = $cityRepository->createQueryBuilder("c");
                    $qb->addOrderBy("c.name");
                    return $qb;
                }
            ])


            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'label' => 'Lieu',
                'attr' => ['id' => 'outing_place'],
                'query_builder' => function(PlaceRepository $placeRepository){
                    $qb = $placeRepository->createQueryBuilder("p");
                    $qb->leftJoin ('p.city', 'c');
                    $qb->addSelect("c");
//                    $qb->addOrderBy('p.name');
                    return $qb;
                }
            ])
            ->add('registerPlanner', CheckboxType::class, [
                'mapped' => false,
                'label' => 'M\'inscrire ?? la sortie'
            ])

        ;
    }






////->add('plannerCampus', Entity::class, [
//'class' => Campus::class,
//'choice_label' => 'name',
//'label' => 'Campus',
//'attr' => 'disabled'
//])



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,

        ]);
    }
}
