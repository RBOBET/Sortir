<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Participant;
use App\Entity\Place;
use App\Entity\Status;
use App\Repository\CampusRepository;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private CityRepository $cityRepository,
        private CampusRepository $campusRepository,
        private ParticipantRepository $participantRepository,
        private StatusRepository $statusRepository,
        private PlaceRepository $placeRepository,
        private ParameterBagInterface $parameterBag
    )
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->addCampus();
        $this->addStatus();
        $this->addCities(50);
        $this->addPlaces(50);
        $this->addParticipant(50);
        $this->addOuting(50);
    }

private function addParticipant(int $number)
{
    $admin = new Participant();
    $admin
        ->setEmail('admin@campus-eni.fr')
        ->setCampus($this->campusRepository->find(1))
        ->setPhone($this->faker->phoneNumber)
        ->setFirstName('Toto')
        ->setLastName('Ladmin')
        ->setPassword($this->passwordHasher->hashPassword($admin,'123456'))
        ->setRoles(['ROLE_ADMIN']);
    $this->entityManager->persist($admin);

    for ($i = 0; $i < $number; $i++){
        $participant = new Participant();

        $participant
            ->setLastName($this->faker->lastName)
            ->setFirstName($this->faker->firstName)
            ->setEmail($this->faker->email)
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($participant,'123456'))
            ->setPhone($this->faker->phoneNumber);

        $idCampus=$this->faker->numberBetween(1,7);
        $participant
            ->setCampus($this->campusRepository->find($idCampus));
        $this->entityManager->persist($participant);
    }
    $this->entityManager->flush();
}

    public function addOuting(int $number)
    {
        for( $i=0 ;$i < $number; $i++){

            $outing = new Outing();
            $idPlanner = $this->faker->numberBetween(min: 1,max: 50);
            $planner = $this->participantRepository->find($idPlanner);
            $status = $this->faker->numberBetween(min: 1, max: 6);
            $idPlace = $this->faker->numberBetween(min: 1, max: 50);


            $outing
                ->setPlanner($planner)
                ->setPlannerCampus($planner->getCampus())
                ->setStatus($this->statusRepository->find($status))
                ->setPlace($this->placeRepository->find($idPlace))
                ->setTitle($this->faker->word)
                ->setDateTimeStart($this->faker->dateTimeBetween('now', '+1 year'))
                ->setDuration($this->faker->numberBetween(min: 1, max: 2880))
                ->setRegistrationLimitDate($this->faker->dateTimeBetween('-1 year', $outing->getDateTimeStart()))
                ->setNbParticipantsMax($this->faker->numberBetween(min: 0, max: 50))
                ->setOverview(implode(" ", $this->faker->words(5)))
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

    public function addPlaces(int $number){
        for ($i=0; $i<$number; $i++){
            $place = new Place();
            $place
                ->setName($this->faker->word)
                ->setStreet($this->faker->streetName);
            $nb = $this->faker->numberBetween(1, 50);
            $place
                ->setCity($this->cityRepository->find($nb));
            $this->entityManager->persist($place);
        }
        $this->entityManager->flush();
    }

    public function addCampus(){
        $campus1 = new Campus();
        $campus1->setName('Saint-Herblain');
        $this->entityManager->persist($campus1);

        $campus2 = new Campus();
        $campus2->setName('Chartres-De-Bretagne');
        $this->entityManager->persist($campus2);

        $campus3 = new Campus();
        $campus3->setName('Quimper');
        $this->entityManager->persist($campus3);

        $campus4 = new Campus();
        $campus4->setName('Niort');
        $this->entityManager->persist($campus4);

        $campus5 = new Campus();
        $campus5->setName('Saint-Nazaire');
        $this->entityManager->persist($campus5);

        $campus6 = new Campus();
        $campus6->setName('La-Roche-Sur-Yon');
        $this->entityManager->persist($campus6);

        $campus7 = new Campus();
        $campus7->setName('Angers');
        $this->entityManager->persist($campus7);

        $this->entityManager->flush();
    }

    public function addStatus(){
        //je récupère le tableau de status dans services.yaml
        $statusCodes = $this->parameterBag->get('status_codes');
        foreach ($statusCodes as $status){
            $st = new Status();
            $st->setLabel($status);
            $this->entityManager->persist($st);
        }

        $this->entityManager->flush();
    }
}
