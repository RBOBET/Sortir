<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Outing;
use App\Entity\Participant;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/outing', name: 'outing_')]
class OutingController extends AbstractController
{

    #[Route('/add', name: 'add')]
    public function add(
                        Request $request,
                        OutingRepository $outingRepository,
                        StatusRepository $statusRepository
    ): Response

    {
        $planner = $this->getUser();
        $outing = new Outing();
        $outing->setPlanner($planner);
        $outing->setPlannerCampus($planner->getCampus());

        $outingForm = $this->createForm(OutingType::class, $outing);


        $outingForm->handleRequest($request);

        if($outingForm->isSubmitted() && $outingForm->isValid()) {

            if ($outingForm->get('saveAndPublish')->isClicked()) {

                 $outing->setStatus($statusRepository->find(2));

                 $outingRepository->save($outing, true);

                //ajout flash parce qu'on est des BG qui font ça bien
                $this->addFlash("success", "Votre sortie est en ligne !");

            } else {

                $outing->setStatus($statusRepository->find(1));

                $outingRepository->save($outing, true);

                $this->addFlash("success",
                    "Sortie créée avec succès :-) n'oubliez pas de la publier ;-) ");

            }


            return $this->redirectToRoute("outing_show", ['id' => $outing->getOuting()->getId()]);
            //           dd($outing);
        }

        return $this->render('outing/add.html.twig', [
            'outingForm' => $outingForm->createView()
        ]);

    }


    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, OutingRepository $outingRepository){
        $outing = $outingRepository->find($id);

        if($outing){
            $outingRepository->remove($outing);
            $this->addFlash("warning","The outing has been deleted, this action cannot be undown" );
        } else {
            throw $this->createNotFoundException(("This outing cannot be deleted"));
        }

        return $this->redirectToRoute('outing_list');
    }

    #[Route('/list', name: 'list')]
    public function list(OutingRepository $outingRepository){
         $outings = $outingRepository->findAll();

        return $this->render('outing/list.html.twig', [
            'outings' => $outings
        ]);
    }

    #[Route('/show/{id}', name:'show')]
    public function show(int $id){

    }

//    public function listPlacesRelatedToCity(Request $request)
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $placeRepository = $entityManager->getRepository("App:Place");
//
//        $place = $placeRepository->createQueryBuilder("p")
//            ->where("p.city = cityId")
//            ->setParameter("cityId", $request->query->get("cityId"))
//            ->getQuery()
//            ->getResult();
//
//        $responseArray = array();
//
//        foreach ($place as $place) {
//            $responseArray[] = array(
//                "id" => $place->getId(),
//                "name" => $place->getName()
//            );
//        }
//
//        return new JsonResponse($responseArray);
//    }


}

