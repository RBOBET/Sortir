<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/update', name: 'update')]
    public function update(Request $request, UserPasswordHasherInterface $passwordHasher,
                           ParticipantRepository $participantRepository,
                            Uploader $uploader): Response
    {
        /**
         * @var Participant $participant
         */
        $participant = $this->getUser();
        $participantForm = $this->createForm(ParticipantType::class,$participant);

        $participantForm->handleRequest($request);



            if ($participantForm->isSubmitted()&& $participantForm->isValid()){
             if (!$participantForm->get('plainPassword')->isEmpty()){
                $password =  $passwordHasher->hashPassword($participant, $participantForm->get('plainPassword')->getData());
                $participant->setPassword($password);
             }

                /**
                 * @var UploadedFile $file
                 */
                $file=$participantForm->get('photo')->getData();
                if ($file){
                    $newFileName = $uploader->upload(
                        $file,
                        $this->getParameter('upload_photo'),
                        $participant->getLastName()
                    );
                    $participant->setPhoto($newFileName);
                }





             $participantRepository->save($participant,true);
            $this->addFlash("success", "Modifications enregistrées !");
           // return  $this->redirectToRoute("outing_list");


        }


        return $this->render('participant/update.html.twig', [
            'participantForm'=>$participantForm->createView(),
            'participant'=>$participant
        ]);
    }
        #[Route('/show/{id}', name: 'show')]
    public function show (int $id, ParticipantRepository $participantRepository): Response
        {
            $participant=$participantRepository->find($id);

            return $this->render('participant/show.html.twig',[
                'participant'=>$participant]);
        }
}
