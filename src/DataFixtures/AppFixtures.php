<?php

namespace App\DataFixtures;

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

private function addParticipant (int $number)
{
    for ($i = 0; $i < $number; $i++){
        $Participant = new Participant();

        $Participant
            ->setLastName($this->faker->lastName)
            ->setFirstName($this->faker->firstName)



    }
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
