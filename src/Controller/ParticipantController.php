<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/update', name: 'update')]
    public function update(Request $request, UserPasswordHasherInterface $passwordHasher, ParticipantRepository $participantRepository): Response
    {
        $participant = $this->getUser();
        $participantForm = $this->createForm(ParticipantType::class,$participant);

        $participantForm->handleRequest($request);

        dump("cucou");

            if ($participantForm->isSubmitted()&& $participantForm->isValid()){
                dump("cucou1");
             if (!$participantForm->get('plainPassword')->isEmpty()){
                 dump("cucou2");
                $password =  $passwordHasher->hashPassword($participant, $participantForm->get('plainPassword')->getData());
                $participant->setPassword($password);
             }

             $participantRepository->save($participant,true);
            $this->addFlash("success", "Modifications enregistrées !");
           // return  $this->redirectToRoute("outing_list");


        }



        return $this->render('participant/update.html.twig', [
            'participantForm'=>$participantForm->createView()
        ]);
    }
}
