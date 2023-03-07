<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Repository\CityRepository;
use App\Entity\Outing;
use App\Repository\ParticipantRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
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

private function addParticipant (int $number)
{
    for ($i = 0; $i < $number; $i++){
        $Participant = new Participant();

        $Participant
            ->setLastName($this->faker->lastName)
            ->setFirstName($this->faker->firstName)
            ->setEmail($this->faker->email)
            ->setRoles('ROLE_USER')
            ->setPassword($this->passwordHasher->hashPassword($participant,'123456'))
            ->setPhone($this->faker->phoneNumber);

        $number=$this->faker->numberBetween(1,7);
        $participant
            ->setCampus($campusRepository->find($number));
        $this->entityManager->persist($participant);
    }
    $this->entityManager->flush();
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
    public function addCities(int $number){
        for ($i=0 ; $i<$number ; $i++){
            $city = new City();
            $city
                ->setName($this->faker->city)
                ->setPostalCode($this->faker->postcode);
            $this->entityManager->persist($city);
        }
        $this->entityManager->flush();
    }

    public function addPlaces(int $number, CityRepository $cityRepository){
        for ($i=0; $i<$number; $i++){
            $place = new Place();
            $place
                ->setName($this->faker->word)
                ->setStreet($this->faker->streetName);
            $nb = $this->faker->numberBetween(1, 50);
            $place
                ->setCity($cityRepository->find($nb));
            $this->entityManager->persist($place);
        }
        $this->entityManager->flush();
    }
}
