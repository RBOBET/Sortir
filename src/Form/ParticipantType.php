<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\ORM\Mapping\Entity;

use phpDocumentor\Reflection\PseudoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',TextType::class,[
                'label'=> 'Pseudo :',
                'required'=>false,
                'constraints' => [
                    new Length([
                        'min'=>4,
                        'max'=>20,
                        'maxMessage' => 'Vous avez depassé la limite de {{ limit }} caractères',
                    ])
                ]

            ])

            ->add('firstName',TextType::class,[
                'label' => 'Prénom :'])

            ->add('lastName', TextType::class,[
                'label'=>'Nom :'
            ])

            ->add('phone',TextType::class,[
                'label'=>'Téléphone',
                'html5' => true,
                'constraints'=>[
                    new Regex('^0[1-79]\.?\d{2}\.?\d{2}\.?\d{2}\.?\d{2}$', message:'Le numéro de téléphone n\'est pas au bon format' )
                //autorise un numéro aux formats 06.05.04.03.02 et 0605040302
                ]
            ])

            ->add('email', EmailType::class,[
            'label'=>'Email'])

            ->add('plainPassword', RepeatedType::class, [

                'type'=>PasswordType::class,
                'required'=>false,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => [
                    'label' => 'Mot de passe',
            ],
                'second_options' => [
                    'label' => 'Confirmation',
                    ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller

            ])

            ->add('campus',EntityType::class, [
                'class'=>Campus::class,
                'choice_label'=> 'name',
                'label'=>'Campus',
                'query_builder'=> function(CampusRepository $campusRepository){
                $qb = $campusRepository->createQueryBuilder("campus");
                $qb->addOrderBy("campus.name");
                return $qb;
                }
            ])


            ->add('photo',FileType::class,[
                'label'=>'Ma photo',
                'required'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
