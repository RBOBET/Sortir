<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/update', name: 'update')]
    public function update(ParticipantRepository $participantRepository): Response
    {
        $participant = $this->getUser();
        $participantFrom = $this->createForm(ParticipantType::class,$participant);

        return $this->render('participant/update.html.twig', [
            'participantForm'=>$participantFrom->createView()
        ]);
    }
}
