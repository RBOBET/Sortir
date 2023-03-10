<?php

namespace App\Service;

use App\Controller\OutingController;
use App\Entity\Outing;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OutingsStatus
{
    public function __construct(private OutingRepository $outingRepository,
                                 private StatusRepository $statusRepository,
                                private EntityManagerInterface $entityManager,
                                private ParameterBagInterface $parameterBag)
    {
    }

    public function updateStatus(){
        //on récupère la liste des statuts depuis services.yaml, id = index+1
        $status = $this->parameterBag->get('status_codes');
        $outings = $this->outingRepository->findAllOutings();
        $now = new \DateTime();
        /**
         * @var Outing $out
         */
        foreach ($outings as $out){

            /**
             * @var \DateTime $dateStart
             */
            //je calcule la date de fin de la sortie = date début + durée
            $dateStart = $out->getDateTimeStart();
            $minutesToAdd = $out->getDuration();
            $dateEnd = $dateStart->modify("+{$minutesToAdd} minutes");
            $dateArchived = $dateEnd->modify('+ 1 month');
            //si la sortie n'est pas créée ni annulée
            if ($out->getStatus()->getId() != $status[0] && $out->getStatus()->getId() != $status[6]){
                //si la date max d'inscription est après now
                if ($out->getRegistrationLimitDate() > $now){
                    //je sette le statut à ouvert
                    $out->setStatus($this->statusRepository->find(2));
                } else {
                    //sinon je sette à fermée
                    $out->setStatus($this->statusRepository->find(3));
                }

                if ($now > $dateStart && $now < $dateEnd){
                    //je sette la sortie en cours ongoing
                    $out->setStatus($this->statusRepository->find(4));
                }

                //si la date de fin de sortie est dépassée
                if ($now >= $dateEnd){
                    //je sette la sortie à finie
                    $out->setStatus($this->statusRepository->find(5));
                }
            }
            //si la date d'archivage est dépassée
            if ($now > $dateArchived){
                //je sette le statut à archivé
                $out->setStatus($this->statusRepository->find(7));
            }
            $this->entityManager->persist($out);
        }
        $this->entityManager->flush();
    }



}