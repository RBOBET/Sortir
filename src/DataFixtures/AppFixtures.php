<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Repository\CampusRepository;
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
        //TODO appeler méthodes créées
    }

private function addParticipant(int $number, CampusRepository $campusRepository)
{
    for ($i = 0; $i < $number; $i++){
        $participant = new Participant();

        $participant
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
