<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/update', name: 'update')]
    public function update(): Response
    {
        return $this->render('participant/update.html.twig', [
            'participant'
        ]);
    }
}
