<?php

namespace App\DataFixtures;

use App\Entity\Outing;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {

        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->addOuting(50);
    }

    public function addOuting(
        int $number,
        ParticipantRepository $participantRepository,
        StatusRepository $statusRepository,
        PlaceRepository $placeRepository
    )
    {
        for( $i=0 ;$i < $number; $i++){

            $outing = new Outing();
            $idPlanner = $this->faker->numberBetween(min: 1,max: 50);
            $status = $this->faker->numberBetween(min: 1, max: 6);
            $place = $this->faker->city;


            $outing
                ->setPlanner($participantRepository->find($idPlanner))
                ->setPlannerCampus($participantRepository->find($idPlanner)->getCampus())
                ->setStatus($statusRepository->find($status))
                ->setPlace($placeRepository->find($place))
                ->setTitle($this->faker->title)
                ->setDateTimeStart($this->faker->dateTimeBetween('now', '+1 year'))
                ->setDuration($this->faker->numberBetween(min: 1, max: 2880))
                ->setRegistrationLimitDate($this->faker->dateTimeBetween('-1 year', $outing->getDateTimeStart()))
                ->setNbParticipantsMax($this->faker->numberBetween(min: 0, max: 50))
                ->setOverview($this->faker->sentence($nbWords = 6, $variableNbWords = true))
            ;

            $this->entityManager->persist($outing);
        }

        $this->entityManager->flush();
    }

}
